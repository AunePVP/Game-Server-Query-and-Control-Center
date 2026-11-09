<?php
require_once 'html/config.php';
require "html/tailcustom.php";
require "functions.php";
if (!isset($install)) {
    redirect("html/install.php");
}
gsq_session_start();
$username = current_user();
// Read (and clear) login errors and create the CSRF token before the session is closed
$error = $_SESSION['error'] ?? [];
unset($_SESSION['error']);
$csrf = csrf_field();
session_write_close();
$sprite = 'bottts';
$seed = $username;
$serverjson = [];
$row = db_row("SELECT server, sprite, seed FROM users WHERE username = ?", [$username]);
if ($row) {
    $serverjson = json_decode($row['server'], true) ?: [];
    $sprite = $row['sprite'] ?? $sprite;
    $seed = $row['seed'] ?? $seed;
}
if (is_admin($username)) {
    $seed = "d";
    $sprite = "bottts";
}
?>
    <!doctype html>
    <html lang="<?php
    echo $lang ?>">
    <?php
    include('html/head.php') ?>
    <body onload="callLoadData()">
    <?php
    include('html/nav.php'); ?>
    <div id="login-popup">
        <div id="lpopb" onclick="exitpopuplogin()" style="z-index:5"></div>
        <div class="centerdiv">
            <div class="padding15">
                <form method="post" action="users/login.php" style="margin:0">
                    <?php echo $csrf ?>
                    <label for="username">Username:</label><input id="username" class="input" name="username" type="text" minlength="4" maxlength="15" placeholder="xxxxx" autocomplete="off" required="required">
                    <label for="password">Password:</label><input id="password" class="input" name="password" type="password" minlength="8" placeholder="xxxxxxxxxxxx" autocomplete="off" required="required">
                    <div style="display:flex;justify-content: flex-end;-webkit-align-items: center;align-items: center;">
                        <?php
                        if (isset($_GET['login'])) {
                            echo "<style>#login-popup{display:flex};</style>";
                        }
                        if (isset($error['specialuser'])) {
                            echo h($error['specialuser']);
                        } elseif (isset($error['nomatch'])) {
                            echo h($error['nomatch']);
                        } elseif (!empty($register)) {
                            echo "<a class='button white-hover' href='users/register.php'>Register</a>";
                        }
                        ?>
                        <input class="button" type="submit" name="login" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <table class="server_list_table">
            <tbody>
                <tr class="server_list_table_top">
                    <th class="status_cell">
                        <?php echo $language[$lang][6] ?>
                    </th>
                    <th class="connectlink_cell">
                        <?php echo $language[$lang][7] ?>
                    </th>
                    <th class="servername_cell">
                        <?php echo $language[$lang][8] ?>
                    </th>
                    <th class="players_cell">
                        <?php echo $language[$lang][4] ?>
                    </th>
                    <th class="img-cell">
                        <div></div>
                    </th>
                </tr>
            </tbody>
        </table>
        <?php
        $serverids = array_values(array_filter(array_map('intval', $serverjson)));
        $sidscript = [];
        $typearr = [];
        if (!$row) {
            echo "Query failed! Talk to your server administrator.";
        }
        if ($serverids) {
            // Get server information from database
            $placeholders = implode(',', array_fill(0, count($serverids), '?'));
            foreach (db_query("SELECT ID, type FROM serverconfig WHERE ID IN ($placeholders)", $serverids) as $srv) {
                $typearr[(int)$srv['ID']] = $srv['type'];
            }
        }
        foreach ($serverids as $ServerID) {
            if (!isset($typearr[$ServerID])) {
                continue;
            }
            $type = $typearr[$ServerID];
            $sidscript[] = $ServerID;
            require 'server.php';
        }
        if ($username == "public") {
            echo "<div class='white-inftext'>" . $language[$lang][9] . "</div>";
        }
        ?>
        <p class="countdown"><?php echo $language[$lang][13]?> <span id="countdown"></span>s.</p>
        <div style="padding-bottom: 15px;"><button class="countdownbtn" type="button" onclick="callLoadData()">Refresh Servers</button></div>
        <script>
            function callLoadData() {
                if (typeof downloadTimer !== 'undefined') {
                    clearInterval(downloadTimer);
                }
                countdown(30);
                let serverid = <?php echo json_encode($sidscript);?>;
                for (const value in serverid) {
                    let modlink = `${serverid[value]}`;
                    LoadData(modlink);
                }
            }
            function popuplogin() {
                document.getElementById("login-popup").style.display = "flex";
            }
            function exitpopuplogin() {
                document.getElementById("login-popup").style.display = "none";
            }
        </script>
        <script src="html/reload.js"></script>
    </div>
    </body>
    </html>