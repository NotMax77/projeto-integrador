const cards = document.querySelector(".cards");

function scrollCards(direcao){

    const larguraCard =
        document.querySelector(".card-baba").offsetWidth + 18;

    cards.scrollLeft += direcao * larguraCard;

}