<?php
include './html/langconf.php';
?>
<!DOCTYPE html>
<table>
    <tr>
        <td style="padding: 5px 0 5px 5px;vertical-align: top;"$language['en'][13] = "Query is disabled on this server.";>
            <div style="text-align: left;">
                <div class="motd">
                    <?php echo $motd ?>
                </div>
                <div class="mctabcontent">Version: <?php echo $version ?></div>
            </div>
        </td>
        <td style="width: 190px;">
            <div style="text-align: left;">
                <div class="mods">
                    <h1>Mods</h1>
                    <div class="modcontent">
                        <div class="player-scroll">
                            <?php
                            if (!empty($serverstatus->modinfo->modList)) {
                                foreach ($serverstatus->modinfo->modList as $mods) {
                                        echo $mods->modid."<br>";
                                }
                            } else {
                                echo $language[$lang][10];
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td class="width35 mcquery">
            <?php
            if ($qport == 0):
                echo $language[$lang][13];
            else:
                ?>
                <div>Query is enabled</div>
            <?php
            endif;
            ?>
        </td>
    </tr>
</table>
