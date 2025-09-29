<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$erros = [];
$dados = [
    'nome' => '',
    'email' => '',
    'telefone' => '',
    'morada' => '',
    'nif' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($dados as $campo => &$valor) {
        $valor = trim($_POST[$campo] ?? '');
    }
    unset($valor);

    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    // Email já registado?
    $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
    $stmt->execute([$dados['email']]);
    if ($stmt->fetch()) {
        $erros[] = "Este email já está registado.";
    }

    // Validação do telefone
    if (!preg_match('/^\d{9}$/', $dados['telefone'])) {
        $erros[] = "O telefone deve conter 9 dígitos numéricos.";
    }

    // Validação do NIF
    if (!preg_match('/^[125689]\d{8}$/', $dados['nif'])) {
        $erros[] = "O NIF deve ter 9 dígitos e começar por 1, 2, 5, 6, 8 ou 9.";
    }

    // Validação da palavra-passe
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{9,}$/', $senha)) {
        $erros[] = "A palavra-passe deve ter pelo menos 9 caracteres, incluindo uma letra maiúscula e um número.";
    }

    if ($senha !== $confirmar) {
        $erros[] = "As palavras-passe não coincidem.";
    }

    // Tudo OK → Criar conta
    if (empty($erros)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $pdo->prepare("INSERT INTO utilizadores (nome, email, palavra_passe) VALUES (?, ?, ?)")
            ->execute([$dados['nome'], $dados['email'], $hash]);

        $id_utilizador = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO clientes_dados (id_utilizador, nome, telefone, morada, nif) VALUES (?, ?, ?, ?, ?)")
            ->execute([$id_utilizador, $dados['nome'], $dados['telefone'], $dados['morada'], $dados['nif']]);

        echo "<div class='alert alert-success container mt-5'>Conta criada com sucesso! <a href='login.php'>Iniciar sessão</a></div>";
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }
}
?>

<div class="container mt-5">
    <h2>📝 Registar</h2>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($erros as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-dark p-4 rounded text-light">
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($dados['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($dados['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Telefone:</label>
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($dados['telefone']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Morada:</label>
            <textarea name="morada" class="form-control" required><?= htmlspecialchars($dados['morada']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>NIF:</label>
            <input type="text" name="nif" class="form-control" value="<?= htmlspecialchars($dados['nif']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Palavra-passe:</label>
            <div class="position-relative">
                <input type="password" name="senha" id="senha" class="form-control" oninput="avaliarSenha()" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="cursor: pointer;" onclick="toggleSenha('senha')">👁️</span>
            </div>
            <div class="progress mt-2" style="height: 6px;">
                <div id="barraForca" class="progress-bar" role="progressbar"></div>
            </div>
            <small id="mensagemForca" class="form-text text-light"></small>
        </div>

        <div class="mb-3">
            <label>Confirmar Palavra-passe:</label>
            <div class="position-relative">
                <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="cursor: pointer;" onclick="toggleSenha('confirmar_senha')">👁️</span>
            </div>
        </div>

        <button class="btn btn-success">Criar Conta</button>
    </form>
</div>

<script>
function toggleSenha(id) {
    const campo = document.getElementById(id);
    campo.type = campo.type === 'password' ? 'text' : 'password';
}

function avaliarSenha() {
    const senha = document.getElementById("senha").value;
    const barra = document.getElementById("barraForca");
    const texto = document.getElementById("mensagemForca");

    let forca = 0;
    if (senha.length >= 9) forca++;
    if (/[A-Z]/.test(senha)) forca++;
    if (/\d/.test(senha)) forca++;

    if (forca === 0) {
        barra.style.width = "0%";
        barra.className = "progress-bar";
        texto.textContent = "";
    } else if (forca === 1) {
        barra.style.width = "33%";
        barra.className = "progress-bar bg-danger";
        texto.textContent = "Senha fraca";
    } else if (forca === 2) {
        barra.style.width = "66%";
        barra.className = "progress-bar bg-warning";
        texto.textContent = "Senha média";
    } else {
        barra.style.width = "100%";
        barra.className = "progress-bar bg-success";
        texto.textContent = "Senha forte";
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
