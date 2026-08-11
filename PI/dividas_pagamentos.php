<?php require_once __DIR__ . '/include/auth.php'; exigirTipo('cliente'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="CSS/dividas_pagamentos.css">
    <title>Document</title>
</head>

<body>
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>

    <section class="pagamentos">
        <h1>Dívidas e Pagamentos</h1>

        <p class="subtitulo">
            Consulte o histórico de pagamentos realizados e pendentes.
        </p>

        <!-- PESQUISA -->
        <section class="container-pesquisa">
            <form class="form_pesquisa">
                <input type="text" name="busca" placeholder="Pesquisar Babás..." required>
                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </section>

        <div class="card-pagamento">

            <div class="foto">
                <img src="img/baba.jpg">
            </div>

            <div class="coluna">
                <h4>Nome</h4>
                <p>Ana Silva</p>
            </div>

            <div class="coluna">
                <h4>Período</h4>
                <p>10/06/2026</p>
                <p>15/06/2026</p>
            </div>

            <div class="coluna">
                <h4>Valor</h4>
                <p>R$ 780,00</p>
            </div>

            <div class="coluna">
                <h4>Situação</h4>

                <span class="status pago">
                    Pago
                </span>

                <button>
                    Ver recibo
                </button>
            </div>
        </div>

        <div class="card-pagamento">

            <div class="foto">
                <img src="img/baba2.jpg">
            </div>

            <div class="coluna">
                <h4>Nome</h4>
                <p>Mariana Lima</p>
            </div>

            <div class="coluna">
                <h4>Período</h4>
                <p>20/05/2026</p>
                <p>22/05/2026</p>
            </div>

            <div class="coluna">
                <h4>Valor</h4>
                <p>R$ 540,00</p>
            </div>

            <div class="coluna">
                <h4>Situação</h4>

                <span class="status pendente">
                    Pendente
                </span>

                <button>
                    Efetuar pagamento
                </button>
            </div>
        </div>

        <div class="card-pagamento">

            <div class="foto">
                <img src="img/baba3.jpg">
            </div>

            <div class="coluna">
                <h4>Nome</h4>
                <p>Juliana Alves</p>
            </div>

            <div class="coluna">
                <h4>Período</h4>
                <p>05/05/2026</p>
                <p>05/05/2026</p>
            </div>

            <div class="coluna">
                <h4>Valor</h4>
                <p>R$ 250,00</p>
            </div>

            <div class="coluna">
                <h4>Situação</h4>

                <span class="status atrasado">
                    Atrasado
                </span>

                <button>
                    Pagar agora
                </button>
            </div>
        </div>
    </section>

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