<?php
/**Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/@author * Torleif Berger, Lorenzo Stanco @link http://stackoverflow.com/a/15025877/995958@license http://creativecommons.org/licenses/by/3.0/ */
function tailCustom($filepath, $lines = 1, $adaptive = true)
{
    $f = @fopen($filepath, "rb");
    if ($f === false) return false;
    if (!$adaptive) $buffer = 4096;
    else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
    fseek($f, -1, SEEK_END);
    if (fread($f, 1) != "\n") $lines -= 1;
    $output = '';
    $chunk = '';
    while (ftell($f) > 0 && $lines >= 0) {
        $seek = min(ftell($f), $buffer);
        fseek($f, -$seek, SEEK_CUR);
        $output = ($chunk = fread($f, $seek)) . $output;
        fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
        $lines -= substr_count($chunk, "\n");
    }
    while ($lines++ < 0) {
        $output= substr($output, strpos($output, "\n") + 1);
    }
    fclose($f);
    return trim($output);
}
