<?php
require_once __DIR__ . '/include/auth.php';
exigirTipo('cliente');

$idCliente = (int)$_SESSION['usuario_id'];
$busca = trim((string)($_GET['busca'] ?? ''));

$sql = "SELECT p.id_pagamento, p.valor, p.data_pagamento, p.status,
               c.id_contrato, c.numero_contrato, c.data_geracao,
               pr.id_proposta, pr.data_inicio, pr.data_fim, pr.horario_inicio, pr.horario_fim,
               b.id_baba, b.nome AS baba_nome, b.sobrenome AS baba_sobrenome, b.foto AS baba_foto
        FROM PAGAMENTO p
        INNER JOIN CONTRATO c ON c.id_contrato = p.id_contrato
        INNER JOIN PROPOSTA pr ON pr.id_proposta = c.id_proposta
        INNER JOIN CONVERSA cv ON cv.id_conversa = pr.id_conversa
        INNER JOIN BABA b ON b.id_baba = cv.id_baba
        WHERE cv.id_cliente = ?";

$paramTypes = 'i';
$params = [$idCliente];
if ($busca !== '') {
    $sql .= " AND CONCAT(b.nome, ' ', b.sobrenome) LIKE ?";
    $paramTypes .= 's';
    $params[] = '%' . $busca . '%';
}
$sql .= ' ORDER BY p.status = \'pendente\' DESC, p.id_pagamento DESC';

$stmt = $conexao->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$pagamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="CSS/navbar.css"><link rel="stylesheet" href="CSS/footer.css"><link rel="stylesheet" href="CSS/dividas_pagamentos.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<title>Dívidas e Pagamentos | Babá Amiga</title>
</head>
<body>
<?php include __DIR__ . '/include/navbar_publica.php'; ?>
<section class="pagamentos">
    <h1>Dívidas e Pagamentos</h1>
    <p class="subtitulo">Aqui aparecem os pagamentos gerados após você confirmar uma proposta de atendimento.</p>

    <section class="container-pesquisa">
        <form class="form_pesquisa" method="GET">
            <input type="text" name="busca" value="<?= e($busca) ?>" placeholder="Pesquisar babá..."><button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section>

    <?php if (!$pagamentos): ?>
        <div class="sem-pagamentos"><i class="fa-regular fa-credit-card"></i><h3>Nenhum pagamento encontrado</h3><p>Quando você confirmar uma proposta de uma babá, a cobrança aparecerá aqui como pendente.</p></div>
    <?php endif; ?>

    <?php foreach ($pagamentos as $pagamento): ?>
        <?php
        $nomeBaba = trim($pagamento['baba_nome'] . ' ' . $pagamento['baba_sobrenome']);
        $fotoBaba = $pagamento['baba_foto'] ?: 'img/baba.jpg';
        $statusClass = $pagamento['status'] === 'pago' ? 'pago' : ($pagamento['status'] === 'atrasado' ? 'atrasado' : 'pendente');
        $statusTexto = ucfirst($pagamento['status']);
        ?>
        <div class="card-pagamento">
            <div class="foto"><img src="<?= e($fotoBaba) ?>" alt="Babá <?= e($nomeBaba) ?>"></div>
            <div class="coluna"><h4>Babá</h4><p><?= e($nomeBaba) ?></p><small>Contrato <?= e($pagamento['numero_contrato']) ?></small></div>
            <div class="coluna"><h4>Período</h4><p><?= date('d/m/Y', strtotime($pagamento['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($pagamento['data_fim'])) ?></p><p><?= e(substr($pagamento['horario_inicio'],0,5)) ?> às <?= e(substr($pagamento['horario_fim'],0,5)) ?></p></div>
            <div class="coluna"><h4>Valor</h4><p>R$ <?= number_format((float)$pagamento['valor'], 2, ',', '.') ?></p></div>
            <div class="coluna"><h4>Situação</h4><span class="status <?= $statusClass ?>"><?= e($statusTexto) ?></span>
                <?php if ($pagamento['status'] === 'pendente'): ?><button type="button" disabled title="Pagamento online ainda não implementado">Pagamento pendente</button><?php else: ?><button type="button">Ver recibo</button><?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/include/footer.php'; ?>
</body></html>
