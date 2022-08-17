<?php
require_once 'html/config.php';
if (file_exists("query/cron/cache/minecraft.php")) {
    include 'query/cron/cache/minecraft.php';
}
require "html/tailcustom.php";
require "functions.php";
if (!isset($install)) {
    header("Location: html/install.php");
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
                    <label for="username">Username:</label><input id="username" class="input" name="username" type="text" minlength="4" maxlength="15" placeholder="xxxxx" autocomplete="off" required="required">
                    <label for="password">Password:</label><input id="password" class="input" name="password" type="password" minlength="8" placeholder="xxxxxxxxxxxx" autocomplete="off" required="required">
                    <div style="display:flex;justify-content: flex-end;-webkit-align-items: center;align-items: center;">
                        <?php
                        session_start();
                        if (!empty($_SESSION['error'])) {
                            $error = $_SESSION['error'];
                        }
                        if (isset($_GET['login'])) {
                            echo "<style>#login-popup{display:flex};</style>";
                        }
                        if (isset($error['specialuser'])){echo $error['specialuser'];}elseif (isset($error['nomatch'])){echo $error['nomatch'];}else {echo "<a class='button white-hover' href='users/register.php'>Register</a>";}
                        unset($_SESSION['error']);
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
        // DB Connection
        $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        if (!isset($username)) {
            $username = "public";
        }
        $sql = "SELECT server FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $serverjson = json_decode($row["server"]);
            }
        } else {
            echo "Query failed! Talk to your server administrator.";
        }
        $idimp = implode(',', $serverjson);
        // Get server information from database
        $sql = "SELECT ID, type FROM serverconfig WHERE ID IN ({$idimp})";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $ID = $row['ID'];
                $typearr[$ID] = $row['type'];

            }
        }
        if (isset($serverjson)) {
            foreach ($serverjson as $ServerID) {
                if (!$ServerID) {
                    continue;
                }
                $type = $typearr[$ServerID];
                $sidscript[] = $ServerID;
                require 'server.php';
            }
            $conn->close();
        }
        if ($username == "public") {
            echo "<div class='white-inftext'>" . $language[$lang][9] . "</div>";
        }
        ?>
        <p class="countdown"><?php echo $language[$lang][13]?> <span id="countdown"></span>s.</p>
        <div><button class="countdownbtn" type="button" onclick="callLoadData()">Refresh Servers</button></div>
        <script>
            function callLoadData() {
                if (typeof downloadTimer !== 'undefined') {
                    clearInterval(downloadTimer);
                }
                countdown(30);
                let serverid = JSON.parse('<?php echo json_encode($sidscript);?>');
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