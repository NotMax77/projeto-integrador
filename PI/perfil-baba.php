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
 
$foto = $baba['foto'] ?: 'img/baba.jpg';
$nomeCompleto = trim($baba['nome'].' '.$baba['sobrenome']);
$media = $avaliacao['media'] ?? null;
$totalAvaliacoes = (int)($avaliacao['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil de <?= e($nomeCompleto) ?> | Babá Amiga</title>
<link rel="stylesheet" href="CSS/navbar.css"><link rel="stylesheet" href="CSS/footer.css"><link rel="stylesheet" href="CSS/perfil.css">
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
            <p><i class="fa-solid fa-location-dot"></i> <?= e($baba['cidade'].' - '.$baba['estado']) ?></p>
            <p><?= $media !== null ? '⭐ '.e((string)$media).' ('.$totalAvaliacoes.' avaliações)' : 'Ainda sem avaliações' ?></p>
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
                <li><strong>Localização:</strong> <?= e($baba['bairro'].', '.$baba['cidade'].'/'.$baba['estado']) ?></li>
            </ul>
        </section>
 
        <section class="perfil-card">
            <h2>Disponibilidade</h2>
            <ul class="dados-list">
            <?php if ($disponibilidades): foreach ($disponibilidades as $item): ?>
                <li><?= e(ucfirst(str_replace('manha','manhã',$item['horario']))) ?></li>
            <?php endforeach; else: ?>
                <li>Não informada</li>
            <?php endif; ?>
            </ul>
        </section>
 
        <section class="perfil-card">
            <h2>Preferências</h2>
            <ul class="dados-list">
            <?php if ($preferencias): foreach ($preferencias as $item): ?>
                <li><?= e($item['descricao']) ?></li>
            <?php endforeach; else: ?>
                <li>Não informadas</li>
            <?php endif; ?>
            </ul>
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
 
 