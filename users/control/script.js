function openNav() {
    document.getElementById("myNav").style.width = "100%";
}
function closeNav() {
    document.getElementById("myNav").style.width = "0%";
}
function selecttype() {
    let type = document.getElementById("type").value;
    if (type == "arkse") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type == "csgo") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type == "minecraft") {
        text = "If your server doesn't support the query protocol, use 0 as query port. If you don't want to use rcon, use 0 as rcon port.";
    } else if (type == "valheim") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type == "vrising") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type == "rust") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    }
    document.getElementById("notes").innerHTML = text;
}
if (window.location.search.indexOf('page=settings') > -1) {
    document.getElementById("tab1").style.textDecoration = "none";
    document.getElementById("tab2").style.textDecoration = "underline";
    document.getElementById("control").style.display = "none";
    document.getElementById("settings").style.display = "block";
} else {
    document.getElementById("tab2").style.textDecoration = "none";
    document.getElementById("tab1").style.textDecoration = "underline";
    document.getElementById("control").style.display = "flex";
    document.getElementById("settings").style.display = "none";
}
function tab(clicked_id) {
    let x = document.getElementById("control");
    let y = document.getElementById("settings");
    let z = document.getElementById(clicked_id);
    if (clicked_id === "tab1") {
        x.style.display = "flex";
        document.getElementById("tab2").style.textDecoration = "none";
        y.style.display = "none";
    } else if (clicked_id === "tab2") {
        x.style.display = "none";
        y.style.display = "block";
        document.getElementById("tab1").style.textDecoration = "none";
    }
    z.style.textDecoration = "underline";
}
function startTime() {
    const today = new Date();
    let h = today.getHours();
    let m = today.getMinutes();
    let s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('clock').innerHTML =  h + ":" + m;
    setTimeout(startTime, 1000);
}

function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}