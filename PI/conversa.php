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

if ($tipoAtual === 'cliente') {
    if ($idBaba <= 0) erroChat('É necessário informar a babá da conversa.');
    $idCliente = $idAtual;
} else {
    if ($idCliente <= 0) erroChat('É necessário informar o cliente da conversa.');
    $idBaba = $idAtual;
}

if ($tipoAtual === 'cliente') {
    $stmt = $conexao->prepare('SELECT id_baba, nome, sobrenome, foto FROM BABA WHERE id_baba = ? LIMIT 1');
    $stmt->bind_param('i', $idBaba);
    $stmt->execute();
    $outro = $stmt->get_result()->fetch_assoc();
    $tipoOutro = 'baba';
} else {
    $stmt = $conexao->prepare('SELECT id_cliente, nome, sobrenome, foto FROM CLIENTE WHERE id_cliente = ? LIMIT 1');
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();
    $outro = $stmt->get_result()->fetch_assoc();
    $tipoOutro = 'cliente';
}
if (!$outro) erroChat('A outra pessoa da conversa não foi encontrada.', 404);

$stmt = $conexao->prepare('SELECT id_conversa FROM CONVERSA WHERE id_cliente = ? AND id_baba = ? LIMIT 1');
$stmt->bind_param('ii', $idCliente, $idBaba);
$stmt->execute();
$rowConversa = $stmt->get_result()->fetch_assoc();

if ($rowConversa) {
    $idConversa = (int) $rowConversa['id_conversa'];
} else {
    $stmt = $conexao->prepare('INSERT INTO CONVERSA (id_cliente, id_baba) VALUES (?, ?)');
    $stmt->bind_param('ii', $idCliente, $idBaba);
    if (!$stmt->execute()) {
        if ((int) $conexao->errno === 1062) {
            $stmt2 = $conexao->prepare('SELECT id_conversa FROM CONVERSA WHERE id_cliente = ? AND id_baba = ? LIMIT 1');
            $stmt2->bind_param('ii', $idCliente, $idBaba);
            $stmt2->execute();
            $rowConversa = $stmt2->get_result()->fetch_assoc();
            if (!$rowConversa) erroChat('Não foi possível criar a conversa: ' . $stmt->error, 500);
            $idConversa = (int) $rowConversa['id_conversa'];
        } else {
            erroChat('Não foi possível criar a conversa: ' . $stmt->error, 500);
        }
    } else {
        $idConversa = (int) $conexao->insert_id;
    }
}

