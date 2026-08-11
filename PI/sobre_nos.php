<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="CSS/sobre_nos.css">
    <title>Document</title>
</head>

<body>
    <!-- NAVBAR -->
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>

    <main class="sobre">
        <section class="quem-somos">
            <div class="texto">
                <h1>Quem Somos</h1>

                <p>
                    Somos uma plataforma criada para conectar famílias e babás de forma
                    simples, segura e eficiente. Nosso objetivo é facilitar a busca por
                    profissionais qualificadas, oferecendo uma experiência prática tanto
                    para quem procura uma babá quanto para quem deseja oferecer seus
                    serviços.
                </p>

                <p>
                    Acreditamos que confiança é essencial quando se trata do cuidado com
                    crianças. Por isso, buscamos proporcionar um ambiente organizado,
                    onde famílias possam encontrar profissionais com informações claras,
                    avaliações e perfis completos.
                </p>

                <p>
                    Além de auxiliar responsáveis na contratação, também valorizamos o
                    trabalho das babás, oferecendo um espaço para divulgação de suas
                    experiências, habilidades e disponibilidade, ampliando suas
                    oportunidades de trabalho.
                </p>
            </div>

            <div class="imagem">
                <img src="img/familia_feliz.png" alt="Família feliz">
            </div>
        </section>

        <section class="missao">

            <div class="card">
                <i class="fa-solid fa-heart"></i>

                <h3>Missão</h3>

                <p>
                    Aproximar famílias e profissionais por meio de uma plataforma
                    segura e eficiente.
                </p>
            </div>

            <div class="card">
                <i class="fa-solid fa-eye"></i>
                <h3>Visão</h3>

                <p>
                    Ser referência nacional na contratação de babás.
                </p>
            </div>

            <div class="card">
                <i class="fa-solid fa-shield-heart"></i>
                <h3>Valores</h3>

                <p>
                    Segurança, respeito, confiança e transparência.
                </p>
            </div>
        </section>

        <section class="como-funciona">
            <h2>Como Funciona</h2>

            <div class="passos">

                <div class="passo">
                    <span>1</span>
                    <h4>Cadastre-se</h4>

                    <p>
                        Crie sua conta como responsável ou babá.
                    </p>
                </div>

                <div class="passo">
                    <span>2</span>
                    <h4>Encontre</h4>

                    <p>
                        Utilize os filtros para localizar a profissional ideal.
                    </p>
                </div>

                <div class="passo">
                    <span>3</span>
                    <h4>Contrate</h4>

                    <p>
                        Entre em contato e acompanhe todo o histórico pela plataforma.
                    </p>
                </div>
            </div>
        </section>

        <section class="cta">
            <h2>Pronto para encontrar a babá ideal?</h2>

            <p>
                Faça seu cadastro e encontre profissionais qualificadas perto de você.
            </p>

            <button>Começar Agora</button>
        </section>
    </main>

    <footer class="footer">

        <div class="footer-container">

            <div class="footer-logo">
                <img src="img/teste.png" alt="Logo">
                <p>Conectando famílias e babás com segurança.</p>
            </div>

            <div class="footer-menu">
                <a href="index.html">Home</a>
                <a href="babas.html">Babás</a>
                <a href="ajuda.html">Ajuda</a>
                <a href="sobre_nos.html">Sobre Nós</a>
            </div>

            <div class="footer-redes">
                <a href="https://facebook.com" target="_blank" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>

                <a href="https://instagram.com" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>

                <a href="https://x.com" target="_blank" aria-label="X">
                    <i class="fab fa-x-twitter"></i>
                </a>

                <a href="https://web.whatsapp.com" target="_blank" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>

        <div class="footer-copy">
            © 2026 Babá Amiga • Todos os direitos reservados.
        </div>
    </footer>

    <!-- MENU DO CELULAR -->
    <div class="menu-mobile" id="menuMobile">
        <a href="index.html">🏠 Home</a>
        <a href="guias.html">👶 Guia para bebês</a>
        <a href="favoritos.html">❤️ Favoritos</a>
        <a href="babas.html">👩 Babás</a>
        <a href="ajuda.html">📞 Ajuda</a>
        <a href="sua_localizacao.html">📍 Sua localização</a>
        <a href="historico_contrato.html">📜 Histórico</a>
        <a href="dividas_pagamentos.html">💳 Pagamentos</a>
        <a href="sobre_nos.htm">ℹ️ Sobre nós</a>
    </div>

    <script src="js/celular.js"></script>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>