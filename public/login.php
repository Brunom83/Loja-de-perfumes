<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

$erro = '';

if (isset($_SESSION['tipo'])) {
    if ($_SESSION['tipo'] === 'admin') {
        header("Location: ../admin/dashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $agora = time();
        $ultimaTentativa = strtotime($user['ultima_tentativa'] ?? '2000-01-01');
        $diferenca = $agora - $ultimaTentativa;

        if ($user['bloqueado'] && $diferenca < 300) {
            $resta = 300 - $diferenca;
            $min = floor($resta / 60);
            $seg = $resta % 60;
            echo "<p class='text-warning'>Conta bloqueada. Tenta novamente em <span id='timer'>{$min}m {$seg}s</span></p>";
            ?>
            <script>
                let segundos = <?= $resta ?>;
                const timer = document.getElementById('timer');
                setInterval(() => {
                    segundos--;
                    const m = Math.floor(segundos / 60);
                    const s = segundos % 60;
                    timer.textContent = `${m}m ${s}s`;
                    if (segundos <= 0) location.reload();
                }, 1000);
            </script>
            <?php
        } else {
            if (password_verify($senha, $user['palavra_passe'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['tipo'] = $user['tipo'];

                $pdo->prepare("UPDATE utilizadores SET tentativas_login = 0, bloqueado = 0 WHERE id = ?")
                    ->execute([$user['id']]);

                if ($user['tipo'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: /index.php");
                }
                exit;
            } else {
                $tentativas = $user['tentativas_login'] + 1;
                $bloqueado = $tentativas >= 3 ? 1 : 0;

                $pdo->prepare("UPDATE utilizadores SET tentativas_login = ?, bloqueado = ?, ultima_tentativa = NOW() WHERE id = ?")
                    ->execute([$tentativas, $bloqueado, $user['id']]);

                $restantes = max(0, 3 - $tentativas);
                echo "<p class='text-danger'>Palavra-passe incorreta. Restam $restantes tentativas.</p>";
            }
        }
    } else {
        echo "<p class='text-danger'>Email não encontrado.</p>";
    }
}
?>

<div class="container mt-5">
    <h2>Iniciar Sessão</h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label class="form-label text-light">Email:</label>
            <input type="email" name="email" class="form-control bg-dark text-light" required>
        </div>

        <div class="mb-3">
            <label class="form-label text-light">Palavra-passe:</label>
            <div class="position-relative">
                <input type="password" name="senha" id="senha" class="form-control bg-dark text-light pr-5" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="toggleSenha()">
                    👁️
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Entrar</button>
    </form>

    <p class="mt-3">Ainda não tem conta? <a href="register.php" class="text-success">Registe-se</a>.</p>
</div>

<script>
function toggleSenha() {
    const input = document.getElementById('senha');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

<?php require_once '../includes/footer.php'; ?>
