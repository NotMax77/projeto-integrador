<?php
declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirTipo('baba');

$id = (int)$_SESSION['usuario_id'];
$stmt = $conexao->prepare("SELECT COUNT(*) total FROM FAVORITOS WHERE id_baba=?");
$stmt->bind_param('i',$id); $stmt->execute(); $favoritos=(int)$stmt->get_result()->fetch_assoc()['total'];
$stmt = $conexao->prepare("SELECT COUNT(*) total FROM CONVERSA WHERE id_baba=?");
$stmt->bind_param('i',$id); $stmt->execute(); $conversas=(int)$stmt->get_result()->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Painel da babá | Babá Amiga</title>
<link rel="stylesheet" href="CSS/navbar.css"><link rel="stylesheet" href="CSS/footer.css"><link rel="stylesheet" href="CSS/perfil.css"><link rel="stylesheet" href="CSS/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></head>
<body>
<?php include __DIR__ . '/include/navbar_publica.php'; ?>
<main class="perfil-wrap">
<section class="perfil-cabecalho">
<div><span class="badge">Área da babá</span><h1>Olá, <?=e($_SESSION['usuario_nome'])?>!</h1><p>Acesse seu perfil e acompanhe suas oportunidades.</p>
<div class="acoes-perfil"><a class="btn" href="perfil-baba.php">Meu perfil</a><a class="btn secundario" href="clientes.php">Ver clientes</a></div></div>
</section>
<div class="perfil-grid">
<section class="perfil-card"><h2>Conversas</h2><p style="font-size:36px;font-weight:700"><?=$conversas?></p><p>conversas iniciadas com clientes.</p></section>
<section class="perfil-card"><h2>Favoritos</h2><p style="font-size:36px;font-weight:700"><?=$favoritos?></p><p>clientes que adicionaram você aos favoritos.</p></section>
<section class="perfil-card" style="grid-column:1/-1"><h2>Próximos passos</h2><p>Mantenha seu perfil atualizado, informe sua disponibilidade e responda às mensagens dos clientes.</p></section>
</div></main>
<?php include __DIR__ . '/include/footer.php'; ?>
</body></html>
