<?php
declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirLogin();

function erroChat(string $mensagem, int $codigo = 400): never {
    http_response_code($codigo);
    exit($mensagem);
}

$tipoAtual = $_SESSION['usuario_tipo'];
$idAtual = (int) $_SESSION['usuario_id'];
$idBaba = (int) ($_GET['id_baba'] ?? 0);
$idCliente = (int) ($_GET['id_cliente'] ?? 0);

// O participante do outro lado é definido pelo tipo de quem está logado.
if ($tipoAtual === 'cliente') {
    if ($idBaba <= 0) {
        erroChat('É necessário informar a babá da conversa.');
    }
    $idCliente = $idAtual;
} else {
    if ($idCliente <= 0) {
        erroChat('É necessário informar o cliente da conversa.');
    }
    $idBaba = $idAtual;
}

// Busca os dados da outra pessoa.
if ($tipoAtual === 'cliente') {
    $stmt = $conexao->prepare(
        'SELECT id_baba, nome, sobrenome, foto FROM BABA WHERE id_baba = ? LIMIT 1'
    );
    $stmt->bind_param('i', $idBaba);
    $stmt->execute();
    $outro = $stmt->get_result()->fetch_assoc();
    $tipoOutro = 'baba';
} else {
    $stmt = $conexao->prepare(
        'SELECT id_cliente, nome, sobrenome, foto FROM CLIENTE WHERE id_cliente = ? LIMIT 1'
    );
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();
    $outro = $stmt->get_result()->fetch_assoc();
    $tipoOutro = 'cliente';
}

if (!$outro) {
    erroChat('A outra pessoa da conversa não foi encontrada.', 404);
}

// Localiza a conversa existente.
$stmt = $conexao->prepare(
    'SELECT id_conversa FROM CONVERSA WHERE id_cliente = ? AND id_baba = ? LIMIT 1'
);
$stmt->bind_param('ii', $idCliente, $idBaba);
$stmt->execute();
$rowConversa = $stmt->get_result()->fetch_assoc();

if ($rowConversa) {
    $idConversa = (int) $rowConversa['id_conversa'];
} else {
    $stmt = $conexao->prepare(
        'INSERT INTO CONVERSA (id_cliente, id_baba) VALUES (?, ?)'
    );
    if (!$stmt) {
        erroChat('Erro ao preparar a criação da conversa: ' . $conexao->error, 500);
    }
    $stmt->bind_param('ii', $idCliente, $idBaba);
    if (!$stmt->execute()) {
        // Se outra requisição criou a conversa primeiro, tenta encontrá-la novamente.
        if ((int)$conexao->errno === 1062) {
            $stmt2 = $conexao->prepare(
                'SELECT id_conversa FROM CONVERSA WHERE id_cliente = ? AND id_baba = ? LIMIT 1'
            );
            $stmt2->bind_param('ii', $idCliente, $idBaba);
            $stmt2->execute();
            $rowConversa = $stmt2->get_result()->fetch_assoc();
            if (!$rowConversa) {
                erroChat('Não foi possível criar a conversa: ' . $conexao->error, 500);
            }
            $idConversa = (int)$rowConversa['id_conversa'];
        } else {
            erroChat('Não foi possível criar a conversa: ' . $stmt->error, 500);
        }
    } else {
        $idConversa = (int) $conexao->insert_id;
    }
}

// Envio: POST normal, sem depender de JavaScript.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = trim((string)($_POST['mensagem'] ?? ''));

    if ($texto !== '') {
        $remetente = $tipoAtual === 'cliente' ? 'cliente' : 'baba';
        $stmt = $conexao->prepare(
            'INSERT INTO MENSAGEM (id_conversa, remetente, mensagem, data_hora) VALUES (?, ?, ?, NOW())'
        );
        if (!$stmt) {
            erroChat('Erro ao preparar o envio da mensagem: ' . $conexao->error, 500);
        }
        $stmt->bind_param('iss', $idConversa, $remetente, $texto);
        if (!$stmt->execute()) {
            erroChat('Erro ao salvar a mensagem: ' . $stmt->error, 500);
        }
    }

    header('Location: conversa.php?' . ($tipoAtual === 'cliente' ? 'id_baba=' . $idBaba : 'id_cliente=' . $idCliente));
    exit;
}

