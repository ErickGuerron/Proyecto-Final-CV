<?php
// models/database.php

class Database
{
    /** @var Database|null */
    private static $instance = null;

    /** @var \PDO */
    private $connection;

    /**
     * Constructor privado: solo se llama desde getInstance()
     */
    private function __construct()
    {
        // Variables de entorno definidas en tu .env
        $host = getenv('DB_HOST') ?: 'gateway01.us-east-1.prod.aws.tidbcloud.com';
        $port = getenv('DB_PORT') ?: '4000';
        $db   = getenv('DB_NAME') ?: 'PHP_ACADEMIA';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
            // Certificado raíz para TiDB Cloud (ajusta la ruta si es distinta)
            \PDO::MYSQL_ATTR_SSL_CA       => __DIR__ . '/../config/isrgrootx1.pem',
            // Para desarrollo puedes desactivar la verificación estricta.
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];

        try {
            $this->connection = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                'Error al conectar con la base de datos: ' . $e->getMessage()
            );
        }
    }

    /**
     * Devuelve la única instancia de Database (singleton)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Devuelve el objeto PDO ya inicializado
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }
}
