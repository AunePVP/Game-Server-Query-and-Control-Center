<?php
/*
 * Shared helpers: database access with prepared statements, session handling,
 * CSRF protection, output escaping and safe config writing.
 */

if (defined('GSQ_LIB_LOADED')) {
    return;
}
define('GSQ_LIB_LOADED', true);

const GSQ_CONFIG_FILE = __DIR__ . '/config.php';
const GSQ_ADMIN_USER = 'admin';
const GSQ_RESERVED_USERS = ['admin', 'public'];
const GSQ_SERVER_TYPES = ['arkse', 'csgo', 'dayz', 'minecraft', 'rust', 'valheim', 'vrising', 'protocol-valve'];
const GSQ_SOURCE_TYPES = ['csgo', 'valheim', 'protocol-valve', 'arkse', 'vrising', 'dayz', 'rust'];
const GSQ_SPRITES = ['adventurer', 'adventurer-neutral', 'notionists', 'avataaars', 'bottts', 'croodles', 'identicon', 'pixel-art', 'pixel-art-neutral', 'male', 'female'];
const GSQ_USERNAME_REGEX = '/^[a-zA-Z0-9_\-äöüÄÖÜß]{4,15}$/u';

/* ------------------------------------------------------------------ */
/* Output                                                              */
/* ------------------------------------------------------------------ */

/** Escape a value for HTML output (text and attribute context). */
function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Send a redirect and stop the script. Always use this instead of a bare header(). */
function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

/** Trim an input value. Escaping happens on output, not on input. */
function test_input($data): string
{
    return trim((string)($data ?? ''));
}

function convertos($Os)
{
    $Opers = array(
        'l' => 'Linux',
        'w' => 'Windows',
        'm' => 'Mac'
    );
    return $Opers[$Os] ?? '';
}

/* ------------------------------------------------------------------ */
/* Game servers                                                        */
/* ------------------------------------------------------------------ */

/** Games that are queried with the Source query protocol (A2S). */
function is_source_type($type): bool
{
    return in_array($type, GSQ_SOURCE_TYPES, true);
}

/** Interpret the many ways game servers report booleans: true, 1, "1", "true", "True". */
function gsq_truthy($value): bool
{
    if (is_string($value)) {
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }
    return (bool)$value;
}

/** Translate an official CS:GO map file name (de_dust2) into its display name. */
function convertcsgomapname($mapname)
{
    static $maplist = null;
    if ($maplist === null) {
        include __DIR__ . '/type/csgo/maplist.php';
    }
    return $maplist['CsgoMapName'][$mapname] ?? null;
}

/** Build the player list (name + formatted play time) from an A2S_PLAYER result. */
function source_playerlist($players): array
{
    $list = [];
    foreach ($players ?: [] as $player) {
        $player = (object)$player;
        if (!strlen((string)($player->Name ?? ''))) {
            continue;
        }
        $rawtime = (int)($player->Time ?? 0);
        $list[] = [
            'Name' => $player->Name,
            'Time' => sprintf('%02dh:%02dm:%02ds', intdiv($rawtime, 3600), intdiv($rawtime, 60) % 60, $rawtime % 60),
        ];
    }
    return $list;
}

/**
 * Read the last $lines entries of a server's history file (query/cron/<id>.json).
 * Returns the player numbers (oldest first, padded with 0) and the latest uptime.
 */
function read_history(int $serverId, int $lines = 7): array
{
    $players = array_fill(0, $lines, 0);
    $uptime = 0;
    $raw = tailCustom(dirname(__DIR__) . "/query/cron/$serverId.json", $lines);
    $count = 0;
    foreach (preg_split("/\r\n|\r|\n/", (string)$raw) as $line) {
        $entry = json_decode($line);
        if (!$entry || $count >= $lines) {
            continue;
        }
        $players[$count++] = (int)($entry->players ?? 0);
        $uptime = $entry->uptime ?? $uptime;
    }
    return ['players' => $players, 'uptime' => $uptime];
}

/* ------------------------------------------------------------------ */
/* Sessions & CSRF                                                     */
/* ------------------------------------------------------------------ */

function gsq_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if (!headers_sent()) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    session_start();
}

function current_user(): string
{
    return $_SESSION['username'] ?? 'public';
}

function is_admin(?string $username = null): bool
{
    return ($username ?? current_user()) === GSQ_ADMIN_USER;
}

function require_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

/** Abort every POST request that does not carry a valid CSRF token. */
function csrf_verify(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }
    $sent = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';
    if (!is_string($sent) || $expected === '' || !hash_equals($expected, $sent)) {
        http_response_code(400);
        exit('Invalid or missing CSRF token. Please reload the page and try again.');
    }
}

/* ------------------------------------------------------------------ */
/* Passwords                                                           */
/* ------------------------------------------------------------------ */

