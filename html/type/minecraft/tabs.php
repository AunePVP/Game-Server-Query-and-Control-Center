<?php
include './html/langconf.php';
?>
<!DOCTYPE html>
<table>
    <tr>
        <td style="display:flex;padding: 5px;">
            <div style="text-align: left;">
                <div class="motd">
                    <?php echo $motd ?>
                </div>
                <div>Version: <?php echo $version ?></div>
            </div>
        </td>
        <td class="width35 mcquery">
            <?php
            if ($qport == 0):
                echo "Query is disabled on this server.";
            else:
                ?>
                <div>Query is enabled</div>
            <?php
            endif;
            ?>
        </td>
    </tr>
</table>
