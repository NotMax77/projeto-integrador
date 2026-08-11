<?php require_once __DIR__ . '/include/auth.php'; exigirTipo('baba'); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="CSS/ganhos.css">

    <title>Clientes</title>
</head>

<body>
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>

    <section class="ganhos">
        <h1>Ganhos e Pagamentos</h1>

        <p class="subtitulo">
            Acompanhe seus recebimentos.
        </p>

        <div class="resumo">

            <div class="card-resumo">
                <h3>Ganhos do mês</h3>
                <span>R$ 2.850,00</span>
            </div>

            <div class="card-resumo">
                <h3>Total recebido</h3>
                <span>R$ 14.320,00</span>
            </div>

            <div class="card-resumo">
                <h3>Serviços realizados</h3>
                <span>18</span>
            </div>
        </div>

        <div class="card-pagamento">

            <div class="cliente">
                <img src="img/cliente1.jpg" alt="">
                <h3>Ana Oliveira</h3>
            </div>

            <div class="valor">
                <strong>R$ 180,00</strong>
            </div>

            <div class="status recebido">
                Recebido
            </div>
        </div>

        <div class="card-pagamento">

            <div class="cliente">
                <img src="img/cliente2.jpg" alt="">
                <h3>Carlos Mendes</h3>
            </div>

            <div class="valor">
                <strong>R$ 250,00</strong>
            </div>

            <div class="status recebido">
                Recebido
            </div>
        </div>

        <div class="card-pagamento">

            <div class="cliente">
                <img src="img/cliente3.jpg" alt="">
                <h3>Fernanda Lima</h3>
            </div>

            <div class="valor">
                <strong>R$ 320,00</strong>
            </div>

            <div class="status pendente">
                Pendente
            </div>
        </div>
    </section>

    <!-- FOOTER -->
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
        <a href="dashboard.html">🏠 Home</a>
        <a href="guias.html">👶 Guia para bebês</a>
        <a href="clientes.html">❤️ Clientes</a>
        <a href="ajuda.html">📞 Ajuda</a>
        <a href="sua_localizacao.html">📍 Sua localização</a>
        <a href="historico_trabalho.html">📜 Histórico de Trabalho</a>
        <a href="ganhos.html">💳 Ganhos</a>
        <a href="sobre_nos.htm">ℹ️ Sobre nós</a>
    </div>

    <script src="js/celular.js"></script>
</body>

</html>