<?php
require_once __DIR__ . '/include/auth.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'logado' => usuarioLogado(),
    'tipo' => $_SESSION['usuario_tipo'] ?? null,
    'id' => $_SESSION['usuario_id'] ?? null,
    'nome' => $_SESSION['usuario_nome'] ?? null,
    'foto' => $_SESSION['usuario_foto'] ?? null
], JSON_UNESCAPED_UNICODE);
