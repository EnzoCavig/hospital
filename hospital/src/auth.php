<?php
require_once 'db.php';

function autenticar($login, $senha) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM usuarios_administrativos WHERE login = ?");
    $stmt->execute([$login]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    return $usuario && password_verify($senha, $usuario['senha']);
}
?>
