<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/conexao.php';

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function usuarioLogado(): bool {
    return isset($_SESSION['usuario_tipo'], $_SESSION['usuario_id']);
}

function exigirLogin(): void {
    if (!usuarioLogado()) {
        header('Location: login.php');
        exit;
    }
}

function exigirTipo(string $tipo): void {
    exigirLogin();
    if ($_SESSION['usuario_tipo'] !== $tipo) {
        header('Location: ' . ($_SESSION['usuario_tipo'] === 'baba' ? 'dashboard.php' : 'babas.php'));
        exit;
    }
}

function dadosUsuarioAtual(mysqli $conexao): ?array {
    if (!usuarioLogado()) {
        return null;
    }

    $tabela = $_SESSION['usuario_tipo'] === 'baba' ? 'BABA' : 'CLIENTE';
    $idCampo = $_SESSION['usuario_tipo'] === 'baba' ? 'id_baba' : 'id_cliente';

    $sql = "SELECT u.*, e.cep, e.estado, e.cidade, e.bairro, e.rua, e.numero, e.complemento
            FROM {$tabela} u
            INNER JOIN ENDERECO e ON e.id_endereco = u.id_endereco
            WHERE u.{$idCampo} = ? LIMIT 1";

    $stmt = $conexao->prepare($sql);
    $id = (int) $_SESSION['usuario_id'];
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc() ?: null;
}
?>
