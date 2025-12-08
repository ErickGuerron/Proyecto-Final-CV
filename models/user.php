<?php
// models/user.php

require_once __DIR__ . '/database.php';

class User
{
    /** @var \PDO */
    private $db;

    public function __construct(?\PDO $db = null)
    {
        // Si no te pasan la conexión, la obtenemos del singleton
        // Si renombraste los métodos, ajusta aquí:
        // Database::instance()->conneccion()
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function getUserByEmail(string $email): ?array
    {
        if (!$this->db instanceof \PDO) {
            throw new \RuntimeException('Conexión a BD no inicializada en User.');
        }

        $sql = 'SELECT ID_USU, COR_USU, CON_USU, ROL_USU
                FROM USUARIOS
                WHERE COR_USU = :email
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ?: null;
    }
}
