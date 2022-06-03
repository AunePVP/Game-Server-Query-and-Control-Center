<?php
header("Content-type: text/css");
$theme = "light"; // Choose between dark and light
$background = "#1D1D1F";
echo $querytime['display'] = "block"; // Display Querytime at bottom of page
switch ($theme) {
    case "dark":
        $server['color'] = "#3b3b3b";
        $players['color'] = "#ff2b2b";
        $mapimg['border'] = "none";
        $font['color'] = "#ebebeb";
        $font['linkcolor'] = "#d7d7d7";
        $font['serverlinkcolor'] = "#0093ff";
        $querytime['color'] = "white";
        break;
    case "light":
        $server['color'] = "#F5F5F5";
        $players['color'] = "#FF0000";
        $mapimg['border'] = "5px solid #888888";
        $font['color'] = "black";
        $font['linkcolor'] = "#323232";
        $font['serverlinkcolor'] = "#007CFF";
        $querytime['color'] = "white";
        break;
    default:
        trigger_error("No theme chosen", E_USER_ERROR);
}
$server['spacing'] = "10px";
$server['width'] = "80%";
$players['size'] = "25px";
$players['weight'] = "bold";
$tab['padding'] = "4px";
$font['normal'] = "Montserrat, sans-serif";
$font['banner'] = "Helvetica, sans-serif";
?>
@import url('https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap');
@import url('https://fonts.googleapis.com/css?family=Lato:400,900|IBM+Plex+Mono');
@import url('https://fonts.cdnfonts.com/css/helvetica-neue-9');
<style>
    body {
        background-color: <?php echo $background ?>;
    }
    /* link hover start */
    a.white-hover {
        color: white;
        text-decoration: none;
        position: relative
    }
    a.black-hover {
        color: black;
        text-decoration: none;
        position: relative
    }
    a.white-hover:after{
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        border-bottom: 2px solid white;
        transition: .6s
    }
    a.black-hover:after{
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        border-bottom: 2px solid #000;
        transition: .6s
    }
    a:hover:after{
        width: 100%
    }
    /* link hover end */
    ::marker{ display:none; } summary{ list-style: none }
    ::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }
    :focus {
        outline: none;
    }
    details summary::-webkit-details-marker {
        display:none;
    }
    * {
        margin: 0;
        padding: 0;
    }
    html, body {
        min-height: 100vh;
        background-color: #1D1D1F;
        text-align: center;
        font-family: Montserrat, sans-serif;
        -ms-user-select: None;
        -moz-user-select: None;
        -webkit-user-select: None;
        user-select: None;

    }
    .movediv {
        width: 100%;t
    }
    .flex {
        display: flex;
    }
    #nav {
        font-family: 'Lato', sans-serif;
        font-size: 25px;
        position: sticky;
        top: 0;
        overflow: auto;
        height:58px;
        z-index: 10;
    }
    #nav ul {
        background-color: #1D1D1F !important;
        list-style-type: none;
        text-align: left;
        background-color: #171c24ff;
        display: flex;
        height: 58px;
    }
    #nav ul li {
        display: inline;
    }
    ul li a {
        display: inline-block;
        margin: 14px 16px;
        text-decoration: none;
        font-family: Montserrat, sans-serif;
        color: white;
    }
    #nav .login {
        display: flex;
        justify-content: center;
    }
    .login .button {
        margin: auto 13px;
        display: inline-block;
        text-decoration: none;
        font-family: Montserrat, sans-serif;
        color: white;
    }
    .user-avater {
        border: 5px solid <?php echo $server['color']?>;
        height: calc(100% - 10px);
    }
    .white-inftext {
        color: white;
        font-size: x-large;
    }
    section {
        color: <?php echo $font['color']?>;
        background-color: <?php echo $server['color']?>;
        margin-bottom: <?php echo $server['spacing']?>;
    }
    .container {
        width: <?php echo $server['width']?>;
        margin: auto;
        padding-top: 70px;
    }
    .status-letter-online, .status-letter-offline {
        writing-mode: vertical-lr;
        text-orientation: upright;
        font-weight: bold;
        font-size: 13px;
        height: 112px;
    }
    .status-letter-online {
        display: block;
    }
    .status-letter-offline {
        display: none;
    }
    .status_icon_onl {
        width: 0.8rem;
        height: 7rem;
        padding: 0;
        margin: 0;
        border-radius: .125rem 0 0 .125rem;
        float: left;
    }
    .status_cell {
        width: 7%;
    }
    .connectlink_cell {
        width: 15%;
    }
    .connectlink_cell a {
        color: <?php echo $font['serverlinkcolor'] ?>;
        text-decoration: none;
        position: relative;
    }
    .servername_cell {
        width: 56%;
        letter-spacing: 1px;
    }
    .players_cell {
        width: 11%;
    }
    .server_list_table {
        color: <?php echo $font['color']?>;
        width: 100%;
        font-family: Montserrat, sans-serif;
    }
    .server_list_table_top th {
        font-weight: normal;
        font-size: 23px;
        box-sizing: border-box;
        font-family: Montserrat, sans-serif;
    }
    .players_numeric {
        color: <?php echo $players['color']?>;
        font-size: <?php echo $players['size']?>;
        font-weight: <?php echo $players['weight']?>;
        font-family: Helvetica Neue, sans-serif;
    }
    .I {
        height: 275px;
    }
    .II {
        text-align: left;
        min-width: -webkit-fit-content;
        padding: 4px 7px 0;
    }
    .II a {
        color: <?php echo $font['linkcolor']?>;
    }
    .II.csgo th {
        color: black;
        font-weight: 600;
    }
    .II.csgo tbody {
        display: block;
        overflow-y: scroll;
        max-height: 166px;
    }
    .II.csgo td.name {
        max-width: 340px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }
    .II.csgo td.value {
        max-width: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: right;
    }
    .II.csgo table tr {
        display: table;
        width: 100%;
    }
    .IV {
        min-width: 215px;
        max-width: 215px;
        padding: 0 4px;
    }
    .IV p {
        font-size: 20px;
        font-weight: 800;
    }
    .IV .scroll {
        height: 247px;
        border-style: solid;
        border-width: 2px;
        overflow-y: scroll;
        padding: 0 7px;
    }
    .canvasparent {
        height: 120px!important;
    }
    .vchartdiv {
        background-color: #1D1D1F;
        width: 195px;
        padding: 12px 10px 10px 10px;
    }
    .vchartdiv .servername p {
        font-family: Helvetica, serif;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        color: white;
        margin: 0 0 8px 0;
        letter-spacing: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* number of lines to show */
        line-clamp: 2;
        -webkit-box-orient: vertical;
        min-height: 32px;
    }
    .vchartdiv .connectlink.vchart {
        display: flex;
        justify-content: center;
    }
    .vchartdiv .connectlink a{
        color: #2e87ff;
        font-family: Helvetica,serif;
        font-size: 15px;
    }
    .vchartdiv .items {
        border-bottom: solid;
        border-color: rgb(70,70,70);
        margin: 0 0 8px 0;
        padding: 5px 0;

    }
    .vchartdiv .items svg{
        width: 25px;
        height: 25px;
        padding: 5px 2px 5px 5px;
    }
    .vchartdiv .items p, .version {
        margin:auto 0!important;
        font-family: Helvetica,serif!important;
        font-size: 14px!important;
        letter-spacing: normal!important;
        color:white!important;
        letter-spacing: normal!important;
        text-align: left!important;
        padding: 5px 0!important;
    }
    .vchartdiv p {
        font-family: Helvetica, sans-serif;
        font-size: 16px;
        font-stretch: expanded;
        margin: auto;
        text-align: center;
        color: white;
        letter-spacing: 2px;
    }
    .tab {
        padding: <?php echo $tab['padding']?>;
    }
    .map {
        border: <?php echo $mapimg['border']?>;
        border-radius: 8px;
        box-sizing: border-box;
        height: 234px;
    }
    .querytime {
        display: <?php echo $querytime['display'] ?>;
        color: <?php echo $querytime['color'] ?>;
    }
    @media only screen and (min-width: 1700px) {
        .container {width: 75%;}
    }
    @media only screen and (min-width: 1400px) {
        .csmovedivhide {display: none}
        .II.csgo, .II.csgo table {width: 100%;}
    }
    @media only screen and (max-width: 1280px) {
        .map {height: 220px;}
    }
    @media only screen and (max-width: 1200px) {
        .map {height: 200px;}
        .IV {min-width: 190px}
    }
    @media only screen and (max-width: 1130px) {
        .II {min-width: 180px}
    }
    @media only screen and (max-width: 1024px) {
        .map {height: 180px}
        .IV {min-width: 165px;}
        .vchartdiv {padding: 12px 5px 10px 5px;}
    }
    @media only screen and (max-width: 950px) {
        .II {display: none;}
        .map {height: 210px}
    }
    @media only screen and (max-width: 750px) {
        .map {height: 180px}
    }
    @media only screen and (max-width: 710px) {
        .I, .movediv, .connectlink_cell {display: none}
        .IV {min-width: 200px}
        .IV, .V {margin:auto;width: 100%;}
        .vchartdiv {padding: 12px 14px 10px 14px}
    }
    @media only screen and (max-width: 650px) {
        .status-letter-online, .server_list_table_top {display: none}
    }
    @media only screen and (max-width: 570px) {
        .players_cell, .IV {display: none!important}
        .container, .servername_cell {width: 100%}
        .vchartdiv {width: auto}
        .container {padding-top: 20px;}
    }
</style>
