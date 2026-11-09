// language
const language = {};
language['de'] = [' seit ', 'Query ist auf diesem Server deaktiviert.', 'Derzeit befinden sich keine Spieler auf diesem Server.'];
language['en'] = [' since ', 'Query is disabled on this server.', 'There are currently no players on this server.'];
let lang = navigator.language.substring(0, 2);
if (!language[lang]) {
    lang = 'en';
}
const NBSP = " ";
// Games with a player list (name + play time) and with system/password/map details
const PLAYER_TYPES = ["arkse", "rust", "vrising", "csgo"];
const DETAIL_TYPES = ["arkse", "rust", "vrising", "csgo", "valheim", "dayz"];

// Everything from reload.php comes from the queried game server and must be treated as untrusted.
function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
}
// Allow only http(s), steam:// and relative URLs in href/src attributes.
function safeUrl(value) {
    let url = String(value ?? '').trim();
    if (/^(https?:|steam:)/i.test(url) || !/^[a-z][a-z0-9+.-]*:/i.test(url)) {
        return url;
    }
    return '#';
}

// DOM helpers for one server section. Missing elements are skipped.
function serverView(root) {
    const el = (cls, tag) => {
        let node = root.getElementsByClassName(cls)[0];
        if (node && tag) {
            node = node.getElementsByTagName(tag)[0];
        }
        return node || null;
    };
    return {
        el: el,
        text(cls, value, tag) {
            const node = el(cls, tag);
            if (node) node.textContent = value ?? "";
        },
        // Detail values are shown with a leading non-breaking space
        detail(cls, value) {
            const node = el(cls);
            if (node) node.textContent = NBSP + (value ?? "");
        },
        html(cls, value, tag) {
            const node = el(cls, tag);
            if (node) node.innerHTML = value;
        },
        src(cls, url, tag) {
            const node = el(cls, tag);
            if (node) node.src = safeUrl(url);
        },
        href(cls, url, tag) {
            const node = el(cls, tag);
            if (node) node.href = safeUrl(url);
        },
        hide(cls) {
            const node = el(cls);
            if (node) node.style.display = "none";
        },
    };
}

function renderMods(view, mods) {
    let html = "";
    for (const value in mods) {
        html += "<a href='" + esc(safeUrl(mods[value]['ModLink'])) + "' target='_blank' rel='noopener noreferrer'>" + esc(mods[value]['ModName']) + "</a><br>";
    }
    view.html("mods", html);
}

function renderDescription(view, description) {
    // Rust sends the description in parts and with literal "\n" line breaks
    let html = "";
    for (const value in description) {
        html += esc(description[value]).replace(/\\n|\n/g, "<br>");
    }
    view.html("description", html);
}

function renderMinecraftPlayers(view, data) {
    if (data.QueryPort == 0) {
        view.html("II", "<div style=\"margin:auto\">" + language[lang][1] + "</div>");
    } else if (data.Players !== 0) {
        let html = "";
        const players = data.raw.Players;
        for (const value in players) {
            html += "<div class='mchead'><img src='" + esc(safeUrl(players[value]['Skin'])) + "' alt='Skin from crafatar'><div class='name'>" + esc(players[value]['Name']) + "</div></div>";
        }
        view.html("II", "<div class='mcheads'>" + html + "</div>");
    } else {
        view.html("II", "<div style=\"margin:auto\">" + language[lang][2] + "</div>");
    }
    view.hide("I");
}

function renderCsgoRules(root, rules) {
    let tbody = root.getElementsByTagName("tbody")[1];
    if (!tbody) return;
    let html = "";
    for (const value in rules) {
        html += "<tr><td class='name'>" + esc(rules[value][1]) + "</td><td class='value'>" + esc(rules[value][2]) + "</td></tr>";
    }
    tbody.innerHTML = html;
}

