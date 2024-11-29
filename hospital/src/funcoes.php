<?php
// src/funcoes.php

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function cadastrarDispositivo($pdo, $nome, $id_setor) {
    try {
        $sql = "INSERT INTO dispositivos (nome, id_setor) VALUES (:nome, :id_setor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':id_setor', $id_setor, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "Dispositivo cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar dispositivo.";
        }
    } catch (PDOException $e) {
        return "Erro ao cadastrar dispositivo: " . $e->getMessage();
    }
}
?>