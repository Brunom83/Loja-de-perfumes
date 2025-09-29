<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$id_encomenda = $_GET['id'] ?? null;

if (!$id_encomenda) {
    echo "<div class='container mt-5 text-center text-danger'><p>❌ Encomenda não encontrada.</p></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Buscar dados da encomenda
$stmt = $pdo->prepare("SELECT total, data FROM encomendas WHERE id = ?");
$stmt->execute([$id_encomenda]);
$encomenda = $stmt->fetch();

$stmt = $pdo->prepare("SELECT p.nome, pe.quantidade, pe.preco_unitario
                       FROM produtos_encomenda pe
                       JOIN produtos p ON pe.id_produto = p.id
                       WHERE pe.id_encomenda = ?");
$stmt->execute([$id_encomenda]);
$produtos = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="text-center">
        <h2 class="text-success">✅ Pagamento concluído</h2>
        <p>Obrigado pela sua compra! A encomenda foi registada com sucesso.</p>
        <p class="text-muted">Encomenda #<?= $id_encomenda ?> • <?= date('d/m/Y H:i', strtotime($encomenda['data'])) ?></p>
    </div>

    <h4 class="mt-4">Resumo da Encomenda</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $p['quantidade'] ?></td>
                    <td><?= number_format($p['preco_unitario'], 2) ?> €</td>
                    <td><?= number_format($p['preco_unitario'] * $p['quantidade'], 2) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Total:</td>
                <td><?= number_format($encomenda['total'], 2) ?> €</td>
            </tr>
        </tfoot>
    </table>

    <div class="text-center mt-4">
        <a href="conta.php" class="btn btn-success">📦 Ver Minhas Encomendas</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
