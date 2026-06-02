<?php // Carrega variáveis do .env e define a conexão PDO
 
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}
 
function env(string $key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
 
define('APP_NAME', env('APP_NAME', 'VisualTech'));
define('APP_URL',  env('APP_URL',  'http://localhost/visualtech'));
define('APP_ENV',  env('APP_ENV',  'production'));
define('APP_DEBUG',env('APP_DEBUG', false));
 
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
 
if (session_status() === PHP_SESSION_NONE) {
    session_name(env('SESSION_NAME', 'vt_session'));
    session_set_cookie_params([
        'lifetime' => (int)env('SESSION_LIFETIME', 7200),
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
 
class Database {
    private static ?Database $instance = null;
    private PDO $pdo;
 
    private function __construct() {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            env('DB_HOST','localhost'), env('DB_PORT','3306'),
            env('DB_NAME','visualtech'), env('DB_CHARSET','utf8mb4'));
        try {
            $this->pdo = new PDO($dsn, env('DB_USER','root'), env('DB_PASS',''), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            $msg = APP_DEBUG ? $e->getMessage() : 'Erro de conexão com o banco.';
            die('<b>Erro DB:</b> ' . $msg);
        }
    }
 
    public static function getInstance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }
 
    public function query(string $sql, array $p = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($p);
        return $stmt;
    }
 
    public function fetch(string $sql, array $p = []): array|false {
        return $this->query($sql, $p)->fetch();
    }
 
    public function fetchAll(string $sql, array $p = []): array {
        return $this->query($sql, $p)->fetchAll();
    }
 
    public function insert(string $sql, array $p = []): string|false {
        $this->query($sql, $p);
        return $this->pdo->lastInsertId();
    }
 
    public function count(string $sql, array $p = []): int {
        return (int)$this->query($sql, $p)->fetchColumn();
    }
}
 
function db(): Database { return Database::getInstance(); }
