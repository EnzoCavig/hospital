<?php

require_once __DIR__ . '/../config.php'; // Certifique-se de que o arquivo config.php está correto

// Verifica se o método é POST e se as respostas e outros parâmetros foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respostas']) && isset($_POST['id_setor']) && isset($_POST['id_dispositivo'])) {
    
    $respostas = $_POST['respostas'];
    $id_setor = $_POST['id_setor'];
    $id_dispositivo = $_POST['id_dispositivo'];

    try {
        $db = getDBConnection(); // Conexão com o banco de dados
        $db->beginTransaction(); // Iniciar uma transação

        // Prepare a query para inserir as respostas no banco de dados
        $stmt = $db->prepare("
            INSERT INTO avaliacoes (id_setor, id_dispositivo, id_pergunta, resposta, feedback_textual, data_hora)
            VALUES (:id_setor, :id_dispositivo, :id_pergunta, :resposta, :feedback_textual, NOW())
        ");

        // Iterar sobre as respostas enviadas
        foreach ($respostas as $id_pergunta => $resposta_dados) {
            // Captura a nota e o comentário, se houver
            $nota = isset($resposta_dados['nota']) ? $resposta_dados['nota'] : null;
            $comentario = isset($resposta_dados['comentario']) ? $resposta_dados['comentario'] : '';

            // Executa a query para salvar a resposta e o comentário
            $stmt->execute([
                ':id_setor' => $id_setor,
                ':id_dispositivo' => $id_dispositivo,
                ':id_pergunta' => $id_pergunta,
                ':resposta' => $nota,
                ':feedback_textual' => $comentario
            ]);
        }

        $db->commit(); // Confirma a transação

        // Mostra a página de agradecimento
        echo "
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Avaliação Enviada</title>
            <script>
                // Redirecionar para o formulário após 5 segundos
                setTimeout(function() {
                    window.location.href = '../public/index.php'; // Atualizado para o caminho correto
                }, 5000);
            </script>
        </head>
        <body>
            <h1>Agradecemos sua Avaliação!</h1>
            <p>Sua avaliação foi enviada com sucesso.</p>
            <p>Você será redirecionado para a primeira pergunta em 5 segundos.</p>
        </body>
        </html>
        ";

    } catch (PDOException $e) {
        $db->rollBack(); // Reverte a transação em caso de erro
        echo "Erro ao salvar respostas: " . $e->getMessage();
    }
} else {
    echo "Nenhuma resposta foi recebida.";
}
