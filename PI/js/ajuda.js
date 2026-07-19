const perguntas = document.querySelectorAll(".faq-pergunta");

perguntas.forEach(pergunta => {

    pergunta.addEventListener("click", () => {

        const item = pergunta.parentElement;

        item.classList.toggle("ativo");

    });

});