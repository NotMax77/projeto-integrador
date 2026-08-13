<?php
declare(strict_types=1);
require_once __DIR__ . '/include/auth.php';
exigirTipo('baba');

$id = (int)($_GET['id'] ?? 0);
$stmt = $conexao->prepare(
    "SELECT c.*, e.cep,e.estado,e.cidade,e.bairro,e.rua,e.numero,e.complemento
     FROM CLIENTE c INNER JOIN ENDERECO e ON e.id_endereco=c.id_endereco
     WHERE c.id_cliente=? LIMIT 1"
);
$stmt->bind_param('i',$id); $stmt->execute(); $cliente=$stmt->get_result()->fetch_assoc();
if(!$cliente){http_response_code(404);exit('Cliente não encontrado.');}
$foto=$cliente['foto'] ?: 'img/cliente.jpg';
$nomeCliente=trim($cliente['nome'].' '.$cliente['sobrenome']);
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Perfil de <?=e($nomeCliente)?> | Babá Amiga</title><link rel="stylesheet" href="CSS/navbar.css"><link rel="stylesheet" href="CSS/footer.css"><link rel="stylesheet" href="CSS/perfil.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></head><body>
<?php include __DIR__.'/include/navbar_publica.php';?>
<main class="perfil-wrap">
<section class="perfil-cabecalho"><img class="perfil-foto" src="<?=e($foto)?>" alt="Foto de <?=e($nomeCliente)?>"><div>
<span class="badge"><i class="fa-solid fa-user"></i> Cliente</span><h1><?=e($nomeCliente)?></h1>
<p><i class="fa-solid fa-location-dot"></i> <?=e($cliente['cidade'].' - '.$cliente['estado'])?></p>
<div class="acoes-perfil" style="margin-top:20px">
<a class="btn" href="conversa.php?id_cliente=<?= (int)$id ?>"><i class="fa-solid fa-comments"></i> Conversar</a>
</div>
</div></section>
<div class="perfil-grid">
<section class="perfil-card"><h2>Informações</h2><ul class="dados-list">
<li><strong>Telefone:</strong> <?=e($cliente['telefone'])?></li><li><strong>E-mail:</strong> <?=e($cliente['email'])?></li>
</ul></section>
<section class="perfil-card"><h2>Localização</h2><ul class="dados-list">
<li><?=e($cliente['bairro'])?></li><li><?=e($cliente['cidade'].' - '.$cliente['estado'])?></li></ul></section>
</div>
</main><?php include __DIR__.'/include/footer.php';?></body></html>
