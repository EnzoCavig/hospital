<?php
require_once 'db.php';

function getPerguntasPorSetor($idSetor) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM perguntas WHERE id_setor = :id_setor");
        $stmt->bindParam(':id_setor', $idSetor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Erro ao buscar perguntas para o setor: " . $e->getMessage());
    }
}

function getPerguntas() {
    $db = getDBConnection();
    $query = $db->query("SELECT id, texto FROM perguntas WHERE status = TRUE");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>
