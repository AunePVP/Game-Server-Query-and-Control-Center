<section id="<?php echo "server_".$ServerID?>">
    <details>
        <summary>
            <table class="server_list_table">
                <tbody>
                    <tr class="server_onl">
                        <td class="status_cell">
                            <span class="status_icon_onl" style="<?php echo $statusfarbe ?>"></span>
                            <div class="status-letter-online"></div>
                        </td>
                        <td title="GAME LINK" class="connectlink_cell"><a href="<?php echo $connectlink ?>"><?php echo $ip . "<span>:" . $gport ."</span>"?></a></td>
                        <td title="<?php if (isset($title)) {echo $title;} ?>" class="servername_cell">
                            <div class="servername_nolink"><?php if (isset($title)) {echo $title;} ?></div></td>
                        <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric"><?php echo $countplayers . '/' . $maxplayers;?></span></div></div></td>
                        <td class="img-cell"><img src="<?php echo $img ?>" width="80px" height="80px" style="float:right;margin-right: 8px;" alt="<?php echo $img ?>"></td>
                    </tr>
                </tbody>
            </table>
        </summary>
        <?php
        include('html/tabs.php');
        ?>
    </details>
</section>