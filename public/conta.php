<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-5'>
            <p>Precisas de iniciar sessão para ver a tua conta. 
            <a href='login.php' class='text-success'>Login</a></p>
          </div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$id = $_SESSION['user_id'];

// Buscar encomendas do utilizador
$stmt = $pdo->prepare("
    SELECT e.id, e.data, e.total, e.id_estado, s.estado AS nome_estado
    FROM encomendas e
    LEFT JOIN estados_encomenda s ON e.id_estado = s.id
    WHERE e.id_utilizador = ?
    ORDER BY e.data DESC
");
$stmt->execute([$id]);
$encomendas = $stmt->fetchAll();

// Cancelar encomenda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_id'], $_POST['motivo'])) {
    $cancelar_id = (int) $_POST['cancelar_id'];
    $motivo = trim($_POST['motivo']);

    if ($motivo !== '') {
        $stmt = $pdo->prepare("UPDATE encomendas SET id_estado = 6, motivo_cancelamento = ? WHERE id = ? AND id_utilizador = ? AND id_estado = 1");
        $stmt->execute([$motivo, $cancelar_id, $id]);
        echo "<div class='alert alert-warning text-center'>❌ Encomenda #$cancelar_id cancelada.</div>";
    }
}
?>

<div class="container mt-5">
    <h2 class="mb-4">👤 Minha Conta</h2>

    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'): ?>
        <div class="mb-4">
            <a href="../admin/dashboard.php" class="btn btn-outline-warning">🔧 Ir para o Painel de Administração</a>
        </div>
    <?php endif; ?>

    <h4 class="mb-3">Minhas Encomendas</h4>
    <?php if (empty($encomendas)): ?>
        <p class="text-muted">Ainda não fez nenhuma encomenda.</p>
    <?php else: ?>
        <?php foreach ($encomendas as $e): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong>🧾 Encomenda #<?= $e['id'] ?></strong><br>
                        <?= date('d/m/Y H:i', strtotime($e['data'])) ?><br>
                        Total: <strong><?= number_format($e['total'], 2) ?> €</strong><br>
                        Estado: <span class="badge bg-<?= $e['id_estado'] == 6 ? 'danger' : ($e['id_estado'] == 2 ? 'success' : 'primary') ?>">
                            <?= htmlspecialchars($e['nome_estado'] ?? 'Desconhecido') ?>
                        </span>
                    </div>
                    <div class="text-end">
                        <a href="fatura.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-secondary mb-2">📄 Ver Fatura</a><br>

                        <?php if ($e['id_estado'] == 1): // Só permitir cancelar se pendente ?>
                            <form method="POST" class="d-flex align-items-center" style="gap: 6px;">
                                <input type="hidden" name="cancelar_id" value="<?= $e['id'] ?>">
                                <input type="text" name="motivo" placeholder="Motivo" required class="form-control form-control-sm">
                                <button type="submit" class="btn btn-sm btn-danger">❌ Cancelar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