function gsq_password_hash(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify a password. Accepts legacy unsalted sha256 hashes so existing
 * accounts keep working; $needsRehash tells the caller to upgrade them.
 */
function gsq_password_verify(string $password, string $hash, ?bool &$needsRehash = null): bool
{
    $needsRehash = false;
    if (preg_match('/^[0-9a-f]{64}$/', $hash)) {
        $ok = hash_equals($hash, hash('sha256', $password));
        $needsRehash = $ok;
        return $ok;
    }
    if (password_verify($password, $hash)) {
        $needsRehash = password_needs_rehash($hash, PASSWORD_BCRYPT);
        return true;
    }
    return false;
}

/* ------------------------------------------------------------------ */
/* Database                                                            */
/* ------------------------------------------------------------------ */

function db(): mysqli
{
    static $conn = null;
    if ($conn === null) {
        global $DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

/**
 * Run a prepared statement. Returns all rows (associative) for SELECT
 * statements and the number of affected rows otherwise.
 */
function db_query(string $sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    if ($params) {
        $types = '';
        foreach ($params as $param) {
            $types .= is_int($param) ? 'i' : 's';
        }
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function db_row(string $sql, array $params = []): ?array
{
    $rows = db_query($sql, $params);
    return $rows[0] ?? null;
}

/** Return the server IDs a user has in their account (always ints, never null). */
function user_server_ids(string $username): array
{
    $row = db_row('SELECT server FROM users WHERE username = ?', [$username]);
    if (!$row) {
        return [];
    }
    $ids = json_decode($row['server'] ?? '[]', true);
    if (!is_array($ids)) {
        return [];
    }
    return array_values(array_filter(array_map('intval', $ids)));
}

/** Store a list of server IDs for a user. An empty list is stored as [0] like before. */
function store_user_server_ids(int $userId, array $ids): void
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    db_query('UPDATE users SET server = ? WHERE id = ?', [json_encode($ids ?: [0]), $userId]);
}

/** Make sure user names are unique on database level. Fails silently if duplicates already exist. */
function ensure_schema(): void
{
    try {
        $rows = db_query("SHOW INDEX FROM users WHERE Column_name = 'username' AND Non_unique = 0");
        if (!$rows) {
            db()->query('ALTER TABLE users ADD UNIQUE KEY uniq_username (username)');
        }
    } catch (mysqli_sql_exception $e) {
        error_log('GSQ: could not add unique index on users.username: ' . $e->getMessage());
    }
}

/* ------------------------------------------------------------------ */
/* Config                                                              */
/* ------------------------------------------------------------------ */

/** Available CSS themes (file names in /css without extension). */
function available_themes(): array
{
    $themes = [];
    foreach (glob(dirname(__DIR__) . '/css/*.css') as $file) {
        $name = basename($file, '.css');
        if ($name !== 'style') {
            $themes[] = $name;
        }
    }
    return $themes;
}

/**
 * Rewrite config.php completely. Every value is exported with var_export(),
 * so user input can never break out of its string and inject PHP code.
 */
function write_config(array $overrides = []): bool
{
    $defaults = [
        'install' => 0,
        'steamwebapi_key' => '',
        'rustmapsapi_key' => '',
        'DB_SERVER' => 'localhost',
        'DB_USERNAME' => '',
        'DB_PASSWORD' => '',
        'DB_NAME' => '',
        'css_template' => 'summer-night',
        'steamapi' => 'https://api.steampowered.com/',
        'register' => true,
    ];
    $content = "<?php\ninclude __DIR__ . '/langconf.php';\n";
    foreach ($defaults as $key => $default) {
        if (array_key_exists($key, $overrides)) {
            $value = $overrides[$key];
        } elseif (array_key_exists($key, $GLOBALS)) {
            $value = $GLOBALS[$key];
        } else {
            $value = $default;
        }
        if (is_bool($default)) {
            $value = (bool)$value;
        } elseif (is_int($default)) {
            $value = (int)$value;
        } else {
            $value = (string)$value;
        }
        $content .= '$' . $key . ' = ' . var_export($value, true) . ";\n";
    }
    return file_put_contents(GSQ_CONFIG_FILE, $content, LOCK_EX) !== false;
}

/* ------------------------------------------------------------------ */
/* JSON caches (replace the old PHP cache files)                       */
/* ------------------------------------------------------------------ */

function json_cache_read(string $file): array
{
    if (!is_file($file)) {
        return [];
    }
    $data = json_decode((string)file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function json_cache_set(string $file, string $key, $value): void
{
    $handle = fopen($file, 'c+');
    if ($handle === false) {
        return;
    }
    flock($handle, LOCK_EX);
    $data = json_decode((string)stream_get_contents($handle), true);
    if (!is_array($data)) {
        $data = [];
    }
    $data[$key] = $value;
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}
