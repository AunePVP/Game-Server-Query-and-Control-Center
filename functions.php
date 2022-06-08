<?php
function minecraftcache($username) {
    global $mcuuid;

    if (!isset($mcuuid[$username])) {
        $data = json_decode(file_get_contents("https://api.mojang.com/users/profiles/minecraft/$username"));
        $uuid = $data->id;
        $data = '$mcuuid[\''.$username.'\'] = "'. $uuid .'";';
        file_put_contents("query/cron/cache/minecraft.php", $data . "\n", FILE_APPEND);
        return $uuid;
    } else {
        return $mcuuid[$username];
    }

}
function convertos($Os)
{
    $Opers = array(
        'l' => 'Linux',
        'w' => 'Windows',
        'm' => 'Mac'
    );
    return $Opers[$Os];
}
function get_title($url){
    $str = file_get_contents($url);
    if(strlen($str)>0){
        $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
        preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
        return $title[1];
    }
}
function convertmodlistark($mod)
{
    include ('html/type/arkse/modlist.php');
    return $modlist['ArkModName'][$mod];
}
function convertcsgomapname($mapname)
{
    include ('html/type/csgo/maplist.php');
    return $mapname['CsgoMapName'][$mapname];
}