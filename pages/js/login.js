
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Impede o envio padrão do formulário

      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;

      if (username === 'admin' && password === '12345') {
        window.location.href = 'admin-dashboard.html'; // Redireciona se o login for correto
      } else {
        document.getElementById('errorMsg').style.display = 'block'; // Mostra mensagem de erro
      }
    });
