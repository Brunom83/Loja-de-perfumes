<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare("UPDATE produtos SET eliminado = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: removidos.php");
exit;
