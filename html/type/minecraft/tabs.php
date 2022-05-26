<?php
include './html/langconf.php';
?>
<!DOCTYPE html>
<table>
    <tr>
        <td>Maria Anders</td>
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
