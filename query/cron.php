<?php
// Import Query for all game server
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\SourceQuery\SourceQuery;
use Spirit55555\Minecraft\MinecraftColors;

// Paths are relative to this file, so the cron job works from every working directory
chdir(__DIR__);
require __DIR__ . '/../html/config.php';
require_once __DIR__ . '/../html/lib.php';
require_once __DIR__ . '/libraries.php';
const HISTORY_MAX_LINES = 1100;
// Set timezone and get current time
date_default_timezone_set('Europe/Berlin');
$time = date('H-i', time());

// Delete the oldest lines of the file, so the history files don't grow forever.
function trim_history($filename) {
    $file = file($filename);
    if ($file === false || count($file) <= HISTORY_MAX_LINES) {
        return;
    }
    file_put_contents($filename, array_slice($file, -HISTORY_MAX_LINES), LOCK_EX);
}
function count_lines($filename) {
    $file = file($filename);
    return $file === false ? 0 : count($file);
}
// Average status over all stored lines plus the current status
function calc_uptime($filename, $lines, $status) {
    $lines++;
    $uptime = $status;
    $handle = fopen($filename, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $line = json_decode($line);
            if (isset($line->status)) {
                $uptime = $uptime + $line->status;
            }
        }
        fclose($handle);
    }
    return round(($uptime / $lines) * 100, 3);
}
// Append one history entry (online or offline) and trim the file
function record_status($file, $time, $status, $players, array $extra = []) {
    $lines = count_lines($file);
    $data = $extra;
    $data["time"] = $time;
    $data["status"] = $status;
    $data['uptime'] = calc_uptime($file, $lines, $status);
    $data["players"] = $status ? (int)$players : 0;
    file_put_contents($file, json_encode($data) . "\n", FILE_APPEND | LOCK_EX);
    trim_history($file);
}
// Store the host name in the database if it changed
function uploadname($namequery, $name, $id) {
    if ($namequery === '' || $namequery == $name) {
        return;
    }
    db_query("UPDATE serverconfig SET Name = ? WHERE ID = ?", [$namequery, (int)$id]);
    echo "Record updated successfully\n";
}
// Clean Minecraft color codes and return plain text
function minecraft_plain($text) {
    return html_entity_decode(MinecraftColors::clean((string)$text), ENT_QUOTES, 'UTF-8');
}

// Get required data of all servers
foreach (db_query("SELECT ID, IP, type, QueryPort, GamePort, RconPort, Name FROM serverconfig") as $row) {
    $id = (int)$row["ID"];
    $ip = $row["IP"];
    $type = $row["type"];
    $qport = (int)$row["QueryPort"];
    $gport = (int)$row["GamePort"];
    $name = $row["Name"];
    $file = __DIR__ . '/cron/' . $id . '.json';
    if (!file_exists($file)) {
        touch($file);
    }
    $extra = [];
    $Query = null;

    if (in_array($type, GSQ_SOURCE_TYPES, true)) {
        # Source Query
        if ($type === "csgo") {
            // Check if the server accepts A2S_RULES requests (used by reload.php)
            $Query = new SourceQuery();
            try {
                $Query->Connect($ip, $qport, SQ_TIMEOUT, SQ_ENGINE);
                $Query->GetRules();
                $extra["csgorules"] = "1";
            } catch (Exception $e) {
                $extra["csgorules"] = "0";
            } finally {
                $Query->Disconnect();
            }
        }
        $Query = new SourceQuery();
        try {
            $Query->Connect($ip, $qport, SQ_TIMEOUT, SQ_ENGINE);
            $info = $Query->GetInfo();
            if ($type === "arkse") {
                // ARK reports wrong player numbers in A2S_INFO, so count the named players
                $countplayers = 0;
                foreach ($Query->GetPlayers() as $player) {
                    if (strlen($player['Name'])) {
                        $countplayers++;
                    }
                }
            } else {
                $countplayers = $info["Players"] ?? 0;
            }
            uploadname((string)($info['HostName'] ?? ''), $name, $id);
            record_status($file, $time, 1, $countplayers, $extra);
        } catch (Exception $e) {
            // If there's an error display server as offline and show players 0
            record_status($file, $time, 0, 0, $extra);
        } finally {
            $Query->Disconnect();
        }
    } elseif ($type === "minecraft") {
        if ($qport == 0) {
            // If queryprotocol is not an option
            try {
                $Query = new MinecraftPing($ip, $gport);
                $queryresult = $Query->Query();
                $queryresultobj = json_decode(json_encode($queryresult));
                // Get name and if necessary, convert name to plain text
                $namequery = "";
                $description = $queryresultobj->description ?? '';
                if (is_string($description)) {
                    $namequery = minecraft_plain($description);
                } elseif (is_array($description->extra ?? null)) {
                    foreach ($description->extra as $extrapart) {
                        $namequery .= $extrapart->text ?? '';
                    }
                } elseif (isset($description->text)) {
                    $namequery = $description->text;
                }
                uploadname($namequery, $name, $id);
                record_status($file, $time, 1, $queryresult['players']['online'] ?? 0);
            } catch (MinecraftPingException $e) {
                record_status($file, $time, 0, 0);
            } finally {
                $Query?->Close();
            }
        } else {
            // If queryprotocol is an option
            $Query = new MinecraftQuery();
            try {
                $Query->Connect($ip, $qport);
                $info = $Query->GetInfo();
                uploadname(minecraft_plain(str_replace("?", "&", (string)($info['HostName'] ?? ''))), $name, $id);
                record_status($file, $time, 1, $info["Players"] ?? 0);
            } catch (MinecraftQueryException $e) {
                record_status($file, $time, 0, 0);
            }
        }
    }
}
// Check game-server version
$arkversion = @file_get_contents("http://arkdedicated.com/version");
