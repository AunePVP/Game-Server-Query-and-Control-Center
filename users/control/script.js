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
function validateForm() {
    let x = document.forms["myForm"]["fname"].value;
    if (x == "") {
        alert("Name must be filled out");
        return false;
    }
}