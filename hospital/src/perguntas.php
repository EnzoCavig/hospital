<?php
// src/perguntas.php
require_once 'db.php';

function getPerguntas() {
    $db = getDBConnection();
    $query = $db->query("SELECT id, texto FROM perguntas WHERE status = TRUE");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>
