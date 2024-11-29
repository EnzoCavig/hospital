<div class="admin-page">

    <?php
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../src/funcoes.php';
    session_start();

    // Verifica se o usuário está autenticado
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php"); // Redireciona para a tela de login
        exit;
    }

    // Conexão com o banco de dados
    $pdo = getDBConnection();

    // Cadastrar dispositivo
    if (isset($_POST['cadastrar_dispositivo'])) {
        $nome = sanitizeInput($_POST['nome']);
        $id_setor = (int) $_POST['id_setor'];

        $mensagem = cadastrarDispositivo($pdo, $nome, $id_setor);
        echo "<p>$mensagem</p>";
    }
    // Gerenciamento de perguntas
    if (isset($_POST['cadastrar_pergunta'])) {
        $texto = sanitizeInput($_POST['texto']);
        $id_setor = (int) $_POST['id_setor_pergunta'];

        $stmt = $pdo->prepare("INSERT INTO perguntas (texto, id_setor, status) VALUES (?, ?, true)");
        $stmt->execute([$texto, $id_setor]);
    } elseif (isset($_POST['editar_pergunta'])) {
        $id = (int) $_POST['id_pergunta'];
        $texto = sanitizeInput($_POST['texto']);
        $stmt = $pdo->prepare("UPDATE perguntas SET texto = ? WHERE id = ?");
        $stmt->execute([$texto, $id]);
    } elseif (isset($_POST['excluir_pergunta'])) {
        $id = (int) $_POST['id_pergunta'];

        // Verificar dependências antes de excluir
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) AS total FROM avaliacoes WHERE id_pergunta = ?");
        $stmtCheck->execute([$id]);
        $dependencias = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($dependencias['total'] > 0) {
            // Desativar pergunta
            $stmt = $pdo->prepare("UPDATE perguntas SET status = false WHERE id = ?");
            $stmt->execute([$id]);
            echo "<p>Pergunta desativada com sucesso.</p>";
        } else {
            // Excluir pergunta
            $stmt = $pdo->prepare("DELETE FROM perguntas WHERE id = ?");
            $stmt->execute([$id]);
        }
    }

    // Obter perguntas para listagem
    $setorPerguntas = [];
    if (isset($_GET['id_setor_perguntas'])) {
        $id_setor = (int) $_GET['id_setor_perguntas'];
        $stmt = $pdo->prepare("SELECT * FROM perguntas WHERE id_setor = ? AND status = true ORDER BY id");
        $stmt->execute([$id_setor]);
        $setorPerguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>


    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administração - Gráficos</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="css/style.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }

            h1,
            h2 {
                color: #333;
            }

            form {
                margin-bottom: 20px;
            }

            label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }

            select,
            input,
            button {
                margin-bottom: 10px;
                padding: 5px 10px;
                font-size: 14px;
                width: 100%;
            }

            canvas {
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
        <h1>Administração</h1>
        <!-- Seletor de Setor para Gerar Relatório -->
        <h2>Gerar Gráfico por Setor</h2>
        <form id="form-relatorio">
            <label for="setor-relatorio">Selecione o Setor:</label>
            <select id="setor-relatorio" name="id_setor">
                <option value="1">Recepção</option>
                <option value="2">Médicos</option>
                <option value="3">Enfermagem</option>
                <option value="4">Alimentação</option>
            </select>
            <button type="button" id="gerar-relatorio">Gerar Relatório</button>
        </form>

        <canvas id="grafico-avaliacoes" width="400" height="200"></canvas>

        <!-- Formulário de Cadastro de Dispositivo -->
        <h2>Cadastrar Dispositivo</h2>
        <form id="form-cadastro" action="" method="POST">
            <label for="nome">Nome do Dispositivo:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="setor-cadastro">Setor:</label>
            <select id="setor-cadastro" name="id_setor" required>
                <option value="1">Recepção</option>
                <option value="2">Médicos</option>
                <option value="3">Enfermagem</option>
                <option value="4">Alimentação</option>
            </select>

            <button type="submit" name="cadastrar_dispositivo">Cadastrar</button>
        </form>

        <!-- Formulário de Cadastro de Pergunta -->
        <h2>Cadastrar Nova Pergunta</h2>
        <form action="admin.php" method="POST">
            <label for="texto">Texto da Pergunta:</label>
            <input type="text" id="texto" name="texto" required>

            <label for="setor-pergunta">Setor:</label>
            <select id="setor-pergunta" name="id_setor_pergunta" required>
                <option value="1">Recepção</option>
                <option value="2">Médicos</option>
                <option value="3">Enfermagem</option>
                <option value="4">Alimentação</option>
            </select>

            <button type="submit" name="cadastrar_pergunta">Cadastrar Pergunta</button>
        </form>

        <!-- Gerenciamento de Perguntas -->
        <h2>Gerenciar Perguntas</h2>
        <form id="form-seletor-perguntas" method="GET">
            <label for="setor-perguntas">Selecione o Setor:</label>
            <select id="setor-perguntas" name="id_setor_perguntas">
                <option value="">Selecione um setor</option>
                <option value="1">Recepção</option>
                <option value="2">Médicos</option>
                <option value="3">Enfermagem</option>
                <option value="4">Alimentação</option>
            </select>
            <button type="submit">Listar Perguntas</button>
        </form>

        <?php if (!empty($setorPerguntas)): ?>
            <!-- Formulário de Listagem e Edição -->
            <table>
                <tr>
                    <th>ID</th>
                    <th>Texto</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($setorPerguntas as $pergunta): ?>
                    <tr>
                        <td><?= $pergunta['id'] ?></td>
                        <td>
                            <span class="texto-pergunta" data-id="<?= $pergunta['id'] ?>">
                                <?= htmlspecialchars($pergunta['texto']) ?>
                            </span>
                            <input type="text" class="input-editar" data-id="<?= $pergunta['id'] ?>" value="<?= htmlspecialchars($pergunta['texto']) ?>" style="display: none;">
                        </td>
                        <td>
                            <button class="btn-editar" data-id="<?= $pergunta['id'] ?>">Editar</button>
                            <button class="btn-salvar" data-id="<?= $pergunta['id'] ?>" style="display: none;">Salvar</button>
                            <button class="btn-excluir" data-id="<?= $pergunta['id'] ?>">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <button id="voltar-inicio" class="btn-voltar">Voltar à Tela Inicial</button>
    </body>

    <script>
        let chart = null;

        document.getElementById('gerar-relatorio').addEventListener('click', atualizarGrafico);

        async function atualizarGrafico() {
            const selectSetorRelatorio = document.getElementById('setor-relatorio');
            const idSetor = selectSetorRelatorio.value;

            if (!idSetor) {
                alert('Por favor, selecione um setor.');
                return;
            }

            try {
                // Requisição para obter dados do gráfico
                const response = await fetch(`dados_grafico.php?id_setor=${idSetor}`);
                const dados = await response.json();

                if (dados.error) {
                    alert(dados.error);
                    return;
                }

                // Processar os dados para o gráfico
                const perguntas = {};
                const todasRespostas = new Set();

                dados.forEach(item => {
                    const idPergunta = item.id_pergunta;
                    const resposta = item.resposta;
                    const quantidade = item.quantidade;

                    if (!perguntas[idPergunta]) {
                        perguntas[idPergunta] = {};
                    }

                    perguntas[idPergunta][resposta] = quantidade;
                    todasRespostas.add(resposta);
                });

                // Ordenar respostas
                const respostasOrdenadas = Array.from(todasRespostas).sort((a, b) => a - b);

                // Criar datasets com cores distintas
                const cores = [
                    'rgba(75, 192, 192, 0.6)', // Verde claro
                    'rgba(54, 162, 235, 0.6)', // Azul
                    'rgba(255, 206, 86, 0.6)', // Amarelo
                    'rgba(255, 99, 132, 0.6)', // Vermelho
                    'rgba(153, 102, 255, 0.6)' // Roxo
                ];

                const datasets = Object.keys(perguntas).map((pergunta, index) => {
                    const dadosPergunta = respostasOrdenadas.map(resposta =>
                        perguntas[pergunta][resposta] || 0
                    );

                    return {
                        label: `Pergunta ${pergunta}`,
                        data: dadosPergunta,
                        backgroundColor: cores[index % cores.length],
                        borderColor: cores[index % cores.length].replace('0.6', '1'),
                        borderWidth: 1
                    };
                });

                // Configuração de exibição dos valores nas barras
                const plugins = {
                    id: 'valores',
                    afterDatasetsDraw(chart) {
                        const {
                            ctx
                        } = chart;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const valor = dataset.data[index];
                                if (valor > 0) {
                                    ctx.fillStyle = 'black';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.font = '12px Arial';
                                    ctx.fillText(valor, bar.x, bar.y - 5);
                                }
                            });
                        });
                    }
                };

                // Atualizar ou criar gráfico
                if (chart) {
                    chart.data.labels = respostasOrdenadas.map(r => `Nota ${r}`);
                    chart.data.datasets = datasets;
                    chart.update();
                } else {
                    const ctx = document.getElementById('grafico-avaliacoes').getContext('2d');
                    chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: respostasOrdenadas.map(r => `Nota ${r}`),
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Avaliações por Nota',
                                    font: {
                                        size: 18
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Nota: ${context.label}, Quantidade: ${context.raw}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Notas (Respostas)',
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Quantidade de Avaliações',
                                        font: {
                                            size: 14
                                        }
                                    },
                                    beginAtZero: true
                                }
                            }
                        },
                        plugins: [plugins]
                    });
                }
            } catch (error) {
                console.error('Erro ao buscar dados do gráfico:', error);
            }
        }
        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', event => {
                const id = event.target.getAttribute('data-id');
                const span = document.querySelector(`.texto-pergunta[data-id="${id}"]`);
                const input = document.querySelector(`.input-editar[data-id="${id}"]`);
                const salvarBtn = document.querySelector(`.btn-salvar[data-id="${id}"]`);

                span.style.display = 'none';
                input.style.display = 'inline-block';
                salvarBtn.style.display = 'inline-block';
                button.style.display = 'none';
            });
        });

        document.querySelectorAll('.btn-salvar').forEach(button => {
            button.addEventListener('click', async event => {
                const id = event.target.getAttribute('data-id');
                const input = document.querySelector(`.input-editar[data-id="${id}"]`);
                const textoAtualizado = input.value;

                try {
                    const response = await fetch('admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `editar_pergunta=1&id_pergunta=${id}&texto=${encodeURIComponent(textoAtualizado)}`
                    });

                    if (response.ok) {
                        const span = document.querySelector(`.texto-pergunta[data-id="${id}"]`);
                        span.textContent = textoAtualizado;
                        span.style.display = 'inline-block';
                        input.style.display = 'none';
                        button.style.display = 'none';
                        document.querySelector(`.btn-editar[data-id="${id}"]`).style.display = 'inline-block';
                    } else {
                        alert('Erro ao editar a pergunta.');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao editar a pergunta.');
                }
            });
        });
        document.querySelectorAll('.btn-excluir').forEach(button => {
            button.addEventListener('click', async event => {
                const id = event.target.getAttribute('data-id');

                if (!confirm('Tem certeza que deseja excluir esta pergunta?')) return;

                try {
                    const response = await fetch('admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `excluir_pergunta=1&id_pergunta=${id}`
                    });

                    if (response.ok) {
                        const row = button.closest('tr');
                        row.remove(); // Remove a linha da tabela visualmente
                        alert('Pergunta excluída com sucesso.');
                    } else {
                        alert('Erro ao excluir a pergunta.');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao excluir a pergunta.');
                }
            });
        });
        document.getElementById('voltar-inicio').addEventListener('click', () => {
            window.location.href = 'index.php?id=1';
        });
    </script>
    </body>

    </html>
</div>