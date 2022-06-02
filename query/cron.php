<?php
// Import Query for all game server
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\SourceQuery\SourceQuery;
use AunePVP\jsonconversion;
use Spirit55555\Minecraft\MinecraftColors;

require '../html/config.php';
require '../html/type/minecraft/jsonconversion.php';
require '../html/type/minecraft/minecraftcolor.php';
require __DIR__ . '/SourceQuery/bootstrap.php';
require __DIR__ . '/minecraft/src/MinecraftPing.php';
require __DIR__ . '/minecraft/src/MinecraftPingException.php';
require __DIR__ . '/minecraft/src/MinecraftQuery.php';
require __DIR__ . '/minecraft/src/MinecraftQueryException.php';
const SQ_TIMEOUT = 1;
const SQ_ENGINE = SourceQuery::SOURCE;
// Set timezone and get current time
date_default_timezone_set('Europe/Berlin');
$timezone = date_default_timezone_get();
$time = date('H-i', time());
// Function to delete the first line of the file. This is to prevent that the files get bigger and bigger over time.
function read_and_delete_first_line($filename) {
    $file = file($filename);
    $output = $file[0];
    unset($file[0]);
    file_put_contents($filename, $file);
}
function calc_uptime($filename, $lines, $id) {
    $handle = fopen($filename, "r");
    if ($handle) {
        $uptime = 0;
        while (($line = fgets($handle)) !== false) {
            $line = json_decode($line);
            if (isset($line->status)) {
                $uptime = $uptime + $line->status;
            }
        }
        fclose($handle);
        $uptime = ($uptime/$lines) * 100;
        $uptime = (round($uptime,3));
        file_put_contents('cron/uptime/'.$id, $uptime);
        
    }
}
function uploadname($namequery, $id) {
    global $DB_SERVER;
    global $DB_USERNAME;
    global $DB_PASSWORD;
    global $DB_NAME;
    $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    $sql = "UPDATE serverconfig SET Name='$namequery' WHERE ID='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}
// Connect to database and get required data of server
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
$sql = "SELECT ID, IP, type, QueryPort, GamePort, RconPort, Name FROM serverconfig WHERE enabled='1'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        unset($data);
        unset($queryresult);
        unset($countplayers);
        unset($file);
        $queryresult = (array)"";
        $id = $row["ID"];
        $ip = $row["IP"];
        $type = $row["type"];
        $qport = $row["QueryPort"];
        $gport = $row["GamePort"];
        $rport = $row["RconPort"];
        $name = $row["Name"];
        $file = 'cron/' . $id . '.json';
        //
        # Source Query
        switch ($type) {
            case "csgo":
                    $Query = new SourceQuery();
                    try {
                        $Query->Connect($ip, $qport, SQ_TIMEOUT, SQ_ENGINE);
                        $csgoruleresult = $Query->GetRules();
                        $data["csgorules"] = "1";
                    }
                    catch( Exception $e )
                    {
                        $data["csgorules"] = "0";
                    }
                    finally {
                        $Query->Disconnect();
                    }
            case "valheim":
            case "protocol-valve":
            case "arkse":
                $Query = new SourceQuery();
                try {
                    $Query->Connect($ip, $qport, SQ_TIMEOUT, SQ_ENGINE);
                    switch ($type) {
                        case "arkse":
                            $queryresult["info"] = $Query->GetInfo();
                            $queryresult["players"] = $Query->GetPlayers();
                            if (!isset($countplayers)) {$countplayers = 0;}
                            foreach ($queryresult["players"] as $player) {
                                if (!strlen($player['Name']))
                                    continue;
                                $countplayers = $countplayers + 1;
                            }
                            break;
                        case "valheim":
                        case "csgo":
                            $queryresult["info"] = $Query->GetInfo();
                            $countplayers = $queryresult["info"]["Players"];
                            break;
                    }
                    $namequery = $queryresult['info']['HostName'];
                    // Check if host name changed or is in database and if the name is not in the, call a function to update the name
                    if ($name != $namequery) {
                        uploadname($namequery, $id);
                    }
                    // Store necessary data in an array
                    $data["time"] = $time;
                    $data["status"] = 1;
                    $data["players"] = $countplayers;
                    // Write the data as json in the file server/$id.json
                    file_put_contents($file, json_encode($data) . "\n", FILE_APPEND);
                    $lines = count(file($file));
                    if ($lines >  1100) {
                        read_and_delete_first_line($file);
                    }
                      calc_uptime($file, $lines, $id);
                } // Check if an erropr occured
                catch (Exception $e) {
                    // If there's an error display server as offline and show players 0
                    echo $e->getMessage();
                    file_put_contents($file, '{"time":"' . $time . '","status":0,"players":0}' . "\n", FILE_APPEND);
                    $lines = count(file($file));
                    if ($lines >  1100) {
                        read_and_delete_first_line($file);
                    }
                      calc_uptime($file, $lines, $id);
                } // Disconnect from Server
                finally {
                    $Query->Disconnect();
                }
                break;
            //
            # Minecraft query
            case "minecraft":
                // If queryprotocol is not an option
                if ($qport == 0) {
                    try {
                        $Query = new MinecraftPing($ip, $gport);
                        $queryresult = $Query->Query();
                        $queryresultobj = json_decode(json_encode($queryresult));

                        //Get name and if necessary, convert name to plain text
                        $namequery = "";
                        if (is_string($queryresultobj->description)) {
                            $namequery = MinecraftColors::clean($queryresultobj->description);
                        } elseif (is_array($queryresultobj->description->extra)) {
                            foreach ($queryresultobj->description->extra as $extra) {
                                $namequery .= $extra->text;
                            }
                        } elseif (isset($queryresultobj->description->text)) {
                            $namequery = $queryresultobj->description->text;
                        }
                        // Check if host name changed or is in database and if the name is not in the, call a function to update the name
                        if ($name != $namequery) {
                            uploadname($namequery, $id);
                        }
                        // Store necessary data in an array
                        $data["time"] = $time;
                        $data["status"] = 1;
                        $data["players"] = $queryresult['players']['online'];
                        // Encode array in json
                        $queryresult = json_encode($queryresult);
                        // Write the data as json in the file server/$id.json
                        file_put_contents('cron/' . $id . '.json', json_encode($data) . "\n", FILE_APPEND);
                        $lines = count(file($file));
                        if ($lines >  1100) {
                            read_and_delete_first_line($file);
                        }
                          calc_uptime($file, $lines, $id);
                    } // Check if an erropr occured
                    catch (MinecraftPingException $e) {
                        echo $e->getMessage();
                        file_put_contents('cron/' . $id . '.json', '{"time":' . $time . ',"status":0,"players":0}' . "\n", FILE_APPEND);
                        $lines = count(file($file));
                        if ($lines >  1100) {
                            read_and_delete_first_line($file);
                        }
                          calc_uptime($file, $lines, $id);
                    } // Disconnect from Server
                    finally {
                        $Query?->Close();
                    }
                } // If queryprotocol is an option
                else {
                    $Query = new MinecraftQuery();
                    try {
                        $Query->Connect($ip, $qport);
                        $queryresult["info"] = ($Query->GetInfo());
                        $queryresultobj = json_decode(json_encode($queryresult));
                        // Convert name to clean text
                        $titleraw = $queryresultobj->info->HostName;
                        $titlestr = (str_replace("?", "&", $titleraw));
                        $namequery = MinecraftColors::clean($titlestr);
                        // If name is different from db upload it to db
                        if ($name != $namequery) {
                            uploadname($namequery, $id);
                        }
                        // Store necessary data in an array
                        $data["time"] = $time;
                        $data["status"] = 1;
                        $data["players"] = $queryresult["info"]["Players"];
                        // Encode array in json
                        $queryresult = json_encode($queryresult);
                        // Write the data as json in the file server/$id.json
                        file_put_contents('cron/' . $id . '.json', json_encode($data) . "\n", FILE_APPEND);
                        $lines = count(file($file));
                        if ($lines >  1100) {
                            read_and_delete_first_line($file);
                        }
                          calc_uptime($file, $lines, $id);
                    } // Check if an erropr occured
                    catch (MinecraftQueryException $e) {
                        echo $e->getMessage();
                        file_put_contents('cron/' . $id . '.json', '{"time":"' . $time . '","status":0,"players":0}' . "\n", FILE_APPEND);
                        $lines = count(file($file));
                        if ($lines >  1100) {
                            read_and_delete_first_line($file);
                        }
                          calc_uptime($file, $lines, $id);
                    }
                }
                break;
        }
    }
}
$conn->close();