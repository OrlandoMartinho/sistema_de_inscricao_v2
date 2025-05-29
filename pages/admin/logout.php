<?php
// Inicie a sessão (se ainda não estiver iniciada)
session_start();

// Destrua todas as variáveis de sessão
$_SESSION = array();

// Se desejar destruir o cookie de sessão também
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrua a sessão
session_destroy();

// Redirecione para a página de login
header("Location: login.php");
exit;
?>