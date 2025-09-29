<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Filtros GET
$pesquisa = $_GET['pesquisa'] ?? '';
$marca = $_GET['marca'] ?? '';
$capacidade = $_GET['capacidade'] ?? '';
$concentracao = $_GET['concentracao'] ?? '';

// SQL com JOINs para trazer nomes legíveis
$sql = "
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
WHERE 1
";
$params = [];

// Filtros dinâmicos
if ($pesquisa) {
    $sql .= " AND p.nome LIKE ?";
    $params[] = "%$pesquisa%";
}
if ($marca) {
    $sql .= " AND m.nome = ?";
    $params[] = $marca;
}
if ($capacidade) {
    $sql .= " AND ca.ml = ?";
    $params[] = $capacidade;
}
if ($concentracao) {
    $sql .= " AND co.tipo = ?";
    $params[] = $concentracao;
}

$sql .= " ORDER BY p.criado_em DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>Loja</h2>

    <div class="row mt-4">
        <!-- Filtros -->
        <div class="col-md-3 filtros mb-4">
            <form method="GET">
                <!-- Marca -->
                <label for="marca">Marca:</label>
                <select name="marca" id="marca" class="form-select mb-2">
                    <option value="">Todas</option>
                    <?php
                    $marcas = $pdo->query("SELECT nome FROM marcas ORDER BY nome")->fetchAll();
                    foreach ($marcas as $m) {
                        $sel = $marca === $m['nome'] ? 'selected' : '';
                        echo "<option value='{$m['nome']}' $sel>{$m['nome']}</option>";
                    }
                    ?>
                </select>

                <!-- Capacidade -->
                <label for="capacidade">Capacidade:</label>
                <select name="capacidade" id="capacidade" class="form-select mb-2">
                    <option value="">Todas</option>
                    <?php
                    $caps = $pdo->query("SELECT ml FROM capacidades ORDER BY ml")->fetchAll();
                    foreach ($caps as $c) {
                        $sel = $capacidade === $c['ml'] ? 'selected' : '';
                        echo "<option value='{$c['ml']}' $sel>{$c['ml']}</option>";
                    }
                    ?>
                </select>

                <!-- Concentração -->
                <label for="concentracao">Concentração:</label>
                <select name="concentracao" id="concentracao" class="form-select mb-3">
                    <option value="">Todas</option>
                    <?php
                    $conc = $pdo->query("SELECT tipo FROM concentracoes ORDER BY tipo")->fetchAll();
                    foreach ($conc as $c) {
                        $sel = $concentracao === $c['tipo'] ? 'selected' : '';
                        echo "<option value='{$c['tipo']}' $sel>{$c['tipo']}</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn btn-success w-100">Filtrar</button>
            </form>
        </div>

        <!-- Produtos -->
        <div class="col-md-9">
            <div class="row">
                <?php if (count($produtos) > 0): ?>
                    <?php foreach ($produtos as $p): ?>
                        <div class="col-md-4 mb-4">
                            <div class="produto card p-3 h-100">
                                <a href="produto.php?id=<?= $p['id'] ?>" class="text-decoration-none">
                                <img src="/assets/images/<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>" class="img-fluid mb-2">
                                    <h5><?= htmlspecialchars($p['nome']) ?></h5>
                                </a>
                                <?php if ($p['em_promocao'] && $p['desconto'] > 0): ?>
                                    <p class="text-danger mb-1">
                                    <del><?= number_format($p['preco'], 2) ?> €</del><br>
                                <span class="fw-bold text-success">
                                    <?= number_format($p['preco'] * (1 - $p['desconto'] / 100), 2) ?> €
                                </span>
                                </p>
                            <?php else: ?>
                                <p class="text-success fw-bold"><?= number_format($p['preco'], 2) ?> €</p>
                            <?php endif; ?>
                                <small>
                                    <?= htmlspecialchars($p['capacidade_ml'] ?? '---') ?><br>
                                    <?= htmlspecialchars($p['concentracao_tipo'] ?? '---') ?>
                                </small>
                                <button type="button" class="btn btn-outline-success add-to-cart mt-2 w-100" data-id="<?= $p['id'] ?>">Adicionar ao Carrinho</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Nenhum produto encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;
        this.classList.add('clicked');
        setTimeout(() => this.classList.remove('clicked'), 400);

        fetch('add_to_cart.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-count').textContent = data.count;
                    showAlert(data.message);
                }
            });
    });
});

function showAlert(message, type = 'success') {
    const alert = document.getElementById('alert-message');
    alert.textContent = message;
    alert.className = `alert alert-${type} mt-3`;
    alert.classList.remove('d-none');
    setTimeout(() => alert.classList.add('d-none'), 3000);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
