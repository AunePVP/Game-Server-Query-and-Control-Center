<?php
require_once __DIR__ . '/html/lib.php';

const GSQ_MINECRAFT_CACHE = __DIR__ . '/query/cron/cache/minecraft.json';

/**
 * Resolve a Minecraft player name to its UUID and cache the result as JSON.
 * Player names come from the queried game server and are therefore untrusted.
 */
function minecraftcache($username) {
    $username = (string)$username;
    // Valid Java edition names: 3-16 characters, letters, digits and underscore.
    if (!preg_match('/^[A-Za-z0-9_]{1,16}$/', $username)) {
        return '';
    }
    static $cache = null;
    if ($cache === null) {
        $cache = json_cache_read(GSQ_MINECRAFT_CACHE);
    }
    if (isset($cache[$username])) {
        return $cache[$username];
    }
    $response = @file_get_contents('https://api.mojang.com/users/profiles/minecraft/' . rawurlencode($username));
    $data = $response ? json_decode($response) : null;
    $uuid = $data->id ?? '';
    if (!preg_match('/^[0-9a-f]{32}$/i', $uuid)) {
        return '';
    }
    $cache[$username] = $uuid;
    json_cache_set(GSQ_MINECRAFT_CACHE, $username, $uuid);
    return $uuid;
}

function get_title($url){
    $str = @file_get_contents($url);
    if ($str !== false && strlen($str) > 0) {
        $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
        if (preg_match("/\<title\>(.*?)\<\/title\>/i", $str, $title)) { // ignore case
            return html_entity_decode($title[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    return '';
}

function convertcsgomapname($mapname)
{
    static $maplist = null;
    if ($maplist === null) {
        include __DIR__ . '/html/type/csgo/maplist.php';
    }
    return $maplist['CsgoMapName'][$mapname] ?? null;
}
