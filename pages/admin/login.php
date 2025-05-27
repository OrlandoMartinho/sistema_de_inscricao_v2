<?php

session_start();

if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == true) {
    header("Location: admin-dashboard.php");
    exit;
}

include('../../services/login.php');


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Login</title>
  <link rel="stylesheet" href="../css1/admin/login.css" />
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    /* Estilos para o modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 400px;
      border-radius: 5px;
      text-align: center;
    }
    
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    
    .close:hover {
      color: black;
    }
    
    .modal-btn {
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 20px;
    }
    
    .error-modal {
      color: #721c24;
      background-color: #f8d7da;
      border-color: #f5c6cb;
    }
    
    .success-modal {
      color: #155724;
      background-color: #d4edda;
      border-color: #c3e6cb;
    }
  </style>
</head>
<body>
  <header style="background-image: url('../../img/background-home.jpg'); height: 150px;">
    <div class="container">
      <nav>
        <a href="../../index.php" class="logo">IP30SET</a>
      </nav>
    </div>
  </header>

  <section class="admin-login">
    <h2>Área de Administração</h2>
    <form id="loginForm" method="POST" action="">
      <div class="form-group">
        <label for="username">Nome de Usuário</label>
        <input type="text" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="login-btn">Entrar</button>
    </form>
    <?php if (isset($login_error)): ?>
      <p id="errorMsg" style="color:red;"><?php echo $login_error; ?></p>
    <?php endif; ?>
  </section>

  <!-- Modal de Erro de Login -->
  <div id="errorModal" class="modal">
    <div class="modal-content error-modal">
      <span class="close" onclick="closeModal('errorModal')">&times;</span>
      <h3>Erro de Login</h3>
      <p id="modal-error-msg">Usuário ou senha incorretos!</p>
      <button class="modal-btn" onclick="closeModal('errorModal')">OK</button>
    </div>
  </div>

  <!-- Modal de Sucesso (redirecionamento) -->
  <div id="successModal" class="modal">
    <div class="modal-content success-modal">
      <h3>Login Bem-sucedido</h3>
      <p>Redirecionando para o painel...</p>
    </div>
  </div>

  <script>
    // Fechar modais
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    // Mostrar modal de erro se houver mensagem de erro
    <?php if (isset($login_error)): ?>
      document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('errorModal').style.display = 'block';
      });
    <?php endif; ?>

    // Validação do formulário no cliente
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      
      if (username === '' || password === '') {
        e.preventDefault();
        document.getElementById('modal-error-msg').textContent = 'Por favor, preencha todos os campos!';
        document.getElementById('errorModal').style.display = 'block';
      }
    });

    // Fechar modais ao clicar fora
    window.onclick = function(event) {
      if (event.target.className === 'modal') {
        event.target.style.display = 'none';
      }
    }
  </script>
</body>
</html>