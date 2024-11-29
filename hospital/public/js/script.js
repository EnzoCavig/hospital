document.addEventListener("DOMContentLoaded", function() {
 // Função para selecionar uma nota
function selecionarNota(perguntaId, nota) {
    // Seleciona todos os botões da pergunta atual e remove a classe "selected"
    const botoes = document.querySelectorAll(`#pergunta-${perguntaId} .avaliacao-btn`);
    botoes.forEach(btn => btn.classList.remove('selected'));

    // Adiciona a classe "selected" ao botão clicado
    const btnSelecionado = document.querySelector(`#pergunta-${perguntaId} .avaliacao-btn[data-value="${nota}"]`);
    btnSelecionado.classList.add('selected');

    // Atualiza o valor do campo hidden com a nota selecionada
    document.getElementById(`nota-${perguntaId}`).value = nota;
}

// Gerenciar avanço das perguntas
document.addEventListener("DOMContentLoaded", function () {
    let perguntaAtual = 0;
    const perguntas = document.querySelectorAll('.pergunta');
    const btnProximo = document.getElementById('proximo-btn');
    const btnEnviar = document.getElementById('enviar-btn');

    function mostrarPergunta(index) {
        // Esconde todas as perguntas, exceto a atual
        perguntas.forEach((pergunta, i) => {
            pergunta.style.display = (i === index) ? 'block' : 'none';
        });

        // Garante que as bordas dos botões não carreguem seleções erradas
        const botoesSelecionados = document.querySelectorAll('.avaliacao-btn.selected');
        botoesSelecionados.forEach(btn => btn.classList.remove('selected'));
    }

    mostrarPergunta(perguntaAtual);

    btnProximo.addEventListener('click', function () {
        const hiddenField = perguntas[perguntaAtual].querySelector('input[type="hidden"]');
        if (!hiddenField.value) {
            alert("Por favor, selecione uma nota antes de avançar.");
            return;
        }

        perguntaAtual++;

        if (perguntaAtual < perguntas.length) {
            mostrarPergunta(perguntaAtual);
        } else {
            btnProximo.style.display = 'none';
            btnEnviar.style.display = 'block';
        }
    });
});

});
