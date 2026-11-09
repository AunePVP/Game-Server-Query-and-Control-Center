<?php
/*
 * Query one game server and prepare its display variables.
 * Expects: $ip, $type, $qport, $gport, $name, $ServerID (from the serverconfig row).
 * Sets: $serverstatus and everything html/type/query.php sets ($title, $status, $img, ...).
 * Included at global scope, so the variables are available to the including page.
 */
$queryresult = null;
if (is_source_type($type)) {
    include __DIR__ . '/sourcequery.php';
} elseif ($type === 'minecraft') {
    include __DIR__ . '/minecraftquery.php';
}
$serverstatus = json_decode($queryresult ?? 'null');
include __DIR__ . '/../html/type/query.php';
// Local images are relative to the web root; the control panel lives two levels deeper
if (isset($controlpanel) && str_starts_with($img, 'html/')) {
    $img = '../../' . $img;
}
