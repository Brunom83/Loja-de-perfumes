<?php require_once __DIR__ . '/../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sobre Nós - Loja de Perfumes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .team-photo {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 12px;
    }
    .section-title {
      margin-top: 3rem;
      margin-bottom: 1rem;
      font-weight: bold;
    }
    .card {
      border: none;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
  </style>
</head>
<body>

  <header class="bg-dark text-white py-4 text-center">
    <h1>Sobre Nós</h1>
    <p class="mb-0">Conhece a equipa por trás da nossa loja de perfumess</p>
  </header>

  <main class="container py-5">

    <section>
      <h2 class="section-title">O Nosso Objetivo</h2>
      <p>Oferecer perfumes de qualidade superior a preços acessíveis, para que todos possam expressar o seu aroma com elegância e confiança.</p>

      <h2 class="section-title">A Nossa Visão</h2>
      <p>Ser a principal referência online em perfumess de alta qualidade em Portugal e além, promovendo o requinte e a individualidade em cada detalhe.</p>

      <h2 class="section-title">Os Nossos Valores</h2>
      <ul>
        <li><strong>Qualidade:</strong> Selecionamos aromas e puros com o máximo rigor.</li>
        <li><strong>Transparência:</strong> Comunicação clara com os nossos clientes.</li>
        <li><strong>Inovação:</strong> Estilo moderno e atendimento digital eficiente.</li>
        <li><strong>Compromisso:</strong> Satisfação do cliente em primeiro lugar.</li>
      </ul>
    </section>

    <section>
      <h2 class="section-title">A Nossa Equipa</h2>
      <div class="row g-4">
        <!-- João Cunha -->
        <div class="col-md-4">
          <div class="card">
            <img src="https://via.placeholder.com/400x250?text=João+Conde" class="team-photo" alt="João Conde">
            <div class="card-body">
              <h5 class="card-title">João Conde</h5>
              <p class="card-text">Responsável pelo design e identidade visual da loja. João acredita que cada perfume deve contar uma história de elegância.</p>
            </div>
          </div>
        </div>

        <!-- Manuel Liendo -->
        <div class="col-md-4">
          <div class="card">
            <img src="https://via.placeholder.com/400x250?text=Manuel+Liendo" class="team-photo" alt="Manuel Liendo">
            <div class="card-body">
              <h5 class="card-title">Manuel Liendo</h5>
              <p class="card-text">Gestor de operações e atendimento. Manuel garante que cada encomenda seja tratada com rapidez, qualidade e atenção ao detalhe.</p>
            </div>
          </div>
        </div>

        <!-- Bruno Monteiro -->
        <div class="col-md-4">
          <div class="card">
            <img src="https://via.placeholder.com/400x250?text=Bruno+Monteiro" class="team-photo" alt="Bruno Monteiro">
            <div class="card-body">
              <h5 class="card-title">Bruno Monteiro</h5>
              <p class="card-text">Desenvolvedor da plataforma e responsável pela experiência digital. Bruno assegura que a loja online seja rápida, intuitiva e segura.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Perfumes Verdes. Todos os direitos reservados.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
