const cards = document.querySelector(".cards");
function scrollCards(direcao){

    const larguraCard =
        document.querySelector(".card-baba").offsetWidth + 18;

    cards.scrollLeft += direcao * larguraCard;
}

function abrirMenu(){
    document
    .getElementById("menuMobile")
    .classList
    .toggle("ativo"); /*toggle, se a classe não existe, ele adiciona.
    Se a classe já existe, ele remove*/
}