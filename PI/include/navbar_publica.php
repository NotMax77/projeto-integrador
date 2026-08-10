<nav class="nav_inteira">
    <div class="minha_navbar">
        <!-- LOGO -->
        <div class="logo">
            <a href="index.html">
                <img src="./img/teste.png" alt="Logo Artifex">
            </a>
        </div>

        <div class="menu_direito">
            <!-- MENU -->
            <ul class="menu">
                <li class="dropdown">

                    <a href="#">Cadastro</a>
                    <ul class="dropdown-content">
                        <li><a href="cadastro-pais.html">Cadastro cliente</a></li>
                        <li><a href="cadastro-babas.html">Cadastro Babá</a></li>
                    </ul>
                </li>
            </ul>

            <!-- ÍCONE -->
            <div class="icone">
                <a href="login.html" title="Entrar como responsável">
                    <i class="fa-solid fa-user"></i>
                </a>

                <a href="" title="Entrar como babá">
                    <i class="fa-solid fa-user-nurse"></i>
                </a>
            </div>

            <div class="hamburguer" onclick="abrirMenu()">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </div>

    <!-- MINI NAVBAR -->
    <div class="mini_nav">
        <ul class="menu_mini">
            <li><a href="guias.html">Guia para bebês</a></li>
            <li><a href="favoritos.html">Favoritos</a></li>
            <li><a href="babas.html">Babás</a></li>
            <li><a href="ajuda.html">Ajuda</a></li>
            <li><a href="sua_localizacao.html">Sua localização</a></li>

            <li class="dropdown">

                <a href="#">Histórico</a>
                <ul class="dropdown-content">
                    <li><a href="historico_contrato.html">Histórico de contrato</a></li>
                    <li><a href="dividas_pagamentos.html">Dívidas e pagamentos​</a></li>
                </ul>
            </li>

            <li><a href="sobre_nos.htm">Sobre Nós</a></li>
        </ul>
    </div>
</nav>


<!-- MENU MOBILE -->
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