$urlAtualizacao = 'conversa.php?' . ($tipoAtual === 'cliente' ? 'id_baba=' . $idBaba : 'id_cliente=' . $idCliente);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'mensagem';

    if ($acao === 'mensagem') {
        $texto = trim((string)($_POST['mensagem'] ?? ''));
        if ($texto !== '') {
            $remetente = $tipoAtual === 'cliente' ? 'cliente' : 'baba';
            $stmt = $conexao->prepare('INSERT INTO MENSAGEM (id_conversa, remetente, mensagem, data_hora) VALUES (?, ?, ?, NOW())');
            if (!$stmt) erroChat('Erro ao preparar o envio: ' . $conexao->error, 500);
            $stmt->bind_param('iss', $idConversa, $remetente, $texto);
            if (!$stmt->execute()) erroChat('Erro ao salvar a mensagem: ' . $stmt->error, 500);
        }
        header('Location: ' . $urlAtualizacao);
        exit;
    }

    if ($acao === 'enviar_proposta') {
        if ($tipoAtual !== 'baba') erroChat('Somente a babá pode enviar uma proposta.');

        $quantidade = max(1, (int)($_POST['quantidade_criancas'] ?? 0));
        $idades = trim((string)($_POST['idades_criancas'] ?? ''));
        $dataInicio = trim((string)($_POST['data_inicio'] ?? ''));
        $dataFim = trim((string)($_POST['data_fim'] ?? ''));
        $horaInicio = trim((string)($_POST['horario_inicio'] ?? ''));
        $horaFim = trim((string)($_POST['horario_fim'] ?? ''));
        $valor = (float) str_replace(',', '.', (string)($_POST['valor'] ?? '0'));
        $observacoes = trim((string)($_POST['observacoes'] ?? ''));

        if ($dataInicio === '' || $dataFim === '' || $horaInicio === '' || $horaFim === '' || $valor <= 0) {
            erroChat('Preencha datas, horários e um valor válido para enviar a proposta.');
        }
        if ($dataFim < $dataInicio) erroChat('A data final não pode ser anterior à data inicial.');
        if ($idades === '') $idades = 'Não informado';

        // Apenas uma proposta pendente por conversa.
        $stmt = $conexao->prepare("SELECT id_proposta FROM PROPOSTA WHERE id_conversa = ? AND status = 'pendente' LIMIT 1");
        $stmt->bind_param('i', $idConversa);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) erroChat('Já existe uma proposta pendente para esta conversa. Aguarde a resposta do cliente.');

        $observacoesComDados = "Crianças: {$quantidade}\nIdades: {$idades}" . ($observacoes !== '' ? "\nObservações: {$observacoes}" : '');
        $stmt = $conexao->prepare("INSERT INTO PROPOSTA (id_conversa, data_inicio, data_fim, horario_inicio, horario_fim, valor, observacoes, status, quantidade_criancas, idades_criancas) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendente', ?, ?)");
        if (!$stmt) erroChat('Erro ao preparar a proposta: ' . $conexao->error, 500);
        $stmt->bind_param('issssdsis', $idConversa, $dataInicio, $dataFim, $horaInicio, $horaFim, $valor, $observacoesComDados, $quantidade, $idades);
        if (!$stmt->execute()) erroChat('Erro ao salvar a proposta: ' . $stmt->error, 500);

        $textoProposta = sprintf('Proposta enviada: %d criança(s), valor de R$ %.2f, de %s a %s.', $quantidade, $valor, date('d/m/Y', strtotime($dataInicio)), date('d/m/Y', strtotime($dataFim)));
        $remetente = 'baba';
        $stmt = $conexao->prepare('INSERT INTO MENSAGEM (id_conversa, remetente, mensagem, data_hora) VALUES (?, ?, ?, NOW())');
        $stmt->bind_param('iss', $idConversa, $remetente, $textoProposta);
        $stmt->execute();

        header('Location: ' . $urlAtualizacao);
        exit;
    }

    if ($acao === 'responder_proposta') {
        if ($tipoAtual !== 'cliente') erroChat('Somente o cliente pode responder a proposta.');
        $idProposta = (int)($_POST['id_proposta'] ?? 0);
        $resposta = $_POST['resposta'] ?? '';
        if ($idProposta <= 0 || !in_array($resposta, ['aceita', 'recusada'], true)) erroChat('Resposta de proposta inválida.');

        $stmt = $conexao->prepare("SELECT * FROM PROPOSTA WHERE id_proposta = ? AND id_conversa = ? AND status = 'pendente' LIMIT 1");
        $stmt->bind_param('ii', $idProposta, $idConversa);
        $stmt->execute();
        $proposta = $stmt->get_result()->fetch_assoc();
        if (!$proposta) erroChat('Esta proposta não está mais pendente.');

        $conexao->begin_transaction();
        try {
            $stmt = $conexao->prepare('UPDATE PROPOSTA SET status = ? WHERE id_proposta = ?');
            $stmt->bind_param('si', $resposta, $idProposta);
            if (!$stmt->execute()) throw new RuntimeException($stmt->error);

            if ($resposta === 'aceita') {
                $numeroContrato = 'BA-' . date('YmdHis') . '-' . $idProposta;
                $stmt = $conexao->prepare('INSERT INTO CONTRATO (id_proposta, numero_contrato, data_geracao) VALUES (?, ?, NOW())');
                $stmt->bind_param('is', $idProposta, $numeroContrato);
                if (!$stmt->execute()) throw new RuntimeException($stmt->error);
                $idContrato = (int)$conexao->insert_id;

                $stmt = $conexao->prepare("INSERT INTO PAGAMENTO (id_contrato, valor, data_pagamento, status) VALUES (?, ?, NULL, 'pendente')");
                $valorProposta = (float)$proposta['valor'];
                $stmt->bind_param('id', $idContrato, $valorProposta);
                if (!$stmt->execute()) throw new RuntimeException($stmt->error);

                $texto = 'Proposta aceita pelo cliente. Um pagamento pendente foi criado em Dívidas e Pagamentos.';
            } else {
                $texto = 'Proposta recusada pelo cliente.';
            }

            $remetente = 'cliente';
            $stmt = $conexao->prepare('INSERT INTO MENSAGEM (id_conversa, remetente, mensagem, data_hora) VALUES (?, ?, ?, NOW())');
            $stmt->bind_param('iss', $idConversa, $remetente, $texto);
            if (!$stmt->execute()) throw new RuntimeException($stmt->error);

            $conexao->commit();
        } catch (Throwable $e) {
            $conexao->rollback();
            erroChat('Não foi possível responder à proposta: ' . $e->getMessage(), 500);
        }

        header('Location: ' . $urlAtualizacao);
        exit;
    }
}

