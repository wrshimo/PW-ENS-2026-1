<?php
// Inicia ou resume a sessão existente
session_start();

// Limpa todas as variáveis de sessão (por exemplo, \$_SESSION['logado'])
$_SESSION = [];

// Destrói a sessão, removendo todos os dados do lado do servidor
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Reinicia a sessão para poder usar a mensagem flash
session_start();
$_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Logout efetuado com sucesso.'];

// Redireciona o usuário para a página de login
header('Location: /admin/login.php');
exit();
?>