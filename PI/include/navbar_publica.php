<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$logado = isset($_SESSION['usuario_tipo'], $_SESSION['usuario_id']);
$tipo = $_SESSION['usuario_tipo'] ?? null;
$nome = $_SESSION['usuario_nome'] ?? '';
?>
<nav class="nav_inteira">
    <div class="minha_navbar">
        <div class="logo">
            <a href="<?= $tipo === 'baba' ? 'dashboard.php' : 'index.php' ?>">
                <img src="./img/teste.png" alt="Logo Babá Amiga">
            </a>
        </div>

        <div class="menu_direito">
            <?php if (!$logado): ?>
                <ul class="menu">
                    <li class="dropdown">
                        <a href="#">Cadastro</a>
                        <ul class="dropdown-content">
                            <li><a href="cadastrar-cliente.php">Cadastro cliente</a></li>
                            <li><a href="cadastro-babas.php">Cadastro babá</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="icone">
                    <a href="login.php" title="Entrar"><i class="fa-solid fa-right-to-bracket"></i></a>
                </div>
            <?php else: ?>
                <div class="usuario-nav">
                    <span>Olá, <?= htmlspecialchars(explode(' ', trim($nome))[0] ?: 'usuário', ENT_QUOTES, 'UTF-8') ?></span>
                    <a href="<?= $tipo === 'baba' ? 'perfil-baba.php' : 'perfil-cliente.php' ?>" title="Meu perfil">
                        <i class="fa-solid <?= $tipo === 'baba' ? 'fa-user-nurse' : 'fa-user' ?>"></i>
                    </a>
                    <a href="logout.php" title="Sair"><i class="fa-solid fa-right-from-bracket"></i></a>
                </div>
            <?php endif; ?>

            <div class="hamburguer" onclick="abrirMenu()">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </div>

    <div class="mini_nav">
        <ul class="menu_mini">
            <li><a href="guias.php">Guia para bebês</a></li>
            <?php if ($tipo !== 'baba'): ?>
                <li><a href="babas.php">Babás</a></li>
            <?php endif; ?>
            <?php if ($tipo === 'cliente'): ?>
                <li><a href="favoritos.php">Favoritos</a></li>
                <li><a href="historico_contrato.php">Histórico</a></li>
                <li><a href="dividas_pagamentos.php">Pagamentos</a></li>
            <?php elseif ($tipo === 'baba'): ?>
                <li><a href="clientes.php">Clientes</a></li>
                <li><a href="historico_trabalho.php">Histórico de trabalho</a></li>
                <li><a href="ganhos.php">Ganhos</a></li>
            <?php endif; ?>
            <li><a href="ajuda.php">Ajuda</a></li>
            <li><a href="sobre_nos.php">Sobre nós</a></li>
        </ul>
    </div>
</nav>

<div class="menu-mobile" id="menuMobile">
    <a href="index.php">🏠 Home</a>
    <a href="guias.php">👶 Guia para bebês</a>
    <?php if ($tipo !== 'baba'): ?>
        <a href="babas.php">👩 Babás</a>
    <?php endif; ?>
    <?php if ($tipo === 'cliente'): ?>
        <a href="favoritos.php">❤️ Favoritos</a>
        <a href="historico_contrato.php">📜 Histórico</a>
        <a href="dividas_pagamentos.php">💳 Pagamentos</a>
    <?php elseif ($tipo === 'baba'): ?>
        <a href="clientes.php">👨‍👩‍👧 Clientes</a>
        <a href="historico_trabalho.php">📜 Histórico</a>
        <a href="ganhos.php">💰 Ganhos</a>
    <?php endif; ?>
    <a href="ajuda.php">📞 Ajuda</a>
    <a href="sobre_nos.php">ℹ️ Sobre nós</a>
    <?php if ($logado): ?>
        <a href="<?= $tipo === 'baba' ? 'perfil-baba.php' : 'perfil-cliente.php' ?>">👤 Meu perfil</a>
        <a href="logout.php">🚪 Sair</a>
    <?php else: ?>
        <a href="login.php">🔐 Entrar</a>
    <?php endif; ?>
</div>