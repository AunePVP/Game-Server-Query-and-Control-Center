<?php
include './html/langconf.php';
// Set image, convert mods...
if ($type == "arkse") {
    $display['IV'] = "block";
    $modlink = '<a href="https://steamcommunity.com/sharedfiles/filedetails/?id=';
} elseif ($type == "minecraft") {
    $display['IV'] = "block";
} elseif ($type == "csgo") {
    $display['IV'] = "block";
} elseif ($type == "valheim") {
    $display['IV'] = "none";
} elseif ($type == "vrising") {
    $maplink = "html/img/map/$map.webp";
    $display['IV'] = "block";
}elseif ($type == "rust") {
    $display['IV'] = "block";
} elseif ($type == "fivem") {
    $display['IV'] = "none";
} else {
    $display['IV'] = "none";
}
?>
<style>
</style>
<div class="tab flex">
    <!-- MAP -->
    <div class="I <?php if($type=="valheim"){echo "valheim";}?>">
        <?php
        if ($type == "arkse") {
            echo '<img class="map">';
            echo '<div class="flex"><span style="font-weight: 500;">Map:</span><div class="mapname"></div></div>';
            echo '<div class="flex"><span style="font-weight: 500;">'.$language[$lang][11]."</span><div class='ingameday'></div></div>";
        } elseif ($type == "valheim") {
            echo('<a class="twitter-timeline" data-chrome="nofooter noheader noscrollbar" id="twitter-timeline" data-width="330" data-height="275" data-dnt="true" data-theme="dark" href="https://twitter.com/Valheimgame">Tweets by Valheimgame</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>');
        } elseif ($type == "csgo") {
            echo '<img class="map">';
            echo '<div class="flex"><span style="font-weight: 500;">Map:</span><div class="mapname"></div></div>';
        }
        elseif ($type == "vrising") {
            echo '<img class="map">';
            echo '<div class="flex" style="max-width: 234px;"><span style="font-weight: 500;">Map:</span><div class="mapname"></div></div>';
            echo '<div class="flex"><span style="font-weight: 500;">'.$language[$lang][11]."</span><div class='ingameday'></div></div>";
        }
        elseif ($type == "rust") {
            echo '<a target="_blank" rel="noopener noreferrer" style="color:#888888"><img class="map"></a>';
            echo '<div class="flex"><span style="font-weight: 500;">Map:</span><div class="mapname"></div></div>';
            echo '<div class="flex"><span style="font-weight: 500;">Uptime:</span><div class="rustuptime"></div></div>';
        }
        ?>
    </div>
    <!-- DETAILS -->
    <div class="II <?php if($type=="csgo"){echo'csgo';}elseif($type=="minecraft"&&$qport == 0) {echo "flex";}?>">
        <?php
        if ($type == "arkse") {
            echo "<div class='flex'><span style='font-weight: 500;'>System:</span><div class='system'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>Cluster ID:</span><div class='clusterid'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>PVP:</span><div class='pvp'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>".$language[$lang][12].":</span><div class='password'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>Battleye:</span><div class='battleye'></div><br></div>";
            echo "<span style='font-weight: 500;'>Mods:</span><div class='mods'></div><br>";
        } elseif ($type == "valheim") {
            echo "<div class='flex'><span style='font-weight: 500;'>System:</span><div class='system'></div><br></div>";
            echo '<div class="flex"><span style="font-weight: 500;">Map:</span><div class="mapname"></div></div>';
            echo '<div class="flex"><span style="font-weight: 500;">Max Players:</span><div class="maxplayers"></div></div>';
            echo '<div class="flex"><span style="font-weight: 500;">Query Port:</span><div class="queryport"></div></div>';
            echo '<div class="flex"><span style="font-weight: 500;">Steam Page:&nbsp;</span><a href="https://store.steampowered.com/appv/892970/Valheim/" target="_blank" rel="noopener noreferrer">Link</a></div>';
            echo '<div class="flex"><span style="font-weight: 500;">Wiki:&nbsp;</span><a href="https://valheim.fandom.com/wiki/Valheim_Wiki" target="_blank" rel="noopener noreferrer">Link</a></div>';
            echo '<div class="flex"><span style="font-weight: 500;">Website:&nbsp;</span><a href="https://www.valheimgame.com" target="_blank" rel="noopener noreferrer">Link</a></div>';
        } elseif ($type == "csgo") {
            echo "<div class='flex'><span style='font-weight: 500;'>System:</span><div class='system'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>Version:</span><div class='sversion'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>".$language[$lang][12].":</span><div class='password'></div><br></div>";
            $lastplayerline = json_decode(tailCustom("query/cron/$ServerID.json", 1));
            $csgorules = $lastplayerline->csgorules ?? "0";
            if (isset($csgorules)) {
                echo "<span style='font-weight: 500;'>Server Settings:<br></span>";
                echo "<div style='height: 100%; width: 100%;'>";
                echo "<table><thead><tr style='border-bottom: solid;border-width: 2px;'><th>Name</th><th style='text-align: right'>Value</th></tr></thead><tbody>";
                echo "</tbody></table></div>";
            }
        } elseif ($type == "vrising") {
            echo "<div class='flex'><span style='font-weight: 500;'>System:</span><div class='system'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>".$language[$lang][12].":</span><div class='password'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>Bloodbound:</span><div class='bloodbound'></div><br></div>";
            echo "<div style='max-height:166px;overflow-y:scroll;'><div style='max-width:300px'><span style='font-weight: 500;'>Tags:</span><div class='tags'></div></div>";
            echo "<span style='font-weight: 500;'>Description:</span><div class='description'></div><br>";
            echo "</div>";
        } elseif ($type == "rust") {
            echo "<div class='flex'><span style='font-weight: 500;'>System:</span><div class='system'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>".$language[$lang][12].":</span><div class='password'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>Website:&nbsp;</span><a class='website' target='_blank' rel='noopener noreferrer'>Link</a><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>PVP:</span><div class='pvp'></div><br></div>";
            echo "<div class='flex'><span style='font-weight: 500;'>FPS:</span><div class='fps'></div><br></div>";
            echo "<div style='max-height:166px;overflow-y:scroll;'><div style='max-width:300px'><span style='font-weight: 500;'>Tags:</span><div class='tags'></div></div>";
            echo "<span style='font-weight: 500;'>Description:</span><div class='description'></div><br>";
            echo "</div>";
        }
        ?>
    </div>
    <div class="movediv<?php if ($type == "csgo" && isset($csgorules)){echo " csmovedivhide";} elseif ($type == "minecraft"){echo " mcmovedivhide";} elseif ($type == "valheim"){echo " valheim";}?>"></div>
    <!-- DIV III (Not used yet) -->
    <div class="III"></div>
    <div class="IV" style="display:<?php echo $display['IV']?>">
        <p><?php
            // Check if Mods for Minecraft or Players for Ark is displayed
            if ($type == "arkse" || $type == "csgo" || $type == "vrising" || $type == "rust") {
                echo $language[$lang][4];
            } elseif ($type == "minecraft") {
                echo "Mods";
            }
            ?></p>
        <div class="scroll"></div>
    </div>
    <div class="V">
        <div class="vchartdiv <?php if($type == "valheim"){echo "valheim";}?>">
            <div class="servername">
                <p href="#">Loading...</p>
            </div>
            <div class="connectlink">
                <a title="Connect to server">xx.xxx.xxx:xxxx</a>
            </div>
            <div class="items flex">
                <div class="flex width50">
                    <img src="html/img/uptime.svg" alt="uptime svgv">
                    <?php
                    $lastplayerlines = tailCustom("query/cron/$ServerID.json", 7);
                    $playerlineexit = array();
                    $count = 0;
                    unset($lastplayers);
                    foreach(preg_split("/((\r?\n)|(\r\n?))/", $lastplayerlines) as $playerline){
                        $playerlinedecode = json_decode($playerline);
                        $lastplayers[$count] = $playerlinedecode->players;
                        $count = $count + 1;
                        $uptime = $playerlinedecode->uptime;
                        if (isset($lastplayers[6])) {
                            if ($lastplayers[6] < 5) {
                                $maxchart = $lastplayers[6]+1;
                            }
                            if ($lastplayers[6] > 5) {
                                $maxchart = $lastplayers[6]+5;
                            }
                            if ($lastplayers[6] > 10) {
                                $maxchart = $lastplayers[6]+15;
                            }
                            if ($lastplayers[6] > 15) {
                                $maxchart = $lastplayers[6]+30;
                            }
                        } else {
                            $maxchart = 0;
                        }
                    }
                    $uptimebanner = str_replace(".", ",", round($uptime, 1))."%";
                    ?>
                    <p><?php echo $uptimebanner ?></p>
                </div>
                <div class="flex width50 players">
                    <img src="html/img/user.svg" alt="suer svg">
                    <p>0/0</p>
                </div>
            </div>
            <p>History</p>
            <div class="canvasparent">
                <canvas id="Chart<?php echo $ServerID ?>" class="vchart" width="190" height="120"></canvas>
            </div>
            <script>Chart.defaults.color = 'white';let xLabels<?php echo $ServerID ?> = ['60','50','40','30','20','10','now'];let xValues<?php echo $ServerID ?> = <?php echo "[$lastplayers[0], $lastplayers[1], $lastplayers[2], $lastplayers[3], $lastplayers[4], $lastplayers[5], $lastplayers[6]];"; ?>new Chart("Chart<?php echo $ServerID ?>", {type: "line", data: {labels: xLabels<?php echo $ServerID ?>, datasets: [{label: "Players", data: xValues<?php echo $ServerID ?>, backgroundColor: "white", borderColor: "red", color: "white", borderWidth: 2, pointBorderWidth: 1.5, pointRadius: 2, fill: false, tension: 0.4, pointBorderColor: "white",}]}, options: {scales: {x: {grid: {display:false}, ticks: {display: true}}, y: {<?php if ($lastplayers[0] < 200) {echo "beginAtZero: true, ";} if ($lastplayers[0] < 200) {echo "max: $maxchart, ";}?>grid:{display:true, color: 'rgb(70,70,70)',},}}, responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false,}}}});</script>
        </div>
    </div>
</div>

