<?php

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respostas']) && isset($_POST['id_setor']) && isset($_POST['id_dispositivo'])) {

    $respostas = $_POST['respostas'];
    $id_setor = $_POST['id_setor'];
    $id_dispositivo = $_POST['id_dispositivo'];

    try {
        $db = getDBConnection();
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO avaliacoes (id_setor, id_dispositivo, id_pergunta, resposta, feedback_textual, data_hora)
            VALUES (:id_setor, :id_dispositivo, :id_pergunta, :resposta, :feedback_textual, NOW())
        ");

        foreach ($respostas as $id_pergunta => $resposta_dados) {
            $nota = isset($resposta_dados['nota']) ? $resposta_dados['nota'] : null;
            $comentario = isset($resposta_dados['comentario']) ? $resposta_dados['comentario'] : '';

            $stmt->execute([
                ':id_setor' => $id_setor,
                ':id_dispositivo' => $id_dispositivo,
                ':id_pergunta' => $id_pergunta,
                ':resposta' => $nota,
                ':feedback_textual' => $comentario
            ]);
        }

        $db->commit();

        echo "
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Avaliação Enviada</title>
    <script>
        // Redirecionar para o formulário após 5 segundos, incluindo o id_dispositivo na URL
        setTimeout(function() {
            window.location.href = '../public/index.php?id=" . $id_dispositivo . "'; // Inclui o id_dispositivo
        }, 5000);
    </script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            color: #333;
            max-width: 600px;
        }
        footer {
            background-color: #f8f8f8;
            padding: 10px;
            font-size: 12px;
            color: #555;
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>O Hospital Regional Alto Vale (HRAV) agradece sua resposta e ela é muito importante para nós, pois nos ajuda a melhorar continuamente nossos serviços!</h1>

    <footer>
        <p>Sua avaliação espontânea é anônima, nenhuma informação pessoal é solicitada ou armazenada.</p>
    </footer>
</body>
</html>
";
    } catch (PDOException $e) {
        $db->rollBack();
        echo "Erro ao salvar respostas: " . $e->getMessage();
    }
} else {
    echo "Nenhuma resposta foi recebida.";
}

function registrarAvaliacao($id_dispositivo, $id_pergunta, $resposta, $feedback_textual)
{
    include 'db.php';

    $sql = "SELECT id_setor FROM dispositivos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_dispositivo);
    $stmt->execute();
    $stmt->bind_result($id_setor);
    $stmt->fetch();
    $stmt->close();

    if ($id_setor) {
        $sql = "INSERT INTO avaliacoes (id_setor, id_pergunta, id_dispositivo, resposta, feedback_textual) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $id_setor, $id_pergunta, $id_dispositivo, $resposta, $feedback_textual);

        if ($stmt->execute()) {
            return "Avaliação registrada com sucesso!";
        } else {
            return "Erro ao registrar avaliação: " . $stmt->error;
        }

        $stmt->close();
    } else {
        return "Erro: Dispositivo não associado a nenhum setor.";
    }

    $conn->close();
}
