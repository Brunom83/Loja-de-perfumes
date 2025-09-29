<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// Ao clicar em "Finalizar Compra"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    } else {
        header("Location: confirmar_checkout.php");
        exit;
    }
}

$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    echo "<div class='container mt-5 text-center'>
            <h3>🛒 O seu carrinho está vazio.</h3>
            <a href='index.php' class='btn btn-success mt-3'>Voltar à loja</a>
          </div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$ids = array_keys($carrinho);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<div class="container mt-5">
    <h2 class="mb-4">🛒 Carrinho</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="text-muted">🧑 Sessão de <strong><?= htmlspecialchars($nomeUtilizador ?? 'Utilizador') ?></strong></p>
        <?php else: ?>
            <p class="text-muted">🛒 Carrinho de visitante — <a href="login.php">iniciar sessão</a> para guardar a encomenda.</p>
        <?php endif; ?>


    <div id="alert" class="alert d-none"></div>

    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>Produto</th>
                <th>Imagem</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
                <th>Remover</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
                <?php
                    $id = $produto['id'];
                    $qtd = $carrinho[$id];

                    // Verifica promoção
                    $precoOriginal = $produto['preco'];
                    $precoFinal = $precoOriginal;

                    if (!empty($produto['em_promocao']) && !empty($produto['desconto']) && $produto['desconto'] > 0) {
                        $precoFinal = $precoOriginal * (1 - $produto['desconto'] / 100);
                    }

                    $subtotal = $precoFinal * $qtd;
                    $total += $subtotal;
                ?>
                <tr id="linha-<?= $id ?>">
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><img src="/assets/images/<?= htmlspecialchars($produto['imagem']) ?>" width="60" alt="<?= htmlspecialchars($produto['nome']) ?>"></td>
                    <td>
                        <?php if (!empty($produto['em_promocao']) && !empty($produto['desconto']) && $produto['desconto'] > 0): ?>
                            <span class="text-danger"><del><?= number_format($precoOriginal, 2) ?> €</del></span><br>
                            <span class="text-success fw-bold"><?= number_format($precoFinal, 2) ?> €</span>
                        <?php else: ?>
                            <span class="text-success fw-bold"><?= number_format($precoFinal, 2) ?> €</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $qtd ?></td>
                    <td><?= number_format($subtotal, 2) ?> €</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="removerItem(<?= $id ?>)">🗑 Remover</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <td colspan="4" class="text-end">Total:</td>
                <td colspan="2"><?= number_format($total, 2) ?> €</td>
            </tr>
        </tfoot>
    </table>

    <div class="d-flex justify-content-center gap-3 mt-5">

    <a href="loja.php" class="btn btn-outline-secondary btn-lg">
        ← Continuar a comprar
    </a>

    <form method="post">
        <button type="submit" name="finalizar_compra" class="btn btn-success btn-lg">
            Finalizar Compra
        </button>
    </form>
</div>

</div>

<script>
function removerItem(id) {
    if (!confirm('Tens a certeza que queres remover este produto?')) return;

    fetch(`remover_item.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('linha-' + id).remove();
                mostrarMensagem('success', data.message);

                if (document.querySelectorAll('tbody tr').length === 0) {
                    window.location.reload(); // se carrinho vazio, recarrega
                }
            } else {
                mostrarMensagem('danger', data.message);
            }
        });
}

function mostrarMensagem(tipo, texto) {
    const alert = document.getElementById('alert');
    alert.className = `alert alert-${tipo}`;
    alert.textContent = texto;
    alert.classList.remove('d-none');
    setTimeout(() => alert.classList.add('d-none'), 3000);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
