<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email inválido.'); window.location.href = 'index.php';</script>";
        exit;
    }

    // Verificar se já está registado
    $stmt = $pdo->prepare("SELECT id FROM newsletter WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "<script>alert('Este email já está subscrito.'); window.location.href = 'index.php';</script>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO newsletter (email, inscrito_em) VALUES (?, NOW())");
        $stmt->execute([$email]);
        echo "<script>alert('Subscrição com sucesso!'); window.location.href = 'index.php';</script>";
    }
}
?>
