document.addEventListener("DOMContentLoaded", function() {
function selecionarNota(perguntaId, nota) {
    const botoes = document.querySelectorAll(`#pergunta-${perguntaId} .avaliacao-btn`);
    botoes.forEach(btn => btn.classList.remove('selected'));

    const btnSelecionado = document.querySelector(`#pergunta-${perguntaId} .avaliacao-btn[data-value="${nota}"]`);
    btnSelecionado.classList.add('selected');

    document.getElementById(`nota-${perguntaId}`).value = nota;
}

document.addEventListener("DOMContentLoaded", function () {
    let perguntaAtual = 0;
    const perguntas = document.querySelectorAll('.pergunta');
    const btnProximo = document.getElementById('proximo-btn');
    const btnEnviar = document.getElementById('enviar-btn');

    function mostrarPergunta(index) {
        perguntas.forEach((pergunta, i) => {
            pergunta.style.display = (i === index) ? 'block' : 'none';
        });

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
