<section id="<?php echo "server_".$ServerID?>">
    <details>
        <summary>
            <table class="server_list_table">
                <tbody>
                    <tr class="server_onl">
                        <td class="status_cell">
                            <span class="status_icon_onl"></span>
                            <div class="status-letter-online"></div>
                        </td>
                        <td title="GAME LINK" class="connectlink_cell"><a><span></span></a></td>
                        <td class="servername_cell">
                            <div class="servername_nolink">Loading...</div>
                        </td>
                        <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric">0/0</span></div></div></td>
                        <td class="img-cell"><img width="80px" height="80px" style="float:right;margin-right: 8px;"></td>
                    </tr>
                </tbody>
            </table>
        </summary>
        <?php
        include('html/tabs.php');
        ?>
    </details>
</section>