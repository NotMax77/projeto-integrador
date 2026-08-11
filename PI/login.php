<?php
declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';

if (usuarioLogado()) {
    header('Location: ' . ($_SESSION['usuario_tipo'] === 'baba' ? 'dashboard.php' : 'babas.php'));
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Informe seu e-mail e sua senha.';
    } else {
        $usuario = null;
        $tipo = null;

        $stmt = $conexao->prepare(
            "SELECT id_baba AS id, nome, sobrenome, email, senha, foto FROM BABA WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            $tipo = 'baba';
        } else {
            $stmt = $conexao->prepare(
                "SELECT id_cliente AS id, nome, sobrenome, email, senha, foto FROM CLIENTE WHERE email = ? LIMIT 1"
            );
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();
                $tipo = 'cliente';
            }
        }

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario_tipo'] = $tipo;
            $_SESSION['usuario_nome'] = $usuario['nome'] . ' ' . $usuario['sobrenome'];
            $_SESSION['usuario_foto'] = $usuario['foto'];

            header('Location: ' . ($tipo === 'baba' ? 'dashboard.php' : 'babas.php'));
            exit;
        }

        $erro = 'E-mail ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Babá Amiga</title>
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="CSS/navbar.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/include/navbar_publica.php'; ?>

<div class="container">
    <div class="card">
        <h2>Bem-vindo!</h2>
        <p class="subtitulo">Entre para encontrar a babá ideal ou acessar sua conta.</p>

        <?php if ($erro): ?>
            <div class="mensagem erro" style="margin-bottom: 18px; padding: 12px; border-radius: 8px; background:#ffe5e5; color:#9b1c1c;">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" placeholder="Digite sua senha" required>

            <button type="submit">Entrar</button>
        </form>

        <div class="cadastro">
            Ainda não possui uma conta?<br><br>
            <a href="cadastro-pais.html">Criar conta de cliente</a>
            <br>
            <a href="cadastro-babas.php">Criar conta de babá</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/include/footer.php'; ?>
</body>
</html>
