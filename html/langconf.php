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
//de
$language['de'][1] = "Überblick";
$language['de'][2] = "System";
$language['de'][3] = "Cluster ID";
$language['de'][4] = "Spieler";
$language['de'][5] = "seit";
$language['de'][6] = "Status";
$language['de'][7] = "IP";
$language['de'][8] = "Name";
$language['de'][9] = "Du musst dich erst einloggen um eigene Server hinzuzufügen.";
$language['de'][10] = "Keine Mods auf dem Server gefunden.";
$language['de'][11] = "In-game Tag: ";
$language['de'][12] = "Passwort";
$language['de'][13] = "Query ist auf diesem Server deaktiviert.";
//en
$language['en'][1] = "General";
$language['en'][2] = "System";
$language['de'][3] = "Cluster ID";
$language['en'][4] = "Players";
$language['en'][5] = "since";
$language['en'][6] = "Status";
$language['en'][7] = "IP";
$language['en'][8] = "Title";
$language['en'][9] = "You have to log in first to add your own servers.";
$language['en'][10] = "There are no mods installed on this server.";
$language['en'][11] = "In-game Day: ";
$language['en'][12] = "Password";
$language['en'][13] = "Query is disabled on this server.";