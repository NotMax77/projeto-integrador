<?php
require_once __DIR__ . '/include/conexao.php';
require_once __DIR__ . '/include/auth.php';
exigirLogin('cliente');

$id = (int)$_SESSION['usuario_id'];
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '' || $sobrenome === '') {
        $erro = 'Nome e sobrenome são obrigatórios.';
    } else {
        $stmt = $conexao->prepare(
            "UPDATE CLIENTE SET nome = ?, sobrenome = ?, telefone = ? WHERE id_cliente = ?"
        );
        $stmt->bind_param('sssi', $nome, $sobrenome, $telefone, $id);
        if ($stmt->execute()) {
            $_SESSION['nome_usuario'] = $nome;
            $mensagem = 'Perfil atualizado com sucesso.';
        } else {
            $erro = 'Não foi possível atualizar o perfil.';
        }
    }
}

$stmt = $conexao->prepare(
    "SELECT c.*, e.cep, e.estado, e.cidade, e.bairro, e.rua, e.numero, e.complemento
     FROM CLIENTE c
     LEFT JOIN ENDERECO e ON e.id_endereco = c.id_endereco
     WHERE c.id_cliente = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

if (!$cliente) {
    http_response_code(404);
    exit('Cliente não encontrado.');
}

$foto = !empty($cliente['foto']) ? $cliente['foto'] : 'img/cliente.jpg';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu perfil - Cliente</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="CSS/navbar.css">
<link rel="stylesheet" href="CSS/perfil.css">
</head>
<body>
<?php include 'include/navbar_cliente.php'; ?>
<main class="perfil-container">
    <section class="perfil-card perfil-topo">
        <img class="perfil-foto" src="<?= escapar($foto) ?>" alt="Foto de <?= escapar($cliente['nome']) ?>">
        <div>
            <span class="badge">Perfil do cliente</span>
            <h1><?= escapar($cliente['nome'] . ' ' . $cliente['sobrenome']) ?></h1>
            <p><?= escapar($cliente['email']) ?></p>
        </div>
    </section>

    <?php if ($mensagem): ?><div class="alert"><?= escapar($mensagem) ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="alert erro"><?= escapar($erro) ?></div><?php endif; ?>

    <section class="perfil-card">
        <h2>Meus dados</h2>
        <div class="grid-info">
            <div class="info-item"><strong>Telefone</strong><?= escapar($cliente['telefone']) ?></div>
            <div class="info-item"><strong>CPF</strong><?= escapar($cliente['cpf']) ?></div>
            <div class="info-item"><strong>Data de nascimento</strong><?= escapar($cliente['data_nascimento']) ?></div>
            <div class="info-item"><strong>Localização</strong><?= escapar(($cliente['cidade'] ?? '') . ' - ' . ($cliente['estado'] ?? '')) ?></div>
            <div class="info-item"><strong>Endereço</strong><?= escapar(($cliente['rua'] ?? '') . ', ' . ($cliente['numero'] ?? '')) ?></div>
        </div>
    </section>

    <section class="perfil-card">
        <h2>Editar perfil</h2>
        <form class="perfil-form" method="POST">
            <label>Nome<input name="nome" value="<?= escapar($cliente['nome']) ?>" required></label>
            <label>Sobrenome<input name="sobrenome" value="<?= escapar($cliente['sobrenome']) ?>" required></label>
            <label class="full">Telefone<input name="telefone" value="<?= escapar($cliente['telefone']) ?>"></label>
            <div class="full"><button class="btn" type="submit">Salvar alterações</button></div>
        </form>
    </section>
</main>
<script>
function abrirMenu(){document.getElementById('menuMobile').classList.toggle('ativo');}
</script>
</body>
</html>
