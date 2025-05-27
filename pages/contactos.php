<?php
// Inicia a sessão para mensagens flash
session_start();

// Conexão com o banco de dados
require_once '../config/connection.php';

// Verifica se há mensagens flash para exibir
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos - Instituto politécnico 30 De Setembro</title>
    <link rel="stylesheet" href="./css1/contactos.css">
    <link rel="stylesheet" href="./css1/style.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .faq-question {
            padding: 15px;
            background-color: #f5f5f5;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-answer {
            padding: 15px;
            display: none;
            background-color: white;
        }
        .contact-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 40px;
        }
        .contact-text, .contact-form {
            flex: 1;
            min-width: 300px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            min-height: 150px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .map-container {
            margin-top: 20px;
        }
        .social-media {
            margin-top: 20px;
        }
        .social-media a {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <!--inicio do header-->
    <header style="background-image: url('../img/img2.jpg');">
        <div class="container">
            <nav>
                <!--logo-->
                <a href="../index.html">
                    <!-- Add your logo here if needed -->
                </a>
                <!--end logo-->
                <ul>
                    <a href="../index.php">Home</a>
                    <a href="admin/login.php">Login</a>
                    <a href="eventos.php">Eventos</a>
                    <a href="contactos.php">Contactos</a>
                    <a href="sobre.php">Sobre nós</a>
                    <a class="btn" href="inscricao.php">Inscrição</a>

                    <div class="close-icon">
                        <img src="../img/close.png" alt="">
                    </div>
                </ul>
                <div class="menu-icon">
                    <img src="../img/menu.png" alt="">
                </div>
            </nav>
            <section class="banner">
                <div class="banner-text">
                    <h1>Contacte-nos</h1>
                    <p>Estamos disponíveis para responder às suas questões</p>
                </div>
            </section>
        </div>
    </header>
    <!--end header-->

    <!-- Contact Information Section -->
    <section class="contact-info">
        <div class="container">
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?= $flash_message['type'] ?>">
                    <?= $flash_message['message'] ?>
                </div>
            <?php endif; ?>

            <div class="contact-wrapper">
                <div class="contact-text">
                    <h3>Informações de Contacto</h3>
                    <p><strong>Endereço:</strong> Av. 30 de Setembro, Luanda, Angola</p>
                    <p><strong>Telefone:</strong> +244 123 456 789</p>
                    <p><strong>Email:</strong> info@ip30setembro.edu.ao</p>
                    <p><strong>Horário de Funcionamento:</strong> Segunda a Sexta, 8:00 - 18:00</p>
                    
                    <div class="social-media">
                        <h4>Siga-nos nas redes sociais:</h4>
                        <a href="#"><img src="../img/facebook-icon.png" alt="Facebook" width="32"></a>
                        <a href="#"><img src="../img/instagram-icon.png" alt="Instagram" width="32"></a>
                        <a href="#"><img src="../img/twitter-icon.png" alt="Twitter" width="32"></a>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Envie-nos uma mensagem</h3>
                    <form action="processar_contacto.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="nome" placeholder="Seu nome" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Seu email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="assunto" placeholder="Assunto" required>
                        </div>
                        <div class="form-group">
                            <textarea name="mensagem" placeholder="Sua mensagem" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <h3>Localização</h3>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1970.41704689055!2d13.153512147496391!3d-8.987409532815944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a5221b99e3e63f9%3A0x634019f3103c2341!2sIMP%2030%20de%20Setembro%20UGP!5e0!3m2!1spt-PT!2sao!4v1743249727822!5m2!1spt-PT!2sao" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h3>Perguntas Frequentes</h3>
            <div class="faq-item">
                <div class="faq-question">
                    <h4>Quais são os horários de atendimento?</h4>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Atendemos de segunda a sexta-feira, das 8:00 às 18:00 horas. Aos sábados das 9:00 às 13:00 horas.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h4>Como posso fazer a minha inscrição?</h4>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Pode fazer a sua inscrição online através do nosso site na página de inscrições, ou presencialmente na nossa secretaria.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h4>Quais documentos são necessários para matrícula?</h4>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Para efetuar a matrícula, são necessários: BI ou passaporte, certificado de habilitações, 2 fotos tipo passe e comprovativo de pagamento da taxa de matrícula.</p>
                </div>
            </div>
        </div>
    </section>

    <!--footer-->
    <footer>
        <div class="container">
            <div class="links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">Twitter</a>
            </div>
            <p>Instituto politécnico 30 de Setembro &copy; 2023 - Todos os direitos reservados</p>
        </div>
    </footer>
    <!--end footer-->

    <script>
        // Simple FAQ toggle functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('.toggle-icon');
                
                if (answer.style.display === 'block') {
                    answer.style.display = 'none';
                    icon.textContent = '+';
                } else {
                    answer.style.display = 'block';
                    icon.textContent = '-';
                }
            });
        });
    </script>
</body>
</html>