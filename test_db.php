<?php
$ssl_ca = __DIR__ . '/config/isrgrootx1.pem';

echo "Certificado existe: " . (file_exists($ssl_ca) ? "SI" : "NO") . "\n";
echo "Host: " . getenv('DB_HOST') . "\n";
echo "Port: " . getenv('DB_PORT') . "\n";
echo "User: " . getenv('DB_USER') . "\n";
echo "DB: " . getenv('DB_NAME') . "\n\n";

try {
    $conn = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4",
        getenv('DB_USER'),
        getenv('DB_PASSWORD'),
        [
            PDO::MYSQL_ATTR_SSL_CA => $ssl_ca,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    echo "Conexion exitosa!\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