function carregarMensagens(mysqli $conexao, int $idConversa): array {
    $stmt = $conexao->prepare(
        'SELECT id_mensagem, remetente, mensagem, data_hora
         FROM MENSAGEM
         WHERE id_conversa = ?
         ORDER BY id_mensagem ASC'
    );
    $stmt->bind_param('i', $idConversa);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$mensagens = carregarMensagens($conexao, $idConversa);
$nomeOutro = trim($outro['nome'] . ' ' . $outro['sobrenome']);
$fotoOutro = $outro['foto'] ?: ($tipoOutro === 'baba' ? 'img/baba.jpg' : 'img/cliente.jpg');
$urlAtualizacao = 'conversa.php?' . ($tipoAtual === 'cliente' ? 'id_baba=' . $idBaba : 'id_cliente=' . $idCliente);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Conversa com <?= e($nomeOutro) ?> | Babá Amiga</title>
<link rel="stylesheet" href="CSS/navbar.css">
<link rel="stylesheet" href="CSS/footer.css">
<link rel="stylesheet" href="CSS/conversa.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/include/navbar_publica.php'; ?>

<main class="chat">
    <aside class="perfil">
        <img src="<?= e($fotoOutro) ?>" alt="Foto de <?= e($nomeOutro) ?>">
        <h2><?= e($nomeOutro) ?></h2>
        <p class="tipo-pessoa"><?= $tipoOutro === 'baba' ? 'Babá' : 'Cliente' ?></p>
        <a class="btn-voltar" href="<?= $tipoAtual === 'cliente' ? 'perfil-baba.php?id=' . $idBaba : 'perfil-cliente-publico.php?id=' . $idCliente ?>">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao perfil
        </a>
    </aside>

    <section class="conversa">
        <header class="topo-chat">
            <div>
                <h3><?= e($nomeOutro) ?></h3>
                <span>Conversa privada</span>
            </div>
        </header>

        <div class="mensagens" id="mensagens">
            <?php if (!$mensagens): ?>
                <div class="sem-mensagens">Nenhuma mensagem ainda. Envie a primeira!</div>
            <?php endif; ?>

            <?php foreach ($mensagens as $msg): ?>
                <div class="msg <?= $msg['remetente'] === $tipoAtual ? 'minha' : 'outra' ?>">
                    <div><?= nl2br(e($msg['mensagem'])) ?></div>
                    <time><?= e(date('d/m/Y H:i', strtotime($msg['data_hora']))) ?></time>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="enviar" method="POST" action="<?= e($urlAtualizacao) ?>">
            <input type="text" name="mensagem" maxlength="2000" placeholder="Digite sua mensagem..." autocomplete="off" required>
            <button type="submit" aria-label="Enviar mensagem"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/include/footer.php'; ?>
<script>
const caixa = document.getElementById('mensagens');
if (caixa) caixa.scrollTop = caixa.scrollHeight;

// Atualiza somente a visualização. O envio é feito pelo POST acima.
setInterval(() => {
    fetch(<?= json_encode($urlAtualizacao . '&ajax=1') ?>, {cache: 'no-store'})
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const novas = doc.getElementById('mensagens');
            if (novas && caixa && novas.innerHTML !== caixa.innerHTML) {
                const estavaNoFim = caixa.scrollHeight - caixa.scrollTop - caixa.clientHeight < 80;
                caixa.innerHTML = novas.innerHTML;
                if (estavaNoFim) caixa.scrollTop = caixa.scrollHeight;
            }
        })
        .catch(() => {});
}, 1500);
</script>
</body>
</html>
