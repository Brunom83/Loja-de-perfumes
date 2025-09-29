<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Atualizar estado da última encomenda se estiver guardado
if (isset($_SESSION['ultima_encomenda'])) {
    $stmt = $pdo->prepare("UPDATE encomendas SET id_estado = 2 WHERE id = ?");
    $stmt->execute([$_SESSION['ultima_encomenda']]);
    unset($_SESSION['ultima_encomenda']);
}
?>

<div class="container mt-5 text-center">
    <h2 class="text-success">✅ Pagamento concluído</h2>
    <p>Obrigado pela sua compra. A encomenda foi registada com sucesso.</p>
    <a href="conta.php" class="btn btn-success mt-3">Ver encomendas</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