function renderPlayers(view, players) {
    const list = view.el("IV");
    const scroll = list ? list.getElementsByClassName("scroll")[0] : null;
    if (!scroll) return;
    let html = "";
    for (const val in players) {
        html += "<h5 class=\"dark\">" + esc(players[val]['Name']) + language[lang][0] + esc(players[val]['Time']) + "</h5><br>";
    }
    scroll.innerHTML = html;
}

function render(id, data) {
    const root = document.getElementById("server_" + id);
    if (!root) return;
    const view = serverView(root);
    const raw = data.raw;
    const type = raw.Type;
    const online = data.Status === 1;

    // Common header
    view.el("status_icon_onl").style.backgroundColor = online ? "#00FF17" : "#E20401";
    view.text("status-letter-online", online ? "ONLINE" : "OFFLINE");
    view.src("img-cell", raw.Logo, "img");
    for (const cls of ["connectlink_cell", "connectlink"]) {
        view.text(cls, data.IP + ":" + data.GamePort, "a");
        view.href(cls, raw.ConnectLink, "a");
    }
    view.text("players_numeric", data.Players + "/" + data.MaxPlayers);
    view.text("players", data.Players + "/" + data.MaxPlayers, "p");
    view.text("servername_nolink", data.Name);
    view.text("servername", data.Name, "p");
    if (raw.MapLink) {
        view.src("map", raw.MapLink);
    }

    // Game specific
    switch (type) {
        case "arkse":
            view.detail("battleye", raw.Battleye);
            view.detail("clusterid", raw.ClusterID);
            view.detail("pvp", raw.PVP);
            view.detail("ingameday", raw.InGameDay);
            renderMods(view, raw.Mods);
            break;
        case "minecraft":
            // MOTD is HTML generated (and escaped) on the server by MinecraftColors::convertToHTML
            view.html("servername", raw.MOTD, "p");
            renderMinecraftPlayers(view, data);
            break;
        case "rust":
            view.el("I").getElementsByTagName("a")[0].href = "https://rustmaps.com/map/" + encodeURIComponent(raw.WorldSize) + "_" + encodeURIComponent(raw.Seed) + "?embed=img_i_l";
            view.detail("rustuptime", raw.RustUptime);
            view.href("website", raw.Website);
            view.text("website", raw.Website);
            view.detail("pvp", raw.PVP);
            view.detail("fps", raw.FPS);
            view.text("tags", raw.Tags);
            renderDescription(view, raw.Description);
            break;
        case "vrising":
            view.detail("ingameday", raw.InGameDay);
            view.detail("bloodbound", raw.Bloodbound);
            view.text("tags", raw.Tags);
            view.text("description", raw.Description);
            break;
        case "csgo":
            view.detail("sversion", raw.Version);
            if (raw.HasRules) {
                renderCsgoRules(root, raw.Rules);
            }
            break;
        case "valheim":
            view.detail("maxplayers", data.MaxPlayers);
            view.detail("queryport", data.QueryPort);
            break;
        case "dayz":
            view.detail("sversion", raw.Version);
            view.text("tags", raw.Tags);
            break;
    }

    if (PLAYER_TYPES.includes(type)) {
        renderPlayers(view, raw.Players);
    }
    if (DETAIL_TYPES.includes(type)) {
        view.detail("system", raw.OS);
        if (type !== "valheim") {
            view.detail("password", raw.Password);
        }
        view.detail("mapname", raw.Map);
    }
}

function LoadData(id) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            render(id, JSON.parse(this.responseText));
        }
    };
    xhttp.open("GET", "reload.php?id=" + encodeURIComponent(id), true);
    xhttp.send();
}
setInterval(refresh, 30000);
function refresh() {
    if(document.hasFocus()) {
        callLoadData();
    } else {
        console.log('refresh paused');
        clearInterval(downloadTimer);
        countdown(30);
    }
}
function countdown(time) {
    downloadTimer = setInterval(function(){
        time--;
        if(time <= 0)
            clearInterval(downloadTimer);
        document.getElementById("countdown").textContent = time;
    },1000);
}
