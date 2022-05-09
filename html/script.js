function steamwebapikeyFuntion() {
    var x = document.getElementById("steamwebkey");
    if (x.type === "password") {
        x.type = "text";
        x.size = "33";
        x.style.textAlign = "center";
        x.s
    } else {
        x.type = "password";
        x.size = "17";
        x.style.textAlign = "left";
    }
}
function passwordFunction() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}