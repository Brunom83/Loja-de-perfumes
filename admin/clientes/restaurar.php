<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?erro=id_invalido");
    exit;
}

$id = (int)$_GET['id'];

// Verifica se o cliente está eliminado
$stmt = $pdo->prepare("SELECT eliminado FROM utilizadores WHERE id = ? AND tipo = 'cliente'");
$stmt->execute([$id]);

$cliente = $stmt->fetch();
if (!$cliente || !$cliente['eliminado']) {
    header("Location: index.php?erro=nao_encontrado");
    exit;
}

// Restaurar cliente
$stmt = $pdo->prepare("UPDATE utilizadores SET eliminado = 0 WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?restaurado=ok");
exit;
?>
