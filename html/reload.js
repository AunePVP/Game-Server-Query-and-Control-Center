// language
const language = {};
language['de'] = [' seit ', 'Query ist auf diesem Server deaktiviert.', 'Derzeit befinden sich keine Spieler auf diesem Server.'];
language['en'] = [' since ', 'Query is disabled on this server.', 'There are currently no players on this server.'];
let lang = navigator.language.substring(0, 2);
function LoadData(id) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let data = JSON.parse(this.responseText);
            let type = data.raw.Type;
            // insert data into html
            let serverid = document.getElementById("server_"+id);
            if (data.Status === 1) {
                serverid.getElementsByClassName("status_icon_onl")[0].style.backgroundColor = "#00FF17";
                serverid.getElementsByClassName("status-letter-online")[0].innerHTML = "ONLINE";
            } else {
                serverid.getElementsByClassName("status_icon_onl")[0].style.backgroundColor = "#E20401";
                serverid.getElementsByClassName("status-letter-online")[0].innerHTML = "OFFLINE";
            }
            serverid.getElementsByClassName("img-cell")[0].getElementsByTagName("img")[0].src = data.raw.Logo;
            serverid.getElementsByClassName("connectlink_cell")[0].getElementsByTagName("a")[0].innerHTML = data.IP+":"+data.GamePort;
            serverid.getElementsByClassName("connectlink")[0].getElementsByTagName("a")[0].innerHTML = data.IP+":"+data.GamePort;
            serverid.getElementsByClassName("connectlink_cell")[0].getElementsByTagName("a")[0].href = data.raw.ConnectLink;
            serverid.getElementsByClassName("connectlink")[0].getElementsByTagName("a")[0].href = data.raw.ConnectLink;
            serverid.getElementsByClassName("players_numeric")[0].innerHTML = data.Players+"/"+data.MaxPlayers;
            serverid.getElementsByClassName("players")[0].getElementsByTagName("p")[0].innerHTML = data.Players+"/"+data.MaxPlayers;
            serverid.getElementsByClassName("servername_nolink")[0].innerHTML = data.Name;
            serverid.getElementsByClassName("servername")[0].getElementsByTagName("p")[0].innerHTML = data.Name;
            if (type === "arkse") {
                let mods = data.raw.Mods;
                serverid.getElementsByClassName("battleye")[0].innerHTML = "&nbsp;"+data.raw.Battleye;
                serverid.getElementsByClassName("clusterid")[0].innerHTML = "&nbsp;"+data.raw.ClusterID;
                serverid.getElementsByClassName("mods")[0].innerHTML = "";
                serverid.getElementsByClassName("map")[0].src = data.raw.MapLink;
                for (const value in mods) {
                    let modlink = `${mods[value]['ModLink']}`;
                    let modname = `${mods[value]['ModName']}`;
                    let finalmod = "<a href='"+modlink+"' target='_blank'>"+modname+"</a><br>";
                    serverid.getElementsByClassName("mods")[0].innerHTML += finalmod;
                }
                serverid.getElementsByClassName("pvp")[0].innerHTML = "&nbsp;"+data.raw.PVP;
                serverid.getElementsByClassName("ingameday")[0].innerHTML = "&nbsp;"+data.raw.InGameDay;
                serverid.getElementsByClassName("img-cell")[0].getElementsByTagName("img")[0].src = data.raw.Logo;
            } else if (type === "minecraft") {
                let mods = data.raw.Mods;
                for (const value in mods) {
                    let modlink = `${mods[value]['ModLink']}`;
                    let modname = `${mods[value]['ModName']}`;
                    let finalmod = "<a href='"+modlink+"' target='_blank'>"+modname+"</a><br>";
                    serverid.getElementsByClassName("mods")[0].innerHTML += finalmod;
                }
                serverid.getElementsByClassName("servername")[0].getElementsByTagName("p")[0].innerHTML = data.raw.MOTD;
                if (data.QueryPort == 0){
                    serverid.getElementsByClassName("II")[0].innerHTML = "<div style=\"margin:auto\">"+language[lang][1]+"</div>";
                    serverid.getElementsByClassName("I")[0].style.display = "none";
                } else {
                    let players = data.raw.Players;
                    if (data.Players !== 0) {
                        serverid.getElementsByClassName("II")[0].innerHTML = "<div class='mcheads'></div>";
                        serverid.getElementsByClassName("mcheads")[0].innerHTML = "";
                        for (const value in players) {
                            let playername = `${players[value]['Name']}`;
                            let skinlink = `${players[value]['Skin']}`;
                            let finaldiv = "<div class='mchead'><img src='"+skinlink+"' alt='Skin from crafatar'><div class='name'>"+playername+"</div></div>";
                            serverid.getElementsByClassName("mcheads")[0].innerHTML += finaldiv;
                        }
                        serverid.getElementsByClassName("I")[0].style.display = "none";
                    } else {
                        serverid.getElementsByClassName("II")[0].innerHTML = "<div style=\"margin:auto\">"+language[lang][2]+"</div>";
                        serverid.getElementsByClassName("I")[0].style.display = "none";
                    }
                }
            } else if (type === "rust") {
                let description = data.raw.Description;
                serverid.getElementsByClassName("I")[0].getElementsByTagName("a")[0].href = "https://rustmaps.com/map/"+data.raw.WorldSize+"_"+data.raw.Seed+"?embed=img_i_l";
                serverid.getElementsByClassName("map")[0].src = data.raw.MapLink;
                serverid.getElementsByClassName("rustuptime")[0].innerHTML = "&nbsp;"+data.raw.RustUptime;
                serverid.getElementsByClassName("website")[0].href = data.raw.Website;
                serverid.getElementsByClassName("website")[0].innerHTML = data.raw.Website;
                serverid.getElementsByClassName("pvp")[0].innerHTML = "&nbsp;"+data.raw.PVP;
                serverid.getElementsByClassName("fps")[0].innerHTML = "&nbsp;"+data.raw.FPS;
                serverid.getElementsByClassName("tags")[0].innerHTML = data.raw.Tags;
                serverid.getElementsByClassName("description")[0].innerHTML = "";
                for (const value in description) {
                    let desc = `${description[value]}`;
                    serverid.getElementsByClassName("description")[0].innerHTML += desc;
                }
            } else if (type === "vrising") {
                let description = data.raw.Description;
                serverid.getElementsByClassName("ingameday")[0].innerHTML = "&nbsp;"+data.raw.InGameDay;
                serverid.getElementsByClassName("bloodbound")[0].innerHTML = "&nbsp;"+data.raw.Bloodbound;
                serverid.getElementsByClassName("tags")[0].innerHTML = data.raw.Tags;
                serverid.getElementsByClassName("description")[0].innerHTML = "";
                serverid.getElementsByClassName("map")[0].src = data.raw.MapLink;
                for (const value in description) {
                    let desc = `${description[value]}`;
                    serverid.getElementsByClassName("description")[0].innerHTML += desc;
                }
            } else if (type === "csgo") {
                let rules = data.raw.Rules;
                serverid.getElementsByClassName("sversion")[0].innerHTML = "&nbsp;"+data.raw.Version;
                serverid.getElementsByClassName("map")[0].src = data.raw.MapLink;
                if (data.raw.HasRules) {
                    let tbody = serverid.getElementsByTagName("tbody")[1];
                    for (const value in rules) {
                        let rulename = "<tr><td class='name'>"+`${rules[value][1]}`+"</td>";
                        let rulevalue = "<td class='value'>"+`${rules[value][2]}`+"</td></tr>";
                        tbody.innerHTML += rulename+rulevalue;
                    }
                }
            }  else if (type === "valheim") {
                serverid.getElementsByClassName("maxplayers")[0].innerHTML = "&nbsp;"+data.MaxPlayers;
                serverid.getElementsByClassName("queryport")[0].innerHTML = "&nbsp;"+data.QueryPort;

            }

            // Display Players
            if (type === "arkse" || type === "rust" || type === "vrising" || type === "csgo") {
                serverid.getElementsByClassName("IV")[0].getElementsByClassName("scroll")[0].innerHTML = "";
                let players = data.raw.Players;
                for (const val in players) {
                    let name = `${players[val]['Name']}`;
                    let time = `${players[val]['Time']}`;
                    let finaldata = "<h5 class=\"dark\">"+name+language[lang][0]+time+"</h5>";
                    serverid.getElementsByClassName("IV")[0].getElementsByClassName("scroll")[0].innerHTML += finaldata+"<br>";
                }
            }
            // Display System, Password, Map
            if (type === "arkse" || type === "rust" || type === "vrising" || type === "csgo" || type === "valheim") {
                serverid.getElementsByClassName("system")[0].innerHTML = "&nbsp;"+data.raw.OS;
                if (type !== "valheim") {serverid.getElementsByClassName("password")[0].innerHTML = "&nbsp;"+data.raw.Password;}
                serverid.getElementsByClassName("mapname")[0].innerHTML = "&nbsp;"+data.raw.Map;
            }
        }
    };
    xhttp.open("GET", "reload.php?id="+id, true);
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