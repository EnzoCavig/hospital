<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');

function getDBConnection() {
    try {
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        exit;
    }
}

