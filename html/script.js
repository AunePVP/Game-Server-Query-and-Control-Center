function steamwebapikeyFuntion() {
    let x = document.getElementById("steamwebkey");
    if (x.type === "password") {
        x.type = "text";
        x.size = "33";
        x.style.textAlign = "center";
    } else {
        x.type = "password";
        x.size = "17";
        x.style.textAlign = "left";
    }
}
function passwordFunction() {
    let x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}