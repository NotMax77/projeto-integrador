<?php
include 'include/navbar_publica.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/navbar_publica.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <title>Document</title>
</head>

<body>
    <!-- MAPA -->
    <section class="mapa-babas">
        <div class="titulo-mapa">
            <h2>Encontre uma babá perto de você</h2>
            <p>Veja no mapa onde estão as babás cadastradas.</p>
        </div>

        <div class="mapa">
            <div class="marcador marcador1"></div>
            <div class="marcador marcador2"></div>
            <div class="marcador marcador3"></div>
            <div class="marcador marcador4"></div>
        </div>
    </section>

    <!-- PESQUISA -->
    <section class="container-pesquisa">
        <form class="form_pesquisa">
            <input type="text" name="busca" placeholder="Pesquisar Babás..." required>
            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </section>

    <h2 class="titulo">Babás mais avaliadas</h2>

    <!-- Babás mais avaliadas -->
    <section class="mais-avaliadas">
        <div class="carrossel">

            <!-- SETA ESQUERDA -->
            <button class="seta esquerda" onclick="scrollCards(-1)">
                ❮
            </button>

            <div class="cards" id="cards">
                <div class="card-baba">
                    <img src="img/baba.jpg" alt="Babá">

                    <div class="info">
                        <h3>Ana Souza</h3>
                        <p class="cidade">
                            <i class="fa-solid fa-location-dot"></i>
                            São Paulo - SP
                        </p>
                        <p class="avaliacao">
                            ⭐ 4.9
                        </p>
                        <a href="informacao_baba.html"><button>Ver Perfil</button></a>
                    </div>
                </div>

                <div class="card-baba">
                    <img src="img/Mídia.jpg" alt="Babá">

                    <div class="info">
                        <h3>Mariana Lima</h3>
                        <p class="cidade">
                            <i class="fa-solid fa-location-dot"></i>
                            Campinas - SP
                        </p>
                        <p class="avaliacao">
                            ⭐ 4.9
                        </p>
                        <button>Ver Perfil</button>
                    </div>
                </div>

                <div class="card-baba">
                    <img src="img/Mídia (1).jpg" alt="Babá">

                    <div class="info">
                        <h3>Carla Mendes</h3>
                        <p class="cidade">
                            <i class="fa-solid fa-location-dot"></i>
                            Sorocaba - SP
                        </p>
                        <p class="avaliacao">
                            ⭐ 4.8
                        </p>
                        <button>Ver Perfil</button>
                    </div>
                </div>

                <div class="card-baba">
                    <img src="img/Mídia (2).jpg" alt="Babá">

                    <div class="info">
                        <h3>Ana Silva</h3>
                        <p class="cidade">
                            <i class="fa-solid fa-location-dot"></i>
                            Sorocaba - SP
                        </p>
                        <p class="avaliacao">
                            ⭐ 4.8
                        </p>
                        <button>Ver Perfil</button>
                    </div>
                </div>

                <div class="card-baba">
                    <img src="img/Mídia (3).jpg" alt="Babá">

                    <div class="info">
                        <h3>Josefina Rodrigues</h3>
                        <p class="cidade">
                            <i class="fa-solid fa-location-dot"></i>
                            Sorocaba - SP
                        </p>
                        <p class="avaliacao">
                            ⭐ 4.8
                        </p>
                        <button>Ver Perfil</button>
                    </div>
                </div>

                <div class="card-baba">
                    <img src="img/baba3.jpg" alt="Babá">

                    <div class="info">
                        <h3>Duda Santos</h3>
                        <p class="cidade">
                            <i class="fa-solid fa-location-dot"></i>
                            Sorocaba - SP
                        </p>
                        <p class="avaliacao">
                            ⭐ 4.8
                        </p>
                        <button>Ver Perfil</button>
                    </div>
                </div>
            </div>

            <!-- SETA DIREITA -->
            <button class="seta direita" onclick="scrollCards(1)">
                ❯
            </button>
        </div>
    </section>

    <script src="js/index.js"></script>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
include 'include/footer.php';
?>