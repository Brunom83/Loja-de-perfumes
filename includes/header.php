<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

$nomeUtilizador = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT nome FROM utilizadores WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $res = $stmt->fetch();
    $nomeUtilizador = $res['nome'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfumes Verdes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/perfumes_verde/assets/css/style.css?v=3">
    <!-- Favicon -->
<link rel="icon" href="/perfumes_verde/assets/favicon.ico" type="image/x-icon">

<!-- Título dinâmico opcional -->
<title><?= $titulo ?? 'Perfumes Verdes' ?></title>
</head>

<body>

<script>
function toggleTheme() {
    const body = document.body;
    const current = localStorage.getItem('theme');
    const nextTheme = current === 'theme-light' ? 'theme-dark' : 'theme-light';
    body.classList.remove('theme-dark', 'theme-light');
    body.classList.add(nextTheme);
    localStorage.setItem('theme', nextTheme);
}

window.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('theme') || 'theme-dark';
    document.body.classList.add(saved);
});
</script>


<!-- Alerta visual para mensagens AJAX -->
<div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; width: auto; max-width: 400px;">
    <div id="alert-message" class="alert d-none text-center py-2 px-3" role="alert" style="font-size: 0.9rem;"></div>
</div>


<!-- Cabeçalho com logotipo, navegação e pesquisa -->
<header class="bg-dark py-2 border-bottom">
    <div class="container d-flex justify-content-between align-items-center">
        
        <!-- Logo -->
        <div class="logo fs-4 fw-bold text-success">
            <a href="/perfumes_verde/index.php" class="text-decoration-none text-success">🌿 Perfumes Verdes</a>
        </div>

        <!-- Pesquisa -->
        <form method="GET" action="../public/loja.php" class="d-flex flex-grow-1 mx-4">
            <input type="text" name="pesquisa" class="form-control pesquisa-topo" placeholder="Pesquisar por produto..." />
                <button type="submit" class="btn btn-outline-success ms-2">
                    <i class="bi bi-search"></i>
                </button>
        </form>

        <style>
.custom-search {
    background-color: #f8f9fa; /* mesmo tom que bg-light */
    border: 1px solid #ced4da;
    color: #333;
}
</style>

        <!-- Navegação -->
        <nav class="d-flex gap-3 align-items-center">

            <a href="/perfumes_verde/public/loja.php" class="text-light text-decoration-none">Loja</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/perfumes_verde/public/conta.php" class="text-light text-decoration-none">Conta</a>
                <a href="/perfumes_verde/public/logout.php" class="text-light text-decoration-none">Logout</a>
            <?php else: ?>
                <a href="/perfumes_verde/public/login.php" class="text-light text-decoration-none">Login</a>
            <?php endif; ?>

            <a href="/perfumes_verde/public/carrinho.php" class="text-light text-decoration-none">
                🛒 Carrinho (<span id="cart-count"><?= array_sum($_SESSION['carrinho'] ?? []) ?></span>)
            </a>
                <a href="/perfumes_verde/public/sobre.php" class="text-light text-decoration-none">Sobre Nós</a>
            <button onclick="toggleTheme()" class="btn btn-sm btn-outline-success ms-3">🌗 Tema</button>
        </nav>
    </div>
</header>
<main class="container mt-4">