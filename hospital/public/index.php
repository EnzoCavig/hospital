<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/perguntas.php';

// Buscar perguntas ativas usando a função de perguntas.php
try {
    $perguntas = getPerguntas();
} catch (PDOException $e) {
    echo "Erro ao buscar perguntas: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação de Serviços</title>
    <style>
        /* Escondendo todas as perguntas por padrão */
        .pergunta {
            display: none;
        }

        /* Estilização para a escala de notas */
        .resposta-escala {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .escala-item {
            display: inline-block;
            padding: 10px;
            background-color: #ddd;
            border-radius: 5px;
            text-align: center;
            width: 40px;
            cursor: pointer;
        }

        /* Cores diferentes para cada nota */
        .escala-0 { background-color: #ff595e; }
        .escala-1 { background-color: #ff6f61; }
        .escala-2 { background-color: #ff7858; }
        .escala-3 { background-color: #ff934f; }
        .escala-4 { background-color: #ffb347; }
        .escala-5 { background-color: #ffd046; }
        .escala-6 { background-color: #ffe156; }
        .escala-7 { background-color: #d4eb78; }
        .escala-8 { background-color: #a5d65a; }
        .escala-9 { background-color: #65bc5d; }
        .escala-10 { background-color: #3bb273; }

        /* Estilo para o campo de texto */
        .campo-texto {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .campo-texto textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Botões */
        #proximo-btn, #enviar-btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #proximo-btn {
            background-color: #4CAF50;
            color: white;
        }
        #enviar-btn {
            background-color: #2196F3;
            color: white;
        }
    </style>
</head>

<body>
    <h1>Avaliação de Serviços</h1>

    <form id="avaliacao-form" action="../src/respostas.php" method="POST">
        <?php if (!empty($perguntas)): ?>
            <?php foreach ($perguntas as $index => $pergunta): ?>
                <div class="pergunta" id="pergunta-<?php echo $index; ?>">
                    <label><?php echo htmlspecialchars($pergunta['texto']); ?></label>
                    <div class="resposta-escala">
                        <?php for ($i = 0; $i <= 10; $i++): ?>
                            <input type="radio" name="respostas[<?php echo $pergunta['id']; ?>][nota]" 
                                   id="resposta-<?php echo $pergunta['id']; ?>-<?php echo $i; ?>" 
                                   value="<?php echo $i; ?>" required>
                            <label for="resposta-<?php echo $pergunta['id']; ?>-<?php echo $i; ?>" class="escala-item escala-<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>

                    <!-- Campo de texto opcional para comentário da pergunta -->
                    <div class="campo-texto">
                        <label for="comentario-<?php echo $pergunta['id']; ?>">Comentário (opcional):</label>
                        <textarea name="respostas[<?php echo $pergunta['id']; ?>][comentario]" 
                                  id="comentario-<?php echo $pergunta['id']; ?>" 
                                  placeholder="Escreva seu comentário aqui..."></textarea>
                    </div>
                </div>
            <?php endforeach; ?>

            <input type="hidden" name="id_setor" value="1"> <!-- Insira o ID do setor -->
            <input type="hidden" name="id_dispositivo" value="123"> <!-- Insira o ID do dispositivo -->

            <button type="button" id="proximo-btn">Avançar</button>
            <button type="submit" id="enviar-btn" style="display: none;">Enviar Avaliação</button>
        <?php else: ?>
            <p>Nenhuma pergunta disponível no momento.</p>
        <?php endif; ?>
    </form>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let perguntaAtual = 0;
        const perguntas = document.querySelectorAll('.pergunta');
        const btnProximo = document.getElementById('proximo-btn');
        const btnEnviar = document.getElementById('enviar-btn');
        const form = document.getElementById('avaliacao-form');

        // Função para mostrar a pergunta atual e esconder as outras
        function mostrarPergunta(index) {
            perguntas.forEach((pergunta, i) => {
                pergunta.style.display = (i === index) ? 'block' : 'none';
            });
        }

        // Mostrar a primeira pergunta ao carregar
        mostrarPergunta(perguntaAtual);

        // Avançar para a próxima pergunta
        btnProximo.addEventListener('click', function() {
            const radios = perguntas[perguntaAtual].querySelectorAll('input[type="radio"]');
            let respostaSelecionada = false;

            // Verifica se algum rádio foi selecionado
            radios.forEach(radio => {
                if (radio.checked) {
                    respostaSelecionada = true;
                }
            });

            // Se nenhuma opção foi selecionada, mostra um alerta
            if (!respostaSelecionada) {
                alert("Por favor, selecione uma nota antes de avançar.");
                return;
            }

            perguntaAtual++;

            if (perguntaAtual < perguntas.length) {
                mostrarPergunta(perguntaAtual);
            } else {
                // Se não houver mais perguntas, esconder o botão de avançar e mostrar o de enviar
                btnProximo.style.display = 'none';
                btnEnviar.style.display = 'block';
            }
        });

        // Adicionar evento para reiniciar o formulário (opcional, caso necessário)
        form.addEventListener('submit', function(e) {
            // Após o envio do formulário, você pode reiniciar as perguntas aqui se necessário.
            perguntaAtual = 0; // Resetar para a primeira pergunta
            mostrarPergunta(perguntaAtual);
            btnProximo.style.display = 'block'; // Mostrar o botão de avançar novamente
            btnEnviar.style.display = 'none'; // Esconder o botão de enviar novamente
        });
    });
</script>
    
</body>

</html>
