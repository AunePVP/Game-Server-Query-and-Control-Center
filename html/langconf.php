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
//en
$language['en'][1] = "General";
$language['en'][2] = "System";
$language['de'][3] = "Cluster ID";
$language['en'][4] = "Players";
$language['en'][5] = "since";
$language['en'][6] = "Status";
$language['en'][7] = "IP";
$language['en'][8] = "Title";
$language['en'][9] = "Du musst dich erst einloggen um eigene Server hinzuzufügen.";