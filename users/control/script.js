function openNav() {
    document.getElementById("myNav").style.width = "100%";
}
function closeNav() {
    document.getElementById("myNav").style.width = "0%";
}
function selecttype() {
    let type = document.getElementById("type").value;
    if (type === "arkse") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type === "csgo") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type === "minecraft") {
        text = "If your server doesn't support the query protocol, use 0 as query port. If you don't want to use rcon, use 0 as rcon port.";
    } else if (type === "valheim") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type === "vrising") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type === "rust") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    } else if (type === "dayz") {
        text = "If you don't want to use rcon, use 0 as rcon port.";
        document.getElementById("input-qport").required = true;
    }
    document.getElementById("notes").innerHTML = text;
}
if(document.getElementById('tab1')!=null){
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
function changetheme(theme) {
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(theme).classList.add("active");
    if (theme === "btndark"){
        backgroundcolor = "#1D1D1F";
        servercolor = "#3b3b3b";
        fontcolor = "#ebebeb";
        border = "1px solid transparent";
        themename = "dark";
    } else if (theme === "btnlight") {
        backgroundcolor = "white";
        servercolor = "#e6e6e6";
        fontcolor = "black";
        border = "1px solid transparent";
        themename = "light";
    } else if (theme === "btnsnight") {
        backgroundcolor = "#1D1D1F";
        servercolor = "#F5F5F5";
        fontcolor = "black";
        border = "1px solid transparent";
        themename = "summer-night";
    } else if (theme === "btnsnightinv") {
        backgroundcolor = "#e6e6e6";
        servercolor = "#1D1D1F";
        fontcolor = "#ebebeb";
        border = "1px solid transparent";
        themename = "summer-night-inverted";
    } else if (theme === "btnmidnight") {
        backgroundcolor = "#0F0F0FFF";
        servercolor = "rgb(0, 0, 0)";
        fontcolor = "#ebebeb";
        border = "1px solid white";
        themename = "midnight";
    }
    document.getElementById('server_list_table').style.color = fontcolor;
    document.getElementById('themevl').style.backgroundColor = backgroundcolor;
    document.getElementById('vorschauparent').style.backgroundColor = servercolor;
    document.getElementById('vorschauparent').style.border = border;
    document.getElementById('themeinput').value = themename;
}
function changetabacc(value) {
    document.querySelectorAll('.tabacc').forEach(function(el) {
        el.style.display = 'none';
    });
    tablinksacc = document.getElementsByClassName("tablinksacc");
    for (i = 0; i < tablinksacc.length; i++) {
        tablinksacc[i].className = tablinksacc[i].className.replace(" active", "");
    }
    if (value === "username"){
        document.getElementsByClassName('tabacc')[0].style.display = "block";
        document.getElementsByClassName('tablinksacc')[0].classList.add("active");
    } else if (value === "password") {
        document.getElementsByClassName('tabacc')[1].style.display = "block";
        document.getElementsByClassName('tablinksacc')[1].classList.add("active");
    } else if (value === "delete") {
        document.getElementsByClassName('tabacc')[2].style.display = "block";
        document.getElementsByClassName('tablinksacc')[2].classList.add("active");
    }
}
function newseed() {
    let seed = document.getElementById("seed").value;
    seedslice = seed.slice(0, 4);
    seedchar = seed.slice(-1);
    seedchar2 = seed.slice(-2);
    if (!isNaN(seedchar)) {
        let a = Number(seedchar);
        let b = 1;
        let nseedchar = a + b;
        newseeds = seedslice + nseedchar;
    } else {
        newseeds = seedslice + "0";
    }
    document.getElementById("seed").value = newseeds;
    selectstyle();
}
function selectstyle() {
    let type = document.getElementById("style").value;
    if (type === "adventurer") {
        sprite = "adventurer";
    } else if (type === "adventurer-neutral") {
        sprite = "adventurer-neutral";
    } else if (type === "notionists") {
        sprite = "notionists";
    } else if (type === "human") {
        sprite = "avataaars";
    } else if (type === "bottts") {
        sprite = "bottts";
    } else if (type === "croodles") {
        sprite = "croodles";
    } else if (type === "identicon") {
        sprite = "identicon";
    } else if (type === "pixel-art") {
        sprite = "pixel-art";
    } else if (type === "pixel-art-neutral") {
        sprite = "pixel-art-neutral";
    }
    if (type !== "human") {document.getElementById("selectsph").style.display = "none";}
    let seed = document.getElementById("seed").value;
    let link = "https://api.dicebear.com/7.x/";
    document.getElementById("ppictureimg").src = link + sprite + "/svg?seed=" + seed;
}
