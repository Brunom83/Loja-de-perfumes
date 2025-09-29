<?php
session_start();
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if ($id && isset($_SESSION['carrinho'][$id])) {
    unset($_SESSION['carrinho'][$id]);
    
    echo json_encode([
        'success' => true,
        'count' => array_sum($_SESSION['carrinho']),
        'message' => 'Removido com sucesso'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Produto não encontrado no carrinho.'
    ]);
}
