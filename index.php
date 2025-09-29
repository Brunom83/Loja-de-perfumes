<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

// Buscar produtos em promoção
$promocao = $pdo->query("SELECT * FROM produtos WHERE em_promocao = 1 AND eliminado = 0 ORDER BY criado_em DESC LIMIT 3")->fetchAll();

// Buscar novidades
$novidades = $pdo->query("SELECT * FROM produtos WHERE eliminado = 0 ORDER BY criado_em DESC LIMIT 3 OFFSET 3")->fetchAll();

// Buscar mais vendidos (simulado com random)
$mais_vendidos = $pdo->query("SELECT * FROM produtos WHERE eliminado = 0 ORDER BY RAND() LIMIT 3")->fetchAll();
?>

<div class="container mt-5">

    <!-- SLIDER -->
    <div id="mainSlider" class="carousel slide mb-5" data-bs-ride="carousel">
  <div class="carousel-inner rounded shadow">
    <div class="carousel-item active">
      <img src="/perfumes_verde/assets/images/banner_dior.jpg" class="d-block w-100 banner-img" alt="Slider 1">
    </div>
    <div class="carousel-item">
      <img src="/perfumes_verde/assets/images/banner_acqua.jpg" class="d-block w-100 banner-img" alt="Slider 2">
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>


    <!-- EM PROMOÇÃO -->
    <section class="mb-5">
        <h2>🎯 Em Promoção</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mt-3">
            <?php foreach ($promocao as $p): ?>
                <div class="col">
                    <div class="card h-100 text-center shadow-sm">
                        <img src="/perfumes_verde/assets/images/<?= htmlspecialchars($p['imagem']) ?>" class="card-img-top img-fluid" alt="<?= $p['nome'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['nome']) ?></h5>
                            
                            <?php if ($p['em_promocao'] && $p['desconto'] > 0): ?>
                                <p class="card-text text-danger mb-1">
                                <del><?= number_format($p['preco'], 2) ?> €</del><br>
                            <span class="fw-bold text-success">
                                <?= number_format($p['preco'] * (1 - $p['desconto'] / 100), 2) ?> €
                            </span>
                            </p>
                            <?php else: ?>
                                <p class="card-text text-success fw-bold"><?= number_format($p['preco'], 2) ?> €</p>
                            <?php endif; ?>

                            <a href="produto.php?id=<?= $p['id'] ?>" class="btn btn-outline-success">Ver Produto</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- NOVIDADES -->
    <section class="mb-5">
        <h2>🆕 Novidades</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mt-3">
            <?php foreach ($novidades as $p): ?>
                <div class="col">
                    <div class="card h-100 text-center shadow-sm">
                        <img src="/perfumes_verde/assets/images/<?= htmlspecialchars($p['imagem']) ?>" class="card-img-top img-fluid" alt="<?= $p['nome'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['nome']) ?></h5>
                            
                            <?php if ($p['em_promocao'] && $p['desconto'] > 0): ?>
                                <p class="card-text text-danger mb-1">
                                <del><?= number_format($p['preco'], 2) ?> €</del><br>
                            <span class="fw-bold text-success">
                                <?= number_format($p['preco'] * (1 - $p['desconto'] / 100), 2) ?> €
                            </span>
                            </p>
                        <?php else: ?>
                            <p class="card-text text-success fw-bold"><?= number_format($p['preco'], 2) ?> €</p>
                        <?php endif; ?>

                            <a href="produto.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary">Ver Produto</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- MAIS VENDIDOS -->
    <section class="mb-5">
        <h2>🔥 Mais Vendidos</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mt-3">
            <?php foreach ($mais_vendidos as $p): ?>
                <div class="col">
                    <div class="card h-100 text-center shadow-sm">
                        <img src="/perfumes_verde/assets/images/<?= htmlspecialchars($p['imagem']) ?>" class="card-img-top img-fluid" alt="<?= $p['nome'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['nome']) ?></h5>

                            <?php if ($p['em_promocao'] && $p['desconto'] > 0): ?>
                                <p class="card-text text-danger mb-1">
                                <del><?= number_format($p['preco'], 2) ?> €</del><br>
                            <span class="fw-bold text-success">
                                <?= number_format($p['preco'] * (1 - $p['desconto'] / 100), 2) ?> €
                            </span>
                            </p>
                        <?php else: ?>
                            <p class="card-text text-success fw-bold"><?= number_format($p['preco'], 2) ?> €</p>
                        <?php endif; ?>

                            <a href="produto.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark">Ver Produto</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- NEWSLETTER -->
    <section class="mt-5">
        <h2>📧 Subscreve a nossa newsletter</h2>
        <form action="subscrever_newsletter.php" method="post" class="d-flex flex-column flex-sm-row gap-2 mt-3">
            <input type="email" name="email" class="form-control" placeholder="O teu email" required>
            <button type="submit" class="btn btn-success">Subscrever</button>
        </form>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
