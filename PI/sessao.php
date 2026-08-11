<?php
require_once __DIR__ . '/include/auth.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'logado' => usuarioLogado(),
    'tipo' => $_SESSION['tipo_usuario'] ?? null,
    'id' => $_SESSION['usuario_id'] ?? null,
    'nome' => $_SESSION['nome_usuario'] ?? null,
    'foto' => $_SESSION['foto_usuario'] ?? null
], JSON_UNESCAPED_UNICODE);
