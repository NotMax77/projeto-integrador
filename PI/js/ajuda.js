const perguntas = document.querySelectorAll(".faq-pergunta"); /*procura todas as perguntas do FAQ.*/
perguntas.forEach(pergunta => { /*Percorre cada pergunta encontrada.*/

    pergunta.addEventListener("click", () => { /*quando o usuário clicar em uma pergunta, execute o código abaixo.*/

        const item = pergunta.parentElement; /*pega o elemento pai da pergunta*/

        item.classList.toggle("ativo"); /*adiciona ou remove a classe ativo*/
    });
});
/*foreach, para cada elemento da lista, faça alguma coisa*/
/*pergunta =>, representa uma pergunta da lista por vez*/

function abrirMenu(){
    document
    .getElementById("menuMobile")
    .classList
    .toggle("ativo");
}