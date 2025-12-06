<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host;
    private $port;
    private $user;
    private $pass;
    private $name;

    private function __construct() {
        $this->loadEnv(__DIR__ . '/../.env');

        $this->host = getenv('HOST');
        $this->port = getenv('PORT');
        $this->user = getenv('USERNAME'); // Changed from USER to USERNAME as per .env file
        $this->pass = getenv('PASSWORD');
        $this->name = getenv('DATABASE');

        $ssl_ca = __DIR__ . '/../config/isrgrootx1.pem';

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->name};", 
                $this->user, 
                $this->pass,
                [
                    PDO::MYSQL_ATTR_SSL_CA => $ssl_ca,
                ]
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    // Function to load .env file
    private function loadEnv($path) {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
