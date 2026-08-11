<?php

declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirTipo('cliente');

$mensagem = '';
$idCliente = (int)$_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = trim($_POST['telefone'] ?? '');
    $stmt = $conexao->prepare("UPDATE CLIENTE SET telefone = ? WHERE id_cliente = ?");
    $stmt->bind_param('si', $telefone, $idCliente);
    $stmt->execute();
    $mensagem = 'Perfil atualizado com sucesso!';
}

$stmt = $conexao->prepare(
    "SELECT c.*, e.cep, e.estado, e.cidade, e.bairro, e.rua, e.numero, e.complemento
     FROM CLIENTE c
     INNER JOIN ENDERECO e ON e.id_endereco = c.id_endereco
     WHERE c.id_cliente = ? LIMIT 1"
);
$stmt->bind_param('i', $idCliente);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

if (!$cliente) {
    http_response_code(404);
    exit('Cliente não encontrado.');
}

$foto = $cliente['foto'] ?: 'img/cliente.jpg';
$nomeCompleto = trim($cliente['nome'] . ' ' . $cliente['sobrenome']);
$idade = (new DateTime($cliente['data_nascimento']))->diff(new DateTime())->y;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil | Babá Amiga</title>
    <link rel="stylesheet" href="CSS/navbar.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>
    <main class="perfil-wrap">
        <?php if ($mensagem): ?><div class="alerta"><?= e($mensagem) ?></div><?php endif; ?>
        <section class="perfil-cabecalho">
            <img class="perfil-foto" src="<?= e($foto) ?>" alt="Foto de <?= e($nomeCompleto) ?>">
            <div>
                <span class="badge"><i class="fa-solid fa-user"></i> Cliente / responsável</span>
                <h1><?= e($nomeCompleto) ?></h1>
                <p><i class="fa-solid fa-location-dot"></i> <?= e($cliente['cidade'] . ' - ' . $cliente['estado']) ?></p>
                <div class="acoes-perfil"><a class="btn" href="babas.php">Encontrar babás</a><a class="btn secundario" href="logout.php">Sair</a></div>
            </div>
        </section>
        <div class="perfil-grid">
            <section class="perfil-card">
                <h2>Dados pessoais</h2>
                <ul class="dados-list">
                    <li><strong>Idade:</strong> <?= e((string)$idade) ?> anos</li>
                    <li><strong>E-mail:</strong> <?= e($cliente['email']) ?></li>
                    <li><strong>Telefone:</strong> <?= e($cliente['telefone']) ?></li>
                </ul>
            </section>
            <section class="perfil-card">
                <h2>Endereço</h2>
                <ul class="dados-list">
                    <li><?= e($cliente['rua'] . ', ' . $cliente['numero']) ?></li>
                    <li><?= e($cliente['bairro']) ?></li>
                    <li><?= e($cliente['cidade'] . ' - ' . $cliente['estado']) ?></li>
                    <li>CEP <?= e($cliente['cep']) ?></li>
                </ul>
            </section>
            <section class="perfil-card" style="grid-column:1/-1">
                <h2>Editar meu perfil</h2>
                <form class="form-edicao" method="POST">
                    <label for="telefone">Telefone</label>
                    <input id="telefone" name="telefone" value="<?= e($cliente['telefone']) ?>" required>
                    <button class="btn" type="submit">Salvar alterações</button>
                </form>
            </section>
        </div>
    </main>
    <?php include __DIR__ . '/include/footer.php'; ?>
</body>

</html>