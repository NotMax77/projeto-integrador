<?php

declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirLogin();

$ehDono = $_SESSION['usuario_tipo'] === 'baba' && !isset($_GET['id']);
$idBaba = $ehDono ? (int)$_SESSION['usuario_id'] : (int)($_GET['id'] ?? 0);

if ($idBaba <= 0) {
    header('Location: babas.php');
    exit;
}

$mensagem = '';
$erroAvaliacao = '';

// Apenas clientes que possuem um contrato com esta babá podem avaliar.
if ($_SESSION['usuario_tipo'] === 'cliente' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_avaliacao'])) {
    $idClienteAtual = (int)$_SESSION['usuario_id'];
    $nota = (float)($_POST['nota'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');
    $idContrato = (int)($_POST['id_contrato'] ?? 0);

    if ($nota < 1 || $nota > 5) {
        $erroAvaliacao = 'Escolha uma nota entre 1 e 5 estrelas.';
    } elseif ($idContrato <= 0) {
        $erroAvaliacao = 'Contrato inválido.';
    } elseif (mb_strlen($comentario) > 1000) {
        $erroAvaliacao = 'O comentário pode ter no máximo 1000 caracteres.';
    } else {
        $stmt = $conexao->prepare(
            "SELECT c.id_contrato
             FROM CONTRATO c
             INNER JOIN PROPOSTA p ON p.id_proposta = c.id_proposta
             INNER JOIN CONVERSA cv ON cv.id_conversa = p.id_conversa
             WHERE c.id_contrato = ? AND cv.id_cliente = ? AND cv.id_baba = ?
             LIMIT 1"
        );
        $stmt->bind_param('iii', $idContrato, $idClienteAtual, $idBaba);
        $stmt->execute();
        $contratoValido = $stmt->get_result()->fetch_assoc();

        if (!$contratoValido) {
            $erroAvaliacao = 'Você não pode avaliar esta babá usando este contrato.';
        } else {
            $stmt = $conexao->prepare("SELECT id_avaliacao FROM AVALIACAO WHERE id_contrato = ? LIMIT 1");
            $stmt->bind_param('i', $idContrato);
            $stmt->execute();
            $jaAvaliou = $stmt->get_result()->fetch_assoc();

            if ($jaAvaliou) {
                $erroAvaliacao = 'Este contrato já foi avaliado.';
            } else {
                $stmt = $conexao->prepare(
                    "INSERT INTO AVALIACAO (id_contrato, id_cliente, id_baba, nota, comentario) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->bind_param('iiids', $idContrato, $idClienteAtual, $idBaba, $nota, $comentario);
                if ($stmt->execute()) {
                    $mensagem = 'Avaliação publicada com sucesso! Obrigado pelo seu feedback.';
                } else {
                    $erroAvaliacao = 'Não foi possível publicar sua avaliação. Tente novamente.';
                }
            }
        }
    }
}
if ($ehDono && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = trim($_POST['telefone'] ?? '');
    $experiencia = trim($_POST['experiencia'] ?? '');

    $stmt = $conexao->prepare("UPDATE BABA SET telefone = ?, experiencia = ? WHERE id_baba = ?");
    $stmt->bind_param('ssi', $telefone, $experiencia, $idBaba);
    $stmt->execute();
    $mensagem = 'Perfil atualizado com sucesso!';
}

$stmt = $conexao->prepare(
    "SELECT b.*, e.cep, e.estado, e.cidade, e.bairro, e.rua, e.numero, e.complemento
     FROM BABA b
     INNER JOIN ENDERECO e ON e.id_endereco = b.id_endereco
     WHERE b.id_baba = ? LIMIT 1"
);
$stmt->bind_param('i', $idBaba);
$stmt->execute();
$baba = $stmt->get_result()->fetch_assoc();

if (!$baba) {
    http_response_code(404);
    exit('Babá não encontrada.');
}

$stmt = $conexao->prepare(
    "SELECT ROUND(AVG(nota),1) media, COUNT(*) total
     FROM AVALIACAO WHERE id_baba = ?"
);
$stmt->bind_param('i', $idBaba);
$stmt->execute();
$avaliacao = $stmt->get_result()->fetch_assoc();

$stmt = $conexao->prepare(
    "SELECT horario FROM DISPONIBILIDADE WHERE id_baba = ? ORDER BY FIELD(horario,'manha','tarde','noite')"
);
$stmt->bind_param('i', $idBaba);
$stmt->execute();
$disponibilidades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conexao->prepare(
    "SELECT p.descricao FROM BABA_PREFERENCIA bp
     INNER JOIN PREFERENCIA p ON p.id_preferencia = bp.id_preferencia
     WHERE bp.id_baba = ? ORDER BY p.descricao"
);
$stmt->bind_param('i', $idBaba);
$stmt->execute();
$preferencias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Contratos do cliente atual com a babá, indicando quais ainda podem ser avaliados.
$contratosAvaliacao = [];
if ($_SESSION['usuario_tipo'] === 'cliente') {
    $idClienteAtual = (int)$_SESSION['usuario_id'];
    $stmt = $conexao->prepare(
        "SELECT c.id_contrato, c.numero_contrato, c.data_geracao, a.id_avaliacao
         FROM CONTRATO c
         INNER JOIN PROPOSTA p ON p.id_proposta = c.id_proposta
         INNER JOIN CONVERSA cv ON cv.id_conversa = p.id_conversa
         LEFT JOIN AVALIACAO a ON a.id_contrato = c.id_contrato
         WHERE cv.id_cliente = ? AND cv.id_baba = ?
         ORDER BY c.data_geracao DESC"
    );
    $stmt->bind_param('ii', $idClienteAtual, $idBaba);
    $stmt->execute();
    $contratosAvaliacao = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Comentários públicos deixados pelos clientes.
$stmt = $conexao->prepare(
    "SELECT a.nota, a.comentario, a.data_avaliacao, c.nome, c.sobrenome
     FROM AVALIACAO a
     INNER JOIN CLIENTE c ON c.id_cliente = a.id_cliente
     WHERE a.id_baba = ? AND a.comentario IS NOT NULL AND TRIM(a.comentario) <> ''
     ORDER BY a.data_avaliacao DESC"
);
$stmt->bind_param('i', $idBaba);
$stmt->execute();
$comentarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$foto = $baba['foto'] ?: 'img/baba.jpg';
$nomeCompleto = trim($baba['nome'] . ' ' . $baba['sobrenome']);
$media = $avaliacao['media'] ?? null;
$totalAvaliacoes = (int)($avaliacao['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= e($nomeCompleto) ?> | Babá Amiga</title>
    <link rel="stylesheet" href="CSS/navbar.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/perfil.css?v=20260813">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <?php include __DIR__ . '/include/navbar_publica.php'; ?>

    <main class="perfil-wrap">
        <?php if ($mensagem): ?><div class="alerta"><?= e($mensagem) ?></div><?php endif; ?>

        <section class="perfil-cabecalho">
            <img class="perfil-foto" src="<?= e($foto) ?>" alt="Foto de <?= e($nomeCompleto) ?>">
            <div>
                <span class="badge"><i class="fa-solid fa-user-nurse"></i> Babá</span>
                <h1><?= e($nomeCompleto) ?></h1>
                <p><i class="fa-solid fa-location-dot"></i> <?= e($baba['cidade'] . ' - ' . $baba['estado']) ?></p>
                <p><?= $media !== null ? '⭐ ' . e((string)$media) . ' (' . $totalAvaliacoes . ' avaliações)' : 'Ainda sem avaliações' ?></p>
                <div class="acoes-perfil">
                    <?php if ($_SESSION['usuario_tipo'] === 'cliente'): ?>
                        <a class="btn" href="conversa.php?id_baba=<?= (int)$idBaba ?>"><i class="fa-solid fa-comments"></i> Conversar</a>
                        <a class="btn secundario" href="babas.php">Voltar para babás</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <div class="perfil-grid">
            <section class="perfil-card">
                <h2>Sobre mim</h2>
                <p><?= nl2br(e($baba['experiencia'] ?: 'Esta babá ainda não adicionou uma descrição.')) ?></p>
            </section>

            <section class="perfil-card">
                <h2>Informações</h2>
                <ul class="dados-list">
                    <li><strong>Idade:</strong> <?= e((string)(new DateTime($baba['data_nascimento']))->diff(new DateTime())->y) ?> anos</li>
                    <li><strong>Telefone:</strong> <?= e($baba['telefone']) ?></li>
                    <li><strong>E-mail:</strong> <?= e($baba['email']) ?></li>
                    <li><strong>Localização:</strong> <?= e($baba['bairro'] . ', ' . $baba['cidade'] . '/' . $baba['estado']) ?></li>
                </ul>
            </section>

            <section class="perfil-card">
                <h2>Disponibilidade</h2>
                <ul class="dados-list">
                    <?php if ($disponibilidades): foreach ($disponibilidades as $item): ?>
                            <li><?= e(ucfirst(str_replace('manha', 'manhã', $item['horario']))) ?></li>
                        <?php endforeach;
                    else: ?>
                        <li>Não informada</li>
                    <?php endif; ?>
                </ul>
            </section>

            <section class="perfil-card">
                <h2>Preferências</h2>
                <ul class="dados-list">
                    <?php if ($preferencias): foreach ($preferencias as $item): ?>
                            <li><?= e($item['descricao']) ?></li>
                        <?php endforeach;
                    else: ?>
                        <li>Não informadas</li>
                    <?php endif; ?>
                </ul>
            </section>

            <?php if ($erroAvaliacao): ?><div class="alerta alerta-erro" style="grid-column:1/-1"><?= e($erroAvaliacao) ?></div><?php endif; ?>

            <?php if ($_SESSION['usuario_tipo'] === 'cliente' && $contratosAvaliacao): ?>
                <section class="perfil-card avaliacao-card" style="grid-column:1/-1">
                    <h2>Avaliar esta babá</h2>
                    <?php
                    $contratoDisponivel = null;
                    foreach ($contratosAvaliacao as $contrato) {
                        if (empty($contrato['id_avaliacao'])) {
                            $contratoDisponivel = $contrato;
                            break;
                        }
                    }
                    ?>
                    <?php if ($contratoDisponivel): ?>
                        <p>Você já teve um contrato com esta babá? Conte como foi sua experiência.</p>
                        <form class="form-avaliacao" method="POST">
                            <input type="hidden" name="enviar_avaliacao" value="1">
                            <input type="hidden" name="id_contrato" value="<?= (int)$contratoDisponivel['id_contrato'] ?>">

                            <div class="campo-avaliacao campo-nota">
                                <label for="nota">Sua nota</label>
                                <select id="nota" name="nota" required>
                                    <option value="">Selecione</option>
                                    <option value="5">★★★★★ — Excelente</option>
                                    <option value="4">★★★★☆ — Muito boa</option>
                                    <option value="3">★★★☆☆ — Boa</option>
                                    <option value="2">★★☆☆☆ — Regular</option>
                                    <option value="1">★☆☆☆☆ — Ruim</option>
                                </select>
                            </div>

                            <div class="campo-avaliacao campo-comentario">
                                <label for="comentario">Comentário</label>
                                <textarea id="comentario" name="comentario" maxlength="1000" placeholder="Conte como foi sua experiência..."></textarea>
                            </div>

                            <button class="btn" type="submit"><i class="fa-solid fa-star"></i> Publicar avaliação</button>
                        </form>
                    <?php else: ?>
                        <p class="sem-avaliacao">Você já avaliou todos os seus contratos com esta babá.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="perfil-card avaliacoes-lista" style="grid-column:1/-1">
                <h2>Comentários dos clientes</h2>
                <?php if ($comentarios): ?>
                    <div class="comentarios">
                        <?php foreach ($comentarios as $comentario): ?>
                            <article class="comentario-item">
                                <div class="comentario-topo">
                                    <strong><?= e(trim($comentario['nome'] . ' ' . $comentario['sobrenome'])) ?></strong>
                                    <span>⭐ <?= e((string)$comentario['nota']) ?></span>
                                </div>
                                <p><?= nl2br(e($comentario['comentario'])) ?></p>
                                <small><?= e(date('d/m/Y', strtotime($comentario['data_avaliacao']))) ?></small>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="sem-avaliacao">Ainda não há comentários para esta babá.</p>
                <?php endif; ?>
            </section>

            <?php if ($ehDono): ?>
                <section class="perfil-card" style="grid-column:1/-1">
                    <h2>Editar meu perfil</h2>
                    <form class="form-edicao" method="POST">
                        <label for="telefone">Telefone</label>
                        <input id="telefone" name="telefone" value="<?= e($baba['telefone']) ?>" required>
                        <label for="experiencia">Sobre mim / experiência</label>
                        <textarea id="experiencia" name="experiencia"><?= e($baba['experiencia']) ?></textarea>
                        <button class="btn" type="submit">Salvar alterações</button>
                    </form>
                </section>
            <?php endif; ?>
        </div>
    </main>
    <?php include __DIR__ . '/include/footer.php'; ?>
</body>

</html>