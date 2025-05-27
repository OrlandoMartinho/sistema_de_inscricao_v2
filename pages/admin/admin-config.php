<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');

// Processar o formulário de alteração de senha
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_pass'] ?? '';
    $new_password = $_POST['new_pass'] ?? '';
    $confirm_password = $_POST['confirm_pass'] ?? '';
    
    // Verificar se a nova senha e a confirmação coincidem
    if ($new_password !== $confirm_password) {
        $_SESSION['msg'] = '<div class="alert error">As senhas não coincidem!</div>';
        header("Location: admin-config.php");
        exit;
    }
    
    // Obter a senha atual do banco de dados
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Verificar a senha atual
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['msg'] = '<div class="alert error">Senha atual incorreta!</div>';
        header("Location: admin-config.php");
        exit;
    }
    
    // Atualizar a senha no banco de dados
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $update_stmt->bind_param("ss", $hashed_password, $username);
    
    if ($update_stmt->execute()) {
        $_SESSION['msg'] = '<div class="alert success">Senha atualizada com sucesso!</div>';
    } else {
        $_SESSION['msg'] = '<div class="alert error">Erro ao atualizar a senha!</div>';
    }
    
    header("Location: admin-config.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Configurações</title>
    <link rel="stylesheet" href="../css1/admin/config.css"> 
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .error {
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
        }
        .success {
            background-color: #ddffdd;
            color: #4F8A10;
            border: 1px solid #4F8A10;
        }
    </style>
</head>
<body>
    <header style="background-image: url('../../img/ac.jpg'); height: 150px;">
        <div class="container">
            <nav>
                <a href="../index.html" class="logo">IP30SET</a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Painel de Administração</h3>
            <div class="admin-menu">
                <a href="admin-dashboard.php"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.php"><i>📅</i> Eventos</a>
                <a href="admin-cursos.php" ><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php"><i>📝</i> Inscrições</a>
                <a href="admin-config.php" class="active"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Configurações do Sistema</h2>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Sair</button>
            </div>
            
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="openTab(event, 'email-settings')">Config. de Segurança</button>
            </div>
        
            <div id="email-settings" class="tab-content active">
                <div class="settings-form">
                    <h3>Alterar Credenciais de Acesso</h3>
                    <form action="admin-config.php" method="post">
                        <?php 
                            if(isset($_SESSION['msg'])){
                                echo $_SESSION['msg'];
                                unset($_SESSION['msg']);
                            }
                        ?>        

                        <div class="form-group">
                            <label for="smtp-user">Nome de Usuário</label>
                            <input type="text" id="smtp-user" value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="current-pass">Senha Atual</label>
                            <input type="password" id="current-pass" name="current_pass" placeholder="Digite sua senha atual" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new-pass">Nova Senha</label>
                            <input type="password" id="new-pass" name="new_pass" placeholder="Digite a nova senha" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm-pass">Confirmar Nova Senha</label>
                            <input type="password" id="confirm-pass" name="confirm_pass" placeholder="Confirme a nova senha" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="save-btn">Atualizar Credenciais</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            // Esconde todos os conteúdos de tab
            var tabcontents = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabcontents.length; i++) {
                tabcontents[i].classList.remove("active");
            }
            
            // Remove a classe active de todos os botões
            var tabbuttons = document.getElementsByClassName("tab-btn");
            for (var i = 0; i < tabbuttons.length; i++) {
                tabbuttons[i].classList.remove("active");
            }
            
            // Mostra a tab atual e adiciona a classe active ao botão
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>
</body>
</html>