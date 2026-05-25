<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../includes/layout.php';

// A sessão já é iniciada dentro do layout.php, mas precisamos dela antes para a lógica.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Se o usuário já está logado, redireciona para o painel principal.
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: /admin/');
    exit;
}

// 2. Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :u");
    $stmt->execute([':u' => $usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['logado'] = true;
        $_SESSION['admin_nome'] = $user['nome'];
        $_SESSION['admin_id'] = $user['id'];
        
        header('Location: /admin/');
        exit;
    } else {
        $_SESSION['flash'] = ['msg' => 'Usuário ou senha incorretos!', 'tipo' => 'danger'];
        header('Location: login.php');
        exit;
    }
}

// 3. Renderiza o cabeçalho da página
render_head('Login - Acesso Administrativo');

?>
<header class="bg-dark text-white py-4 text-center shadow-sm">
    <div class="container">
        <h1 class="h3"><i class="bi bi-shield-lock"></i> Minha Loja</h1>
        <p class="mb-0 lead">Painel Administrativo</p>
    </div>
</header>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <?php 
            // 4. Renderiza as mensagens flash de forma independente
            render_flash_messages(); 
            ?>
            <div class="card shadow-sm mt-3">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Faça seu login</h4>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuário</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" name="senha" id="senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// 5. Renderiza o rodapé da página
render_footer();
?>
