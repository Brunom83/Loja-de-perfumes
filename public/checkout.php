<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$carrinho = $_SESSION['carrinho'];

// Buscar produtos do carrinho com desconto e promoções
$ids = array_keys($carrinho);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT id, nome, preco, em_promocao, desconto FROM produtos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produtosBD = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$precosFinais = [];

foreach ($produtosBD as $produto) {
    $id = $produto['id'];
    $qtd = $carrinho[$id];
    $preco = $produto['preco'];

    if ($produto['em_promocao'] && $produto['desconto'] > 0) {
        $preco *= (1 - $produto['desconto'] / 100);
    }

    $precosFinais[$id] = $preco;
    $total += $preco * $qtd;
}

// Se método de pagamento foi escolhido
$metodo = $_POST['pagamento'] ?? 'paypal';

// Inserir encomenda
$stmt = $pdo->prepare("INSERT INTO encomendas (id_utilizador, total, id_pagamento, id_estado, data)
                       VALUES (?, ?, ?, ?, NOW())");

// Mapear métodos de pagamento para ID
$mapa_pagamentos = [
    'paypal' => 1,
    'mbway' => 2,
    'referencia' => 3,
    'cartao' => 4,
];

$id_pagamento = $mapa_pagamentos[$metodo] ?? 1;
$stmt->execute([$user_id, $total, $id_pagamento, 1]);
$id_encomenda = $pdo->lastInsertId();
$_SESSION['ultima_encomenda'] = $id_encomenda;

// Inserir produtos_encomenda + Atualizar stock + Notificar stock baixo
$stmt = $pdo->prepare("INSERT INTO produtos_encomenda (id_encomenda, id_produto, quantidade, preco_unitario)
                       VALUES (?, ?, ?, ?)");
$stockStmt = $pdo->prepare("UPDATE produtos SET stock = stock - ? WHERE id = ?");
$notifStmt = $pdo->prepare("INSERT INTO notificacoes (titulo, mensagem, tipo, lida, criada_em) VALUES (?, ?, ?, 0, NOW())");

foreach ($carrinho as $id => $qtd) {
    $preco = $precosFinais[$id];
    $stmt->execute([$id_encomenda, $id, $qtd, $preco]);
    $stockStmt->execute([$qtd, $id]);

    // Verifica stock atual
    $novoStock = $pdo->prepare("SELECT nome, stock FROM produtos WHERE id = ?");
    $novoStock->execute([$id]);
    $dados = $novoStock->fetch();

    if ($dados && $dados['stock'] <= 5) {
        $titulo = "Estoque Baixo";
        $mensagem = "O produto <strong> {$dados['nome']} </strong> está com apenas {$dados['stock']} unidades.";
        $notifStmt->execute([$titulo, $mensagem, 'stock_baixo']);
    }
}

// Limpar carrinho após checkout
unset($_SESSION['carrinho']);

// Redirecionamento baseado no método de pagamento
if ($metodo === 'paypal') {
    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $paypal_email = "sb-ve4dr39410659@business.example.com"; // Substituir pelo e-mail real
    ?>

    <form action="<?= $paypal_url ?>" method="post" id="paypal-form">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?= $paypal_email ?>">
        <input type="hidden" name="item_name" value="Encomenda #<?= $id_encomenda ?>">
        <input type="hidden" name="amount" value="<?= number_format($total, 2, '.', '') ?>">
        <input type="hidden" name="currency_code" value="EUR">
        <input type="hidden" name="return" value="http://localhost/perfumes_verde/public/obrigado.php">
        <input type="hidden" name="cancel_return" value="http://localhost/perfumes_verde/public/cancelado.php">
    </form>

    <script>
        document.getElementById('paypal-form').submit();
    </script>

    <?php
    exit;
} else {
    // Para métodos não implementados, simular página de confirmação
    header("Location: obrigado.php?metodo=$metodo");
    exit;
}
?>
