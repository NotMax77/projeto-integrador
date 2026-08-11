<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="CSS/ajuda.css">
</head>

<body>
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>

    <main class="ajuda">
        <section class="cabecalho-ajuda">
            <h1>Central de Ajuda</h1>

            <p>
                Encontre respostas para as dúvidas mais frequentes sobre nossa plataforma.
            </p>
        </section>

        <section class="faq">
            <h2>Perguntas Frequentes</h2>

            <div class="faq-item">

                <button class="faq-pergunta">
                    Como contratar uma babá?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="faq-resposta">
                    Basta acessar a página "Babás", escolher a profissional desejada e clicar em "Contratar".
                </div>
            </div>

            <div class="faq-item">

                <button class="faq-pergunta">
                    Como faço meu cadastro?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="faq-resposta">
                    Clique em "Cadastro" no menu superior e escolha se deseja cadastrar uma conta de responsável ou de
                    babá.
                </div>
            </div>

            <div class="faq-item">

                <button class="faq-pergunta">
                    Como funciona o pagamento?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="faq-resposta">
                    O pagamento é realizado pela plataforma após a confirmação do serviço.
                </div>
            </div>

            <div class="faq-item">

                <button class="faq-pergunta">
                    Posso cancelar um serviço?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="faq-resposta">
                    Sim. O cancelamento pode ser feito pelo histórico de contratos, respeitando a política de
                    cancelamento.
                </div>
            </div>

            <div class="faq-item">

                <button class="faq-pergunta">
                    Como altero meus dados?
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="faq-resposta">
                    Após realizar o login, acesse seu perfil e clique em "Editar Perfil".
                </div>
            </div>
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
    
    <script src="js/ajuda.js"></script>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>