<?php
include './html/langconf.php';
if ($map == "Ragnarok") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/KGcJjwL.jpg";
} elseif ($map == "LostIsland") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/gCDSBQA.jpg";
} elseif ($map == "Valguero") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/og8oSee.jpg";
} elseif ($map == "TheIsland") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/Ark-TheIsland-Map.jpg";
} elseif ($map == "Aberration") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/K8b0ezf.jpg";
} elseif ($map == "Viking_P") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/fpGj2Rl.jpg";
} elseif ($map == "TheCenter") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/OtJbTgc.jpg";
} elseif ($map == "CrystalIsles") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/mYFoUGU.jpg";
} elseif ($map == "ScorchedEarth") {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/Nxm7tvV.jpg";
} else {
    $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/crt9S5y.jpg";
}
$modlink = '<a href="https://steamcommunity.com/sharedfiles/filedetails/?id=';
function convertmodlistark($mod)
{
    include ('html/type/arkse/modlist.php');
    return $modlist['ArkModName'][$mod];
}
?>
<!DOCTYPE html>
<?php
// I query the number that the server has at Battlemetrics. And create the link to the banner with it.
$linkI = "https://cdn.battlemetrics.com/b/standardVertical/";
$linkII = ".html?foreground=%23EEEEEE&linkColor=%231185ec&lines=%23333333&background=%23222222&chart=players%3A24H&chartColor=%23FF0700&maxPlayersHeight=300";
// Url to Rcon control Website.
$rconurl = "https://wo-ist-der-igua.de/example.php";
?>
<table>
    <tbody>
    <tr>
        <td valign="top" class="sidebar">

            <div style="width:130px">
                <button class="sidebar-button tablink" onclick="openCity(event, 'Überblick')"><?php echo $language[$lang][1]?></button>
            </div>
        </td>
        <td class="Informationen">
            <div>
                <div class="Überblick" class="w3-container city">
                    <table>
                        <tbody>
                        <tr class="Überblick">
                            <td class="Daten">
                                <img class="map" src="<?php echo $maplink ?>" alt="<?php echo $maplink;?>"></img>
                                <div style="text-align:left">Map: <?php echo $map ?></div>
                                <div style="text-align:left">In-game Day: <?php echo $ingameday; ?></div>
                                <div style="text-align:left"><?php if ($pve == "true") {echo "PVE: True";} else {echo "PVP: True";} ?></div>
                </div>
            </div>

        </td>
        <td class="Leistung">
            <div style="text-align:left">System:
                <?php
                if (isset($Os)) {echo $Os;}
                ?>
            </div>
            <div style="text-align:left">Cluster ID:
                <?php
                if (isset($clusterid)) {echo $clusterid;}
                ?>
            </div>
            <div style="text-align:left">Password:
                <?php
                if ($password == "true" ) {
                    echo "True";
                } else {
                    echo "False";
                }
                ?>
            </div>
            <div style="text-align:left">Battleye:
                <?php
                if ($battleye == "true") {
                    echo "True";
                } else {
                    echo "False";
                }
                ?>
            </div>
            <?php
            if ($hasmods):
                ?>
                <div style="text-align:left">Mods:<br>
                    <?php
                    foreach ($mods as $mod) {
                        echo $modlink . $mod . '" target="_blank">';
                        $convertedmod = convertmodlistark($mod);
                        if (!empty($convertedmod)) {
                            echo $convertedmod;
                        } else {
                            echo $mod;
                        }
                        echo "</a><br>";
                    }
                    ?>
                </div>
            <?php
            endif;
            ?>
        </td>
        <td class="Spieler">
            <div class="Spieler">
                <h1><?php echo $language[$lang][4]?></h1>
                <div class="player-scroll">
                    <?php
                    foreach ($serverstatus->players as $player) {
                        if(!strlen($player->Name))
                            continue;
                        echo('<h5 class="dark">');
                        $rawtimeconv = $player->Time;
                        $rawtimeconv = round($rawtimeconv);
                        $output = sprintf('%02dh:%02dm:%02ds', ($rawtimeconv/ 3600),($rawtimeconv/ 60 % 60), $rawtimeconv% 60);
                        echo $player->Name." ".$language[$lang][5]." ";
                        echo $output;
                        echo "</h5><br>";
                    }
                    ?>
                </div>
            </div>
        </td>
        <td class="format-html">
            <iframe src="<?php echo $linkI . $banner . $linkII ?>" frameborder=0 style="border: 0px; width: 200px!important; height: 273px!important;" name="fcbyn"></iframe>
        </td>
    </tr>
    </tbody>
</table>
</div>

<div class="Control" class="w3-container city" style="display:none">
    <button class="stopp" type="submit">Stopp</button>
    <div class="div1"><h2>das ergebnis ist...</h2></div>
    <button class="Start" type="submit">Start</button>
    <div class="div2"><h2>das ergebnis ist...</h2></div>

</div>

<div class="Config w3-container city" style="display:none">
    <h2>Config</h2>
    <iframe src="<?php echo $linkI . $banner . $linkII ?>" frameborder=0 style="border: 0; width: 200px!important; height: 273px!important;" name="fcbyn"></iframe>
</div>

</div>
</td>

<script>
    function openCity(evt, cityName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("city");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " w3-red";
    }
</script>
</tr>
</tbody>
</table>