$stmt = $conexao->prepare('SELECT id_mensagem, remetente, mensagem, data_hora FROM MENSAGEM WHERE id_conversa = ? ORDER BY id_mensagem ASC');
$stmt->bind_param('i', $idConversa);
$stmt->execute();
$mensagens = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conexao->prepare('SELECT * FROM PROPOSTA WHERE id_conversa = ? ORDER BY id_proposta DESC');
$stmt->bind_param('i', $idConversa);
$stmt->execute();
$propostas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$propostaPendente = null;
foreach ($propostas as $p) {
    if ($p['status'] === 'pendente') { $propostaPendente = $p; break; }
}

$nomeOutro = trim($outro['nome'] . ' ' . $outro['sobrenome']);
$fotoOutro = $outro['foto'] ?: ($tipoOutro === 'baba' ? 'img/baba.jpg' : 'img/cliente.jpg');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Conversa com <?= e($nomeOutro) ?> | Babá Amiga</title>
<link rel="stylesheet" href="CSS/navbar.css"><link rel="stylesheet" href="CSS/footer.css"><link rel="stylesheet" href="CSS/conversa.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/include/navbar_publica.php'; ?>
<main class="chat <?= $tipoAtual === 'baba' ? 'chat-com-proposta' : '' ?>">
    <aside class="perfil">
        <img src="<?= e($fotoOutro) ?>" alt="Foto de <?= e($nomeOutro) ?>">
        <h2><?= e($nomeOutro) ?></h2><p class="tipo-pessoa"><?= $tipoOutro === 'baba' ? 'Babá' : 'Cliente' ?></p>
        <a class="btn-voltar" href="<?= $tipoAtual === 'cliente' ? 'perfil-baba.php?id=' . $idBaba : 'perfil-cliente-publico.php?id=' . $idCliente ?>"><i class="fa-solid fa-arrow-left"></i> Voltar ao perfil</a>
    </aside>

    <section class="conversa">
        <header class="topo-chat"><div><h3><?= e($nomeOutro) ?></h3><span>Conversa privada</span></div></header>
        <div class="mensagens" id="mensagens">
            <?php if (!$mensagens): ?><div class="sem-mensagens">Nenhuma mensagem ainda. Envie a primeira!</div><?php endif; ?>
            <?php foreach ($mensagens as $msg): ?>
                <div class="msg <?= $msg['remetente'] === $tipoAtual ? 'minha' : 'outra' ?>"><div><?= nl2br(e($msg['mensagem'])) ?></div><time><?= e(date('d/m/Y H:i', strtotime($msg['data_hora']))) ?></time></div>
            <?php endforeach; ?>
            <?php if ($tipoAtual === 'cliente' && $propostaPendente): ?>
                <div class="proposta-card proposta-recebida">
                    <div class="proposta-titulo"><i class="fa-solid fa-file-signature"></i> Proposta da babá</div>
                    <p><strong><?= (int)$propostaPendente['quantidade_criancas'] ?> criança(s)</strong> — idades: <?= e($propostaPendente['idades_criancas']) ?></p>
                    <p><?= date('d/m/Y', strtotime($propostaPendente['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($propostaPendente['data_fim'])) ?></p>
                    <p><?= e(substr($propostaPendente['horario_inicio'],0,5)) ?> às <?= e(substr($propostaPendente['horario_fim'],0,5)) ?></p>
                    <p class="valor-proposta">R$ <?= number_format((float)$propostaPendente['valor'], 2, ',', '.') ?></p>
                    <?php if (!empty($propostaPendente['observacoes'])): ?><p class="observacoes-proposta"><?= nl2br(e($propostaPendente['observacoes'])) ?></p><?php endif; ?>
                    <div class="acoes-proposta">
                        <form method="POST"><input type="hidden" name="acao" value="responder_proposta"><input type="hidden" name="id_proposta" value="<?= (int)$propostaPendente['id_proposta'] ?>"><input type="hidden" name="resposta" value="aceita"><button class="btn-aceitar" type="submit">Confirmar informações</button></form>
                        <form method="POST"><input type="hidden" name="acao" value="responder_proposta"><input type="hidden" name="id_proposta" value="<?= (int)$propostaPendente['id_proposta'] ?>"><input type="hidden" name="resposta" value="recusada"><button class="btn-recusar" type="submit">Recusar</button></form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <form class="enviar" method="POST" action="<?= e($urlAtualizacao) ?>"><input type="hidden" name="acao" value="mensagem"><input type="text" name="mensagem" maxlength="2000" placeholder="Digite sua mensagem..." autocomplete="off" required><button type="submit" aria-label="Enviar mensagem"><i class="fa-solid fa-paper-plane"></i></button></form>
    </section>

    <?php if ($tipoAtual === 'baba'): ?>
    <aside class="painel-proposta">
        <div class="painel-proposta-header"><i class="fa-solid fa-clipboard-list"></i><div><h3>Resumo do atendimento</h3><span>Preencha com o que o cliente informar</span></div></div>
        <form method="POST" class="form-proposta">
            <input type="hidden" name="acao" value="enviar_proposta">
            <label>Quantidade de crianças
                <input type="number" name="quantidade_criancas" min="1" max="20" value="1" required>
            </label>
            <label>Idades das crianças
                <input type="text" name="idades_criancas" placeholder="Ex.: 2 e 6 anos" required>
            </label>
            <div class="duas-colunas"><label>Data inicial<input type="date" name="data_inicio" required></label><label>Data final<input type="date" name="data_fim" required></label></div>
            <div class="duas-colunas"><label>Início<input type="time" name="horario_inicio" required></label><label>Fim<input type="time" name="horario_fim" required></label></div>
            <label>Valor a cobrar (R$)<input type="number" name="valor" min="0.01" step="0.01" placeholder="Ex.: 300,00" required></label>
            <label>Observações<textarea name="observacoes" rows="4" placeholder="Outras informações combinadas..." maxlength="1000"></textarea></label>
            <?php if ($propostaPendente): ?><div class="aviso-proposta">Já existe uma proposta pendente. Aguarde o cliente responder.</div><?php endif; ?>
            <button class="btn-enviar-proposta" type="submit" <?= $propostaPendente ? 'disabled' : '' ?>><i class="fa-solid fa-paper-plane"></i> Enviar proposta ao cliente</button>
        </form>
        <?php if ($propostas): ?><div class="historico-propostas"><h4>Propostas anteriores</h4><?php foreach ($propostas as $p): ?><div class="mini-proposta"><strong>R$ <?= number_format((float)$p['valor'],2,',','.') ?></strong><span><?= ucfirst($p['status']) ?></span></div><?php endforeach; ?></div><?php endif; ?>
    </aside>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/include/footer.php'; ?>
<script>
const caixa = document.getElementById('mensagens');
if (caixa) caixa.scrollTop = caixa.scrollHeight;
setInterval(() => {
    fetch(<?= json_encode($urlAtualizacao . '&ajax=1') ?>, {cache:'no-store'})
      .then(r=>r.text()).then(html=>{const doc=new DOMParser().parseFromString(html,'text/html');const novas=doc.getElementById('mensagens');if(novas&&caixa&&novas.innerHTML!==caixa.innerHTML){const fim=caixa.scrollHeight-caixa.scrollTop-caixa.clientHeight<80;caixa.innerHTML=novas.innerHTML;if(fim)caixa.scrollTop=caixa.scrollHeight;}}).catch(()=>{});
},1500);
</script>
</body></html>
