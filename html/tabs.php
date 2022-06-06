<?php
include './html/langconf.php';
// Set image, convert mods...
if ($type == "arkse") {
    $display['IV'] = "block";
    $officialmaps = array("Aberration", "CrystalIsles", "Gen2", "Gen", "LostIsland", "Ragnarok", "ScorchedEarth", "TheCenter", "TheIsland", "Valguero", "Viking_P", "Valhalla", "TheVolcano");
    if (in_array($map, $officialmaps)) {
        $maplink = "html/img/map/$map.webp";
    } else {
        $maplink = "html/img/map/modmap.webp";
    }
    $modlink = '<a href="https://steamcommunity.com/sharedfiles/filedetails/?id=';
    function convertmodlistark($mod)
    {
        include ('html/type/arkse/modlist.php');
        return $modlist['ArkModName'][$mod];
    }
} elseif ($type == "minecraft") {
    $display['IV'] = "block";
} elseif ($type == "csgo") {
    $display['IV'] = "block";
    $officialmaps = array("ar_baggage", "ar_dizzy", "ar_lunacy", "ar_monastery", "ar_shoots", "cs_agency", "cs_assault", "cs_climb", "cs_italy", "cs_militia", "cs_office", "de_ancient", "de_bank", "de_cache", "de_canals", "de_cbble", "de_crete", "de_dust2", "de_hive", "de_inferno", "de_iris", "de_lake", "de_mirage", "de_nuke", "de_overpass", "de_safehouse", "de_shortdust", "de_shortnuke", "de_stmarc", "de_sugarcane", "de_train", "de_vertigo", "dz_blacksite", "dz_ember", "dz_sirocco", "dz_vineyard", "ze_Bathroom_v2_5");
    function convertcsgomapname($mapname)
    {
        include ('html/type/csgo/maplist.php');
        return $mapname['CsgoMapName'][$mapname];
    }
    if (in_array($map, $officialmaps)) {
        $maplink = "html/img/map/$map.webp";
        $mapname = convertcsgomapname($map) ?? $map;
    } else {
        $substr = substr($map, 0, 3);
        if (preg_match('/[a-zA-Z]{2}\_{1}/m', $substr)) {
            $map = substr($map, 3);
        }
        $mapname = ucwords(str_replace("_"," ", $map));
        $maplink = "html/img/map/csgo_modmap.webp";
    }
} elseif ($type == "valheim") {
    $display['IV'] = "none";
} elseif ($type == "vrising") {
    $maplink = "html/img/map/$map.webp";
    $display['IV'] = "block";
}elseif ($type == "rust") {
    $display['IV'] = "block";
    unset($metas,$mapurl);
    $mapurl = "https://rustmaps.com/map/".$worldsize."_".$seed."?embed=img_i_l";

    $metas = get_meta_tags($mapurl);
    if (isset($metas['twitter:image'])) {
        $metas = ($metas['twitter:image']);
        $maplink = str_replace('FullMap.png', 'Thumbnail.png', $metas);
    } else {
        $maplink = "html/img/map/rustgenerate.webp";
    }
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
    <div class="I" style="<?php if($type=="valheim"){echo "min-width:330px";}?>">
        <?php
        if ($type == "arkse") {
            echo '<img class="map" src="'.$maplink.'" alt="'.$maplink.'">';
            echo '<div style="text-align:left"><span style="font-weight: 500;">Map:</span> '.$map.'</div>';
            echo '<div style="text-align:left"><span style="font-weight: 500;">'.$language[$lang][11]."</span>".$ingameday.'</div>';
        } elseif ($type == "valheim") {
            echo('<a class="twitter-timeline" data-chrome="nofooter noheader noscrollbar" id="twitter-timeline" data-width="330" data-height="275" data-dnt="true" data-theme="dark" href="https://twitter.com/Valheimgame">Tweets by Valheimgame</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>');
        } elseif ($type == "csgo") {
            echo '<img class="map" src="'.$maplink.'" alt="'.$maplink.'">';
            echo '<div style="text-align:left;max-width: 234px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Map: '.$mapname.'</div>';
        }
        elseif ($type == "vrising") {
            echo '<img class="map" src="'.$maplink.'" alt="'.$maplink.'">';
            echo '<div style="text-align:left"><span style="font-weight: 500;">Map:</span> '.$map.'</div>';
            echo '<div style="text-align:left"><span style="font-weight: 500;">'.$language[$lang][11]."</span>".$ingameday.'</div>';
        }
        elseif ($type == "rust") {
            echo '<a href="'.$mapurl.'" target="_blank" rel="noopener noreferrer"><img class="map" src="'.$maplink.'" alt="'.$maplink.'"></a>';
            echo '<div style="text-align:left"><span style="font-weight: 500;">Map:</span> '.$map.'</div>';
            echo '<div style="text-align:left"><span style="font-weight: 500;">Uptime:</span> '.$rustuptime.'<br></div>';
        }
        ?>
    </div>
    <!-- DETAILS -->
    <div class="II <?php if($type=="csgo"){echo'csgo';}elseif($type=="minecraft"&&$qport == 0) {echo "flex";}?>">
        <?php
        if ($type == "arkse") {
            echo "<span style='font-weight: 500;'>System:</span> $Os<br>";
            echo "<span style='font-weight: 500;'>Cluster ID:</span> $clusterid<br>";
            if ($pve == "true") {echo "<span style='font-weight: 500;'>PVE:</span> True<br>";} else {echo "<span style='font-weight: 500;'>PVP:</span> True<br>";}
            if ($password == "true" ) {echo "<span style='font-weight: 500;'>".$language[$lang][12].":</span> True<br>";} else {echo "<span style='font-weight: 500;'>".$language[$lang][12].":</span> False<br>";}
            if ($battleye == "true") {echo "<span style='font-weight: 500;'>Battleye:</span> True<br>";} else {echo "<span style='font-weight: 500;'>Battleye:</span> False<br>";}
            if ($hasmods) {
                echo "<span style='font-weight: 500;'>Mods:</span><br>";
                foreach ($mods as $mod) {
                    echo $modlink . $mod . '" target="_blank">';
                    $convertedmod = convertmodlistark($mod);
                    if (!empty($convertedmod)) {
                        echo $convertedmod;
                    } else {
                        // If mod is not in modlist, get the mod from the steamcommunity website and store the mod in the modlist file
                        $convertedmod = substr(get_title("https://steamcommunity.com/sharedfiles/filedetails/?id=$mod"), 16);
                        echo $convertedmod;
                        $convertedmod = str_replace("'", "\'", $convertedmod);
                        $convertedmod = '$modlist[\'ArkModName\']'."[$mod] = '$convertedmod';";
                        file_put_contents("html/type/arkse/modlist.php", $convertedmod . "\n", FILE_APPEND);

                    }
                    echo "</a><br>";
                }
            }
        } elseif ($type == "valheim") {
            echo "<span style='font-weight: 500;'>System:</span> $Os<br>";
            echo "<span style='font-weight: 500;'>Map:</span> $map<br>";
            echo "<span style='font-weight: 500;'>Max Players:</span> $maxplayers<br>";
            echo "<span style='font-weight: 500;'>Query Port:</span> $qport<br>";
            echo "<span style='font-weight: 500;'>Steam Page:</span> <a href='https://store.steampowered.com/appv/892970/Valheim/' target='_blank' rel='noopener noreferrer'>Link</a><br>";
            echo "<span style='font-weight: 500;'>Wiki:</span> <a href='https://valheim.fandom.com/wiki/Valheim_Wiki' target='_blank' rel='noopener noreferrer'>Link</a><br>";
            echo "<span style='font-weight: 500;'>Website:</span> <a href='https://www.valheimgame.com' target='_blank' rel='noopener noreferrer'>Link</a><br>";
        } elseif ($type == "csgo") {
            echo "<span style='font-weight: 500;'>System:</span> $Os<br>";
            echo "<span style='font-weight: 500;'>Version:</span> $version<br>";
            if ($password == "true" ?? false) {echo "<span style='font-weight: 500;'>".$language[$lang][12]."</span>: True<br>";} else {echo "<span style='font-weight: 500;'>".$language[$lang][12]."</span>: False<br>";}
            if (isset($serverstatus->rules)) {
                echo "<span style='font-weight: 500;'>Server Settings:<br></span>";
                echo "<div style='height: 100%; width: 100%;'>";
                echo "<table><thead><tr style='border-bottom: solid;border-width: 2px;'><th>Name</th><th style='text-align: right'>Value</th></tr></thead><tbody>";
                // Print every rulename and value inside the table
                foreach ($csgorule as $rule) {
                    echo "<tr><td class='name'>";
                    print_r($rule[1]);
                    echo "</td><td class='value'>";
                    print_r($rule[2]);
                    echo "</td></tr>";
                }
                echo "</tbody></table></div>";
            }
        } elseif ($type == "vrising") {
            echo "<span style='font-weight: 500;'>System:</span> $Os<br>";
            echo "<span style='font-weight: 500;'>".$language[$lang][12].":</span> $password<br>";
            echo "<span style='font-weight: 500;'>Bloodbound:</span> $bloodbound<br>";
            echo "<div style='max-width:300px'><span style='font-weight: 500;'>Tags:</span> $GameTags<br></div>";
            echo "<div style='width:-webkit-min-content;'><span style='font-weight: 500;'>Description:</span> $description<br></div>";
        } elseif ($type == "rust") {
            echo "<span style='font-weight: 500;'>System:</span> $Os<br>";
            echo "<span style='font-weight: 500;'>".$language[$lang][12].":</span> $password<br>";
            echo "<span style='font-weight: 500;'>Website:</span><a href='$rustwebsite'  target='_blank' rel='noopener noreferrer'> $rustwebsite</a><br>";
            echo "<span style='font-weight: 500;'>PVP:</span> $pvp<br>";
            echo "<span style='font-weight: 500;'>FPS:</span> $rustfps<br>";
            echo "<div style='max-width:300px'><span style='font-weight: 500;'>Tags:</span> $GameTags<br></div>";
            echo "<div style='width:300px;'><span style='font-weight: 500;'>Description:</span>";
            foreach ($rustdesc as $description) {
                echo "$description<br>";
            }
            echo "</div>";
        } elseif ($type == "minecraft") {
            if ($qport == 0) {
                echo '<div style="margin:auto">'.$language[$lang][13].'</div>';
            } else {
                echo "<div class='mcheads'>";
                foreach ($serverstatus->players as $player) {
                    $crafatar = "https://crafatar.com/avatars/". minecraftcache($player);
                    echo "<div class='mchead'><img src='$crafatar' alt='Skin from crafatar'><div class='name'>$player</div></div>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>
    <div class="movediv<?php if ($type == "csgo" && isset($serverstatus->rules)){echo " csmovedivhide";} elseif ($type == "minecraft"){echo " mcmovedivhide";}?>"></div>
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
        <div class="scroll">
            <?php
            // Display players for ARK, Csgo, Vrising
            if ($type == "arkse" || $type == "csgo" || $type == "vrising" || $type == "rust") {
                foreach ($serverstatus->players ?? (array) "0" as $player) {
                    if(!strlen($player->Name ?? ''))
                        continue;
                    echo '<h5 class="dark">';
                    $rawtimeconv = $player->Time;
                    $rawtimeconv = round($rawtimeconv);
                    $output = sprintf('%02dh:%02dm:%02ds', ($rawtimeconv/ 3600),($rawtimeconv/ 60 % 60), $rawtimeconv% 60);
                    echo $player->Name." ".$language[$lang][5]." ".$output;
                    echo "</h5><br>";
                }
                // Display mods for Minecaft
            } elseif ($type == "minecraft") {
                if (!empty($serverstatus->modinfo->modList)) {
                    foreach ($serverstatus->modinfo->modList as $mods) {
                        echo $mods->modid."<br>";
                    }
                } else {
                    echo $language[$lang][10];
                }
            }

            ?>
        </div>
    </div>
    <div class="V">
        <div class="vchartdiv" style='<?php if($type == "valheim"){echo "width:320px";}?>'>
            <div class="servername">
                <p href="#"><?php if (!empty($motd)) {echo $motd;} else {echo $title;}?></p>
            </div>
            <div class="connectlink">
                <a href="<?php echo $connectlink ?>>" title="Connect to server"><?php echo $ip . ":" . $gport ?></a>
            </div>
            <div class="items flex">
                <div class="flex width50">
                    <svg width="100%" height="100%" viewBox="0 0 123 100" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                            <g>
                                <path d="M73.12,0C79.85,0 86.28,1.34 92.15,3.77C98.24,6.29 103.72,9.99 108.31,14.58C112.89,19.16 116.59,24.64 119.11,30.75C121.54,36.62 122.88,43.05 122.88,49.77C122.88,56.5 121.54,62.93 119.11,68.8C116.59,74.89 112.89,80.37 108.3,84.96C103.72,89.54 98.24,93.24 92.13,95.76C86.26,98.19 79.83,99.53 73.11,99.53C66.38,99.53 59.95,98.19 54.08,95.76C47.99,93.24 42.51,89.54 37.93,84.96L37.92,84.95C33.33,80.36 29.64,74.88 27.12,68.8C26.34,66.91 25.67,64.97 25.12,62.98C26.16,63.08 27.22,63.13 28.29,63.13C30.32,63.13 32.3,62.95 34.23,62.6C34.55,63.56 34.9,64.51 35.28,65.44C37.35,70.44 40.39,74.94 44.17,78.72C47.95,82.5 52.46,85.54 57.45,87.61C62.26,89.6 67.55,90.71 73.11,90.71C78.67,90.71 83.95,89.61 88.77,87.61C93.77,85.54 98.27,82.5 102.05,78.72C105.83,74.94 108.87,70.43 110.94,65.44C112.93,60.63 114.04,55.34 114.04,49.78C114.04,44.22 112.94,38.94 110.94,34.12C108.87,29.12 105.83,24.62 102.05,20.84C98.27,17.06 93.76,14.02 88.77,11.95C83.96,9.96 78.67,8.85 73.11,8.85C67.55,8.85 62.27,9.95 57.45,11.95C57.02,12.13 56.59,12.32 56.17,12.51C54.53,9.93 52.55,7.59 50.28,5.56C51.52,4.92 52.79,4.33 54.08,3.79C59.97,1.34 66.39,0 73.12,0ZM67.41,26.11C67.41,24.89 67.91,23.79 68.7,22.99C69.5,22.19 70.6,21.7 71.82,21.7C73.04,21.7 74.14,22.19 74.94,22.99C75.74,23.79 76.23,24.89 76.23,26.11L76.23,49.33L93.58,59.62C94.62,60.24 95.32,61.23 95.6,62.32C95.88,63.41 95.75,64.61 95.13,65.65L95.13,65.66C94.51,66.7 93.52,67.4 92.43,67.68C91.34,67.96 90.14,67.83 89.1,67.21L89.09,67.21L69.68,55.7C69.01,55.33 68.46,54.79 68.06,54.15C67.65,53.48 67.41,52.69 67.41,51.85L67.41,26.11Z" style="fill:white;fill-rule:nonzero;"/>
                                <path d="M26.98,2.64C41.88,2.64 53.96,14.72 53.96,29.62C53.96,44.52 41.88,56.6 26.98,56.6C12.08,56.6 0,44.52 0,29.62C0,14.72 12.08,2.64 26.98,2.64ZM26.98,13.72L41.46,31.62L32.47,31.62L32.47,41.14L21.49,41.14L21.49,31.62L12.5,31.62L26.98,13.72Z" style="fill:rgb(107,190,102);"/>
                            </g>
                        </svg>
                    <p><?php echo $uptimebanner ?></p>
                </div>
                <div class="flex width50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                        <g>
                            <path d="M24 24c4.42 0 8-3.59 8-8 0-4.42-3.58-8-8-8s-8 3.58-8 8c0 4.41 3.58 8 8 8zm0 4c-5.33 0-16 2.67-16 8v4h32v-4c0-5.33-10.67-8-16-8z"  style="fill:white;"/>
                            <path d="M0 0h48v48h-48z" fill="none"/>
                        </g>
                    </svg>
                    <p><?php echo $countplayers . '/' . $maxplayers;?></p>
                </div>
            </div>
            <p>History</p>
            <div class="canvasparent">
                <canvas id="Chart<?php echo $ServerID ?>" class="vchart" width="190" height="120"></canvas>
            </div>
            <script>Chart.defaults.color = 'white';let xLabels<?php echo $ServerID ?> = ['60','50','40','30','20','10','now'];let xValues<?php echo $ServerID ?> = <?php echo "[$lastplayers[0], $lastplayers[1], $lastplayers[2], $lastplayers[3], $lastplayers[4], $lastplayers[5], $lastplayers[6]];"; ?>new Chart("Chart<?php echo $ServerID ?>", {type: "line", data: {labels: xLabels<?php echo $ServerID ?>, datasets: [{label: "Players", data: xValues<?php echo $ServerID ?>, backgroundColor: "white", borderColor: "red", color: "white", borderWidth: 2, pointBorderWidth: 1.5, pointRadius: 2, fill: false, tension: 0.4, pointBorderColor: "white",}]}, options: {scales: {x: {grid: {display:false}, ticks: {display: true}}, y: {<?php if ($lastplayers[0] < 200) {echo "beginAtZero: true, ";} if ($lastplayers[0] < 200 && $maxplayers > 500) {$maxchart = $lastplayers[0] + 30; echo "max: $maxchart, ";}?>grid:{display:true, color: 'rgb(70,70,70)',},}}, responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false,}}}});</script>
        </div>
    </div>
</div>

