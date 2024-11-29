<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');    // Host do banco de dados
define('DB_NAME', 'hospital');     // Nome do banco de dados
define('DB_USER', 'postgres');     // Usuário do banco de dados
define('DB_PASS', 'postgres');     // Senha do banco de dados

// Função para obter a conexão com o banco de dados
function getDBConnection() {
    try {
        // Criando uma nova conexão PDO
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

        // Configurando o modo de erro do PDO para exceções
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;  // Retorna a conexão PDO
    } catch (PDOException $e) {
        // Caso ocorra um erro, ele será exibido
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        exit;
    }
}

// Teste a conexão (opcional, apenas para depuração)
// $pdo = getDBConnection();
// echo "Conexão com o banco de dados estabelecida com sucesso!";
