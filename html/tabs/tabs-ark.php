<?php
$query = $_GET['query'];
if ($map == "Ragnarok") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/KGcJjwL.jpg";
} elseif ($map == "LostIsland") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/gCDSBQA.jpg";
} elseif ($map == "Valguero") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/og8oSee.jpg";
} elseif ($map == "TheIsland") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/Ark-TheIsland-Map.png";
} elseif ($map == "Aberration") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/K8b0ezf.jpg";
} elseif ($map == "Viking_P") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/fpGj2Rl.jpg";
} elseif ($map == "TheCenter") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/OtJbTgc.jpg";
} elseif ($map == "CrystalIsles") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/mYFoUGU.png";
} elseif ($map == "ScorchedEarth") {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/Nxm7tvV.jpg";
} else {
  $maplink = "https://cdn.muehlhaeusler.online/img/tracker/Maps/crt9S5y.jpg";
}   
$modlink = '<a href="https://steamcommunity.com/sharedfiles/filedetails/?id=';
?>
<!DOCTYPE html>

<httpProtocol>
  <customHeaders>
    <add name="Content-Security-Policy" value="frame-ancestors https://cdn.battlemetrics.com" />
    <!-- Test this option as Chrome supports CSP now for Iframe opening-->
  </customHeaders>
</httpProtocol>
<?php
// I query the number that the server has at Battlemetrics. And create the link to the banner with it.
$banner = $_GET['banner'];
$rport = $_GET['rport'];
$linkI = "https://cdn.battlemetrics.com/b/standardVertical/";
$linkII = ".html?foreground=%23EEEEEE&linkColor=%231185ec&lines=%23333333&background=%23222222&chart=players%3A24H&chartColor=%23FF0700&maxPlayersHeight=300";
// Url to Rcon control Website.
$rconurl = "https://wo-ist-der-igua.de/example.php";
?>
<table>
        <tbody>
            <tr id="sidebar">
                <td valign="top" class="sidebar">

<div class="w3-sidebar w3-bar-block w3-light-grey w3-card" style="width:130px">
<button class="w3-bar-item w3-button tablink w3-red" onclick="openCity(event, 'Überblick<?php echo $query;?>')">Überblick</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Control<?php echo $query;?>')">Control</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Config<?php echo $query;?>')">Config</button>
</div>
</td>
<td class="Informationen">
<div>
  <div id="Überblick<?php echo $query?>" class="w3-container city">
    <table>
        <tbody>
            <tr id="Überblick">
                <td class="Daten">
                  <img class="map" src="<?php echo $maplink ?>" alt="<?php echo $maplink;?>"></img>
                  <div style="text-align:left">Map: <?php echo $map ?></div>
                  <div style="text-align:left">Ping: <?php echo $ping . "ms" ?></div>
                  <div style="text-align:left">Mods: 
                  <?php 
                  foreach ($battlemetrics_serverstatus->data->attributes->details->modIds as $mod) {
                    echo $modlink . $mod . '" target="_blank">' . $mod . "</a><br>";
                  }
                  ?>
                  <div>
    </div>
</div>

                </td>
                <td class="Leistung">
                  <!-- <iframe src="<?php echo $domain . ":3000"; ?>"></iframe> -->
                  <iframe src=http://85.215.90.221:3000></iframe>
                </td>
                <td class="Spieler">
                    <div id="Spieler">
                        <h1>Spieler</h1>
                        <?php 
                        foreach ($serverstatus->players as $player) {
                          if(!strlen($player->name))
                          continue;
                          echo('<h5 class="dark">');
                          $rawtimeconv = $player->raw->time;
                          $rawtimeconv = round($rawtimeconv);
                          $output = sprintf('%02dh:%02dm:%02ds', ($rawtimeconv/ 3600),($rawtimeconv/ 60 % 60), $rawtimeconv% 60);
                          echo $player->name . " seit ";
                          echo $output;
                          echo "</h5><br>";
                      }
                        ?> 
                    </div>
                </td>
                <td class="format-html">
                <iframe src="<?php echo $linkI . $banner . $linkII ?>" frameborder=0 style="border: 0px; width: 200px!important; height: 273px!important;" name="fcbyn"></iframe>
                </td>
            </tr>
        </tbody>
    </table>
  </div>

  <div id="Control<?php echo $query?>" class="w3-container city" style="display:none">
<button id="stopp" type="submit">Stopp</button>
<div id="div1"><h2>das ergebnis ist...</h2></div>
<button id="Start" type="submit">Start</button>
<div id="div2"><h2>das ergebnis ist...</h2></div>

  </div>

  <div id="Config<?php echo $query?>" class="w3-container city" style="display:none">
    <h2>Config</h2>
<iframe src="<?php echo $linkI . $banner . $linkII ?>" frameborder=0 style="border: 0px; width: 200px!important; height: 273px!important;" name="fcbyn"></iframe>
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
