<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/perguntas.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idDispositivo = $_GET['id'];
    
    try {
        $pdo = getDBConnection();

        $stmt = $pdo->prepare("SELECT id_setor FROM dispositivos WHERE id = :id_dispositivo");
        $stmt->bindParam(':id_dispositivo', $idDispositivo, PDO::PARAM_INT);
        $stmt->execute();
        
        $dispositivo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dispositivo) {
            echo "Dispositivo não encontrado.";
            exit;
        }

        $idSetor = $dispositivo['id_setor'];

        $perguntas = getPerguntasPorSetor($idSetor);

    } catch (PDOException $e) {
        echo "Erro ao buscar informações: " . $e->getMessage();
        exit;
    }
} else {
    echo "Parâmetro id_dispositivo é obrigatório. Por favor, forneça o id_dispositivo na URL.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação de Serviços</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
    <style>
        .inicio-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .perguntas-container {
            display: none;
        }

        #comecar-btn {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #comecar-btn:hover {
            background-color: #45a049;
        }      
    </style>
</head>

<body>
    <div class="inicio-container" id="inicio-container">
        <h1>Bem-vindo à Avaliação de Serviços</h1>
        <p>Por favor, clique no botão abaixo para iniciar sua avaliação.</p>
        <button id="comecar-btn" onclick="comecarAvaliacao()">Começar Avaliação</button>
        <button id="admin-btn" onclick="painelAdministrativo()">Painel Administrativo</button>
    </div>

    <div class="perguntas-container" id="perguntas-container">
        <h1>Avaliação de Serviços</h1>
        <form id="avaliacao-form" action="../src/respostas.php" method="POST">
            <?php if (!empty($perguntas)): ?>
                <?php foreach ($perguntas as $index => $pergunta): ?>
                    <div class="pergunta" id="pergunta-<?php echo $index; ?>">
                        <label><?php echo htmlspecialchars($pergunta['texto']); ?></label>
                        <div class="resposta-escala">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <div class="avaliacao-btn"
                                    data-value="<?php echo $i; ?>"
                                    onclick="selecionarNota('<?php echo $pergunta['id']; ?>', <?php echo $i; ?>)">
                                    <?php echo $i; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="campo-texto">
                            <label for="comentario-<?php echo $pergunta['id']; ?>">Comentário (opcional):</label>
                            <textarea name="respostas[<?php echo $pergunta['id']; ?>][comentario]"
                                id="comentario-<?php echo $pergunta['id']; ?>"
                                placeholder="Escreva seu comentário aqui..."></textarea>
                        </div>
                        <input type="hidden" id="nota-<?php echo $pergunta['id']; ?>" name="respostas[<?php echo $pergunta['id']; ?>][nota]" required>
                    </div>
                <?php endforeach; ?>
                <input type="hidden" name="id_setor" value="<?php echo $idSetor; ?>">
                <input type="hidden" name="id_dispositivo" value="<?php echo $idDispositivo; ?>">
                <button type="button" id="proximo-btn">Avançar</button>
                <button type="submit" id="enviar-btn" style="display: none;">Enviar Avaliação</button>
            <?php else: ?>
                <p>Nenhuma pergunta disponível para o setor.</p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        function comecarAvaliacao() {
            document.getElementById('inicio-container').style.display = 'none';
            document.getElementById('perguntas-container').style.display = 'block';
        }

        function painelAdministrativo() {
            window.location.href = "admin.php";
        }

        function selecionarNota(perguntaId, nota) {
            const perguntaAtual = document.querySelector(`#pergunta-${perguntaId}`);
            perguntaAtual.querySelectorAll('.avaliacao-btn').forEach(btn => btn.classList.remove('selected'));

            const btnSelecionado = perguntaAtual.querySelector(`.avaliacao-btn[data-value="${nota}"]`);
            if (btnSelecionado) {
                btnSelecionado.classList.add('selected');
            }

            const inputNota = document.getElementById(`nota-${perguntaId}`);
            if (inputNota) {
                inputNota.value = nota;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            let perguntaAtual = 0; 
            const perguntas = document.querySelectorAll('.pergunta'); 
            const btnProximo = document.getElementById('proximo-btn'); 
            const btnEnviar = document.getElementById('enviar-btn'); 

            function mostrarPergunta(index) {
                perguntas.forEach((pergunta, i) => {
                    pergunta.style.display = i === index ? 'block' : 'none';

                    const botoesNota = pergunta.querySelectorAll('.avaliacao-btn');
                    botoesNota.forEach(btn => btn.classList.remove('selected'));
                });
            }

            mostrarPergunta(perguntaAtual);

            perguntas.forEach((pergunta) => {
                const botoesNota = pergunta.querySelectorAll('.avaliacao-btn');
                botoesNota.forEach((botao) => {
                    botao.addEventListener('click', function() {
                        const nota = botao.getAttribute('data-value');
                        const campoHidden = pergunta.querySelector('input[type="hidden"]');

                        if (campoHidden) {
                            campoHidden.value = nota;
                            console.log(`Nota registrada para pergunta ${pergunta.id}: ${nota}`);
                        }

                        botoesNota.forEach(btn => btn.classList.remove('selected'));
                        botao.classList.add('selected');
                    });
                });
            });

            btnProximo.addEventListener('click', function() {
                const perguntaAtualElemento = perguntas[perguntaAtual];
                const campoHidden = perguntaAtualElemento.querySelector('input[type="hidden"]');

                if (!campoHidden || !campoHidden.value) {
                    alert("Por favor, selecione uma nota antes de avançar.");
                    return;
                }

                console.log(`Pergunta ${perguntaAtual} respondida com nota: ${campoHidden.value}`);

                perguntaAtual++;
                if (perguntaAtual < perguntas.length) {
                    mostrarPergunta(perguntaAtual);
                } else {
                    btnProximo.style.display = 'none';
                    btnEnviar.style.display = 'block';
                }
            });
        });
    </script>

    <footer>
        <p>Sua avaliação é anônima e nenhuma informação pessoal é coletada ou armazenada.</p>
    </footer>
</body>

</html>
