<?php

include('../../config/connection.php');


// Verificar se já existe algum usuário na tabela admin_users
$verificar = $conn->query("SELECT COUNT(*) as total FROM admin_users");
$row = $verificar->fetch_assoc();

if ($row['total'] == 0) {
    // Nenhum usuário cadastrado, criar um admin padrão
    $default_username = "admin";
    $default_password = "12345678"; // ⚠️ Troque essa senha depois!
    $hash = password_hash($default_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $default_username, $hash);
    $stmt->execute();
    $stmt->close();
}

// Processar o formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter dados do formulário
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);

    // Buscar usuário no banco de dados
    $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        // Verificar a senha
        if (password_verify($input_password, $hashed_password)) {
            // Autenticação bem-sucedida
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;

            // Redirecionar para o painel
            header("Location:admin-dashboard.php");
            exit;
        } else {
            // Senha incorreta
            $login_error = "Usuário ou senha incorretos!";
        }
    } else {
        // Usuário não encontrado
        $login_error = "Usuário ou senha incorretos!";
    }

    $stmt->close();
}

$conn->close();
?>
