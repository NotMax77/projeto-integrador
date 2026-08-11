<?php
declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirTipo('baba');

$clientes = $conexao->query(
    "SELECT c.id_cliente,c.nome,c.sobrenome,c.foto,e.cidade,e.estado
     FROM CLIENTE c INNER JOIN ENDERECO e ON e.id_endereco=c.id_endereco
     ORDER BY c.nome"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Clientes | Babá Amiga</title>
<link rel="stylesheet" href="CSS/navbar.css"><link rel="stylesheet" href="CSS/footer.css"><link rel="stylesheet" href="CSS/clientes.css"><link rel="stylesheet" href="CSS/perfil.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></head>
<body>
<?php include __DIR__ . '/include/navbar_publica.php'; ?>
<main class="perfil-wrap">
<h1>Clientes cadastrados</h1>
<p>Consulte as famílias disponíveis para conhecer oportunidades de trabalho.</p>
<div class="perfil-grid">
<?php foreach($clientes as $cliente): ?>
<section class="perfil-card">
<img src="<?=e($cliente['foto'] ?: 'img/cliente.jpg')?>" alt="" style="width:90px;height:90px;border-radius:50%;object-fit:cover;float:left;margin:0 15px 10px 0">
<h2><?=e($cliente['nome'].' '.$cliente['sobrenome'])?></h2>
<p><i class="fa-solid fa-location-dot"></i> <?=e($cliente['cidade'].' - '.$cliente['estado'])?></p>
<div style="clear:both"></div>
<a class="btn secundario" href="perfil-cliente-publico.php?id=<?=(int)$cliente['id_cliente']?>">Ver perfil</a>
</section>
<?php endforeach; ?>
</div>
</main>
<?php include __DIR__ . '/include/footer.php'; ?>
</body></html>
