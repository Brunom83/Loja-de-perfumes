<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $morada = trim($_POST['morada']);
    $nif = trim($_POST['nif']);
    $senha = $_POST['senha'];

    // Verifica se o email já está registado
    $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $erros[] = "Este email já está registado.";
    }

    // Verificação da força da palavra-passe
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{6,}$/', $senha)) {
        $erros[] = "A palavra-passe deve ter pelo menos 6 caracteres, uma letra maiúscula e um número.";
    }

    if (empty($erros)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // Criar utilizador
        $pdo->prepare("INSERT INTO utilizadores (nome, email, palavra_passe) VALUES (?, ?, ?)")
            ->execute([$nome, $email, $hash]);

        $id_utilizador = $pdo->lastInsertId();

        // Criar dados adicionais
        $pdo->prepare("INSERT INTO clientes_dados (id_utilizador, nome, telefone, morada, nif) VALUES (?, ?, ?, ?, ?)")
            ->execute([$id_utilizador, $nome, $telefone, $morada, $nif]);

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
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Telefone:</label>
            <input type="text" name="telefone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Morada:</label>
            <textarea name="morada" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>NIF:</label>
            <input type="text" name="nif" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Palavra-passe:</label>
            <input type="password" name="senha" class="form-control" required>
        </div>

        <button class="btn btn-success">Criar Conta</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
