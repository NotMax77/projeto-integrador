<?php
require_once __DIR__ . '/../include/conexao.php';
require_once __DIR__ . '/../include/auth.php';
exigirLogin('baba');

$id = (int)$_SESSION['usuario_id'];
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $experiencia = trim($_POST['experiencia'] ?? '');

    if ($nome === '' || $sobrenome === '') {
        $erro = 'Nome e sobrenome são obrigatórios.';
    } else {
        $stmt = $conexao->prepare(
            "UPDATE BABA SET nome = ?, sobrenome = ?, telefone = ?, experiencia = ? WHERE id_baba = ?"
        );
        $stmt->bind_param('ssssi', $nome, $sobrenome, $telefone, $experiencia, $id);
        if ($stmt->execute()) {
            $_SESSION['nome_usuario'] = $nome;
            $mensagem = 'Perfil atualizado com sucesso.';
        } else {
            $erro = 'Não foi possível atualizar o perfil.';
        }
    }
}

$stmt = $conexao->prepare(
    "SELECT b.*, e.cep, e.estado, e.cidade, e.bairro, e.rua, e.numero, e.complemento
     FROM BABA b
     LEFT JOIN ENDERECO e ON e.id_endereco = b.id_endereco
     WHERE b.id_baba = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$baba = $stmt->get_result()->fetch_assoc();

if (!$baba) {
    http_response_code(404);
    exit('Babá não encontrada.');
}

$disponibilidade = [];
$stmtD = $conexao->prepare("SELECT horario FROM DISPONIBILIDADE WHERE id_baba = ?");
$stmtD->bind_param('i', $id);
$stmtD->execute();
$resD = $stmtD->get_result();
while ($row = $resD->fetch_assoc()) $disponibilidade[] = $row['horario'];

$preferencias = [];
$stmtP = $conexao->prepare(
    "SELECT p.descricao
     FROM BABA_PREFERENCIA bp
     INNER JOIN PREFERENCIA p ON p.id_preferencia = bp.id_preferencia
     WHERE bp.id_baba = ?"
);
if ($stmtP) {
    $stmtP->bind_param('i', $id);
    if ($stmtP->execute()) {
        $resP = $stmtP->get_result();
        while ($row = $resP->fetch_assoc()) $preferencias[] = $row['descricao'];
    }
}

$foto = !empty($baba['foto']) ? '../' . ltrim($baba['foto'], '/') : '../img/baba.jpg';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu perfil - Babá</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../CSS/navbar.css">
<link rel="stylesheet" href="../CSS/perfil.css">
</head>
<body>
<nav class="nav_inteira">
    <div class="minha_navbar">
        <div class="logo"><a href="../dashboard.php"><img src="../img/teste.png" alt="Logo"></a></div>
        <div class="menu_direito">
            <div class="icone">
                <a href="perfil.php" title="Meu perfil"><i class="fa-solid fa-user-nurse"></i></a>
                <a href="../logout.php" title="Sair"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
            <div class="hamburguer" onclick="abrirMenu()"><i class="fa-solid fa-bars"></i></div>
        </div>
    </div>
    <div class="mini_nav"><ul class="menu_mini">
        <li><a href="../dashboard.php">Início</a></li>
        <li><a href="../clientes.html">Clientes</a></li>
        <li><a href="../historico_trabalho.html">Histórico</a></li>
        <li><a href="../ganhos.html">Ganhos</a></li>
    </ul></div>
</nav>

<main class="perfil-container">
    <section class="perfil-card perfil-topo">
        <img class="perfil-foto" src="<?= escapar($foto) ?>" alt="Foto de <?= escapar($baba['nome']) ?>">
        <div>
            <span class="badge">Perfil da babá</span>
            <h1><?= escapar($baba['nome'] . ' ' . $baba['sobrenome']) ?></h1>
            <p><?= escapar($baba['email']) ?></p>
        </div>
    </section>

    <?php if ($mensagem): ?><div class="alert"><?= escapar($mensagem) ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="alert erro"><?= escapar($erro) ?></div><?php endif; ?>

    <section class="perfil-card">
        <h2>Informações profissionais</h2>
        <div class="grid-info">
            <div class="info-item"><strong>Telefone</strong><?= escapar($baba['telefone']) ?></div>
            <div class="info-item"><strong>Experiência</strong><?= nl2br(escapar($baba['experiencia'] ?: 'Não informado')) ?></div>
            <div class="info-item"><strong>Disponibilidade</strong><?= escapar(implode(' • ', $disponibilidade) ?: 'Não informado') ?></div>
            <div class="info-item"><strong>Preferências</strong><?= escapar(implode(', ', $preferencias) ?: 'Não informado') ?></div>
            <div class="info-item"><strong>Localização</strong><?= escapar(($baba['cidade'] ?? '') . ' - ' . ($baba['estado'] ?? '')) ?></div>
        </div>
    </section>

    <section class="perfil-card">
        <h2>Editar perfil</h2>
        <form class="perfil-form" method="POST">
            <label>Nome<input name="nome" value="<?= escapar($baba['nome']) ?>" required></label>
            <label>Sobrenome<input name="sobrenome" value="<?= escapar($baba['sobrenome']) ?>" required></label>
            <label>Telefone<input name="telefone" value="<?= escapar($baba['telefone']) ?>"></label>
            <label class="full">Experiência<textarea name="experiencia"><?= escapar($baba['experiencia']) ?></textarea></label>
            <div class="full"><button class="btn" type="submit">Salvar alterações</button></div>
        </form>
    </section>
</main>
<div class="menu-mobile" id="menuMobile">
    <a href="../dashboard.php">🏠 Início</a>
    <a href="../clientes.html">👨‍👩‍👧 Clientes</a>
    <a href="../historico_trabalho.html">📜 Histórico</a>
    <a href="../ganhos.html">💰 Ganhos</a>
    <a href="perfil.php">👤 Meu perfil</a>
    <a href="../logout.php">🚪 Sair</a>
</div>
<script>function abrirMenu(){document.getElementById('menuMobile').classList.toggle('ativo');}</script>
</body>
</html>
