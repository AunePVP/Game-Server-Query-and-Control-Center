<?php
// Load all query libraries (Source query, Minecraft ping/query, Minecraft colors)
require_once __DIR__ . '/SourceQuery/bootstrap.php';
require_once __DIR__ . '/minecraft/src/MinecraftPing.php';
require_once __DIR__ . '/minecraft/src/MinecraftPingException.php';
require_once __DIR__ . '/minecraft/src/MinecraftQuery.php';
require_once __DIR__ . '/minecraft/src/MinecraftQueryException.php';
require_once __DIR__ . '/../html/type/minecraft/jsonconversion.php';
require_once __DIR__ . '/../html/type/minecraft/minecraftcolor.php';
const SQ_TIMEOUT = 1;
const SQ_ENGINE = \xPaw\SourceQuery\SourceQuery::SOURCE;
