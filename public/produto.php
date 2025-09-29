<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<p class='text-danger'>Produto não encontrado.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Buscar o produto
$stmt = $pdo->prepare("
    SELECT p.*, 
           m.nome AS marca_nome, 
           ca.ml AS capacidade_ml, 
           co.tipo AS concentracao_tipo,
           c.nome AS categoria_nome
    FROM produtos p
    LEFT JOIN marcas m ON p.id_marca = m.id
    LEFT JOIN capacidades ca ON p.id_capacidade = ca.id
    LEFT JOIN concentracoes co ON p.id_concentracao = co.id
    LEFT JOIN categorias c ON p.id_categoria = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    echo "<p class='text-danger'>Produto não encontrado.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Buscar produtos relacionados pela mesma categoria (excluindo o atual)
$rel_stmt = $pdo->prepare("
    SELECT id, nome, imagem, preco 
    FROM produtos 
    WHERE id_categoria = ? AND id != ? 
    ORDER BY RAND() 
    LIMIT 3
");
$rel_stmt->execute([$produto['id_categoria'], $produto['id']]);
$relacionados = $rel_stmt->fetchAll();
?>

<div class="container mt-5">
    <a href="loja.php" class="btn btn-outline-secondary mb-4">← Voltar à Loja</a>

    <div class="row">
        <!-- Imagem -->
        <div class="col-md-5">
            <img src="/perfumes_verde/assets/images/<?= htmlspecialchars($produto['imagem']) ?>
            " alt="<?= htmlspecialchars($produto['nome']) ?>" class="img-fluid rounded border">
        </div>

        <!-- Detalhes -->
        <div class="col-md-7 text-dark">
            <h2><?= htmlspecialchars($produto['nome']) ?></h2>
                <?php if ($produto['em_promocao'] && $produto['desconto'] > 0): ?>
                 <p class="fs-5 text-danger">
                    <del><?= number_format($produto['preco'], 2) ?> €</del>
                    <span class="text-success fw-bold">
                    <?= number_format($produto['preco'] * (1 - $produto['desconto'] / 100), 2) ?> €
                </span>
             </p>
        <?php else: ?>
            <p class="fs-4 fw-bold text-success"><?= number_format($produto['preco'], 2) ?> €</p>
        <?php endif; ?>


            <ul class="list-unstyled mb-3">
                <li><strong>Marca:</strong> <?= htmlspecialchars($produto['marca_nome'] ?? '---') ?></li>
                <li><strong>Capacidade:</strong> <?= htmlspecialchars($produto['capacidade_ml'] ?? '---') ?></li>
                <li><strong>Concentração:</strong> <?= htmlspecialchars($produto['concentracao_tipo'] ?? '---') ?></li>
                <li><strong>Categoria:</strong> <?= htmlspecialchars($produto['categoria_nome'] ?? '---') ?></li>
                <li><strong>Stock:</strong> <?= (int) $produto['stock'] ?></li>
            </ul>

            <button class="btn btn-outline-success add-to-cart" data-id="<?= $produto['id'] ?>">Adicionar ao Carrinho</button>
        </div>
    </div>

    <!-- Descrição -->
    <div class="mt-5">
        <h4 class="text-dark">Descrição</h4>
        <p class="text-secondary"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
    </div>

    <!-- Relacionados -->
    <?php if ($relacionados): ?>
    <div class="mt-5">
        <h4 class="text-dark mb-3">Produtos Relacionados</h4>
        <div class="row">
            <?php foreach ($relacionados as $rel): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="/assets/images/<?= htmlspecialchars($rel['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($rel['nome']) ?>">
                        <div class="card-body">
                            <h6><a href="produto.php?id=<?= $rel['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($rel['nome']) ?></a></h6>
                            <p class="text-success fw-bold"><?= number_format($rel['preco'], 2) ?> €</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.querySelector('.add-to-cart').addEventListener('click', function () {
    const id = this.dataset.id;

    fetch('add_to_cart.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').textContent = data.count;
                showAlert(data.message, 'success');
            }
        });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
