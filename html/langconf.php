<?php
if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $lang = (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    if ($lang == "de") {
        $lang = "de";
    } else {
        $lang = "en";
    }
} else {
    $lang = "en";
}
if ($lang == "en") {
    $Überblick = "General";
    $Control = "Control";
    $Config = "Config";
    $Spieler = "Players";
    $seit = "since";
    $Status = "Status";
    $Adresse = "IP";
    $Name = "Title";
    $mustlogin = "You have to log in first to add your own servers.";
} elseif ($lang == "de") {
    $Überblick = "Überblick";
    $Control = "Control";
    $Config = "Config";
    $Spieler = "Spieler";
    $seit = "seit";
    $Status = "Status";
    $Adresse = "Adresse";
    $Name = "Name";
    $mustlogin = "Du musst dich erst einloggen um eigene Server hinzuzufügen.";
} else {
    $Überblick = "General";
    $Control = "Control";
    $Config = "Config";
    $Spieler = "Players";
    $seit = "since";
    $Status = "Status";
    $Adresse = "IP";
    $Name = "Title";
    $mustlogin = "You have to log in first to add your own servers.";
}