<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/funcoes.php';

header('Content-Type: application/json');

if (!isset($_GET['id_setor']) || empty($_GET['id_setor'])) {
    echo json_encode(['error' => 'Setor não especificado.']);
    exit;
}

$idSetor = (int) $_GET['id_setor'];

try {
    $pdo = getDBConnection();

    // Query para buscar dados filtrados por setor
    $query = $pdo->prepare("SELECT id_pergunta, resposta, COUNT(resposta) AS quantidade
                            FROM avaliacoes
                            WHERE id_setor = :id_setor
                            GROUP BY id_pergunta, resposta");
    $query->bindParam(':id_setor', $idSetor, PDO::PARAM_INT);
    $query->execute();

    $dados = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dados);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao buscar dados do gráfico: ' . $e->getMessage()]);
}
