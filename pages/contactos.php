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

// Processar o formulário de contacto se for submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    // Limpar e validar os dados de entrada
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $assunto = trim($_POST['assunto']);
    $mensagem = trim($_POST['mensagem']);
    
    // Validação básica
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "O campo nome é obrigatório.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Por favor, insira um email válido.";
    }
    
    if (empty($assunto)) {
        $erros[] = "O campo assunto é obrigatório.";
    }
    
    if (empty($mensagem)) {
        $erros[] = "O campo mensagem é obrigatório.";
    }
    
    // Se não houver erros, inserir no banco de dados
    if (empty($erros)) {
        try {
            $stmt = $conn->prepare("INSERT INTO contactos (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $assunto, $mensagem]);
            
            // Mensagem de sucesso
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Sua mensagem foi enviada com sucesso! Entraremos em contacto em breve.'
            ];
            
            // Redirecionar para evitar reenvio do formulário
            header('Location: contactos.php');
            exit;
            
        } catch (PDOException $e) {
            $erros[] = "Ocorreu um erro ao enviar sua mensagem. Por favor, tente novamente mais tarde.";
        }
    }
    
    // Se houver erros, armazenar para exibição
    if (!empty($erros)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => implode("<br>", $erros)
        ];
        
        // Armazenar os valores do formulário para preenchimento automático
        $_SESSION['form_data'] = [
            'nome' => $nome,
            'email' => $email,
            'assunto' => $assunto,
            'mensagem' => $mensagem
        ];
        
        header('Location: contactos.php');
        exit;
    }
}
?>
<!doctype html>
<html class="no-js" lang="pt-br">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Instituto politécnico 30 De Setembro, contactos, Angola">
    <meta name="description" content="Página de contactos do Instituto politécnico 30 De Setembro">
    <meta name='copyright' content='Instituto politécnico 30 De Setembro'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Contactos - Instituto politécnico 30 De Setembro</title>

    <!-- Favicon -->
    <link rel="icon" href="../img/30 DE SEPTEMBRO.png">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/icofont.css">
    <link rel="stylesheet" href="../css/slicknav.min.css">
    <link rel="stylesheet" href="../css/owl-carousel.css">
    <link rel="stylesheet" href="../css/animate.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/responsive.css">
    
    <style>
        .contact-banner {
            background-image: url('../img/ac.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .contact-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .contact-banner h1 {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
        }
        .contact-banner p {
            font-size: 18px;
            position: relative;
        }
        .contact-info-section {
            padding: 80px 0;
        }
        .contact-info-box {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 5px;
            height: 100%;
        }
        .contact-info-box h3 {
            margin-bottom: 20px;
            color: #2c2c2c;
        }
        .contact-info-box p {
            margin-bottom: 15px;
        }
        .contact-form-box {
            padding: 30px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .contact-form-box h3 {
            margin-bottom: 20px;
            color: #2c2c2c;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 50px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        textarea.form-control {
            height: auto;
            min-height: 150px;
        }
        .btn-contact {
            background: #007bff;
            color: #fff;
            padding: 12px 30px;
            border-radius: 3px;
            border: none;
            font-weight: 600;
        }
        .btn-contact:hover {
            background: #0056b3;
            color: #fff;
        }
        .map-container {
            margin-top: 40px;
        }
        .social-media a {
            display: inline-block;
            margin-right: 10px;
            color: #007bff;
            font-size: 20px;
        }
        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        .faq-question {
            padding: 15px 20px;
            background: #f9f9f9;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-answer {
            padding: 20px;
            display: none;
        }
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
    </style>
</head>

<body>

    <div class="preloader">
        <div class="loader">
            <div class="loader-outter"></div>
            <div class="loader-inner"></div>
            <div class="indicator">
                <svg width="16px" height="12px">
                    <polyline id="back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                    <polyline id="front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                </svg>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="header-inner">
            <div class="container">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <!-- Logo -->
                            <div class="logo">
                                <img class="img_logo" width="70" src="../img/30 DE SEPTEMBRO.png" alt="Logo">
                            </div>
                            <!-- Mobile Nav -->
                            <div class="mobile-nav"></div>
                        </div>
                        <div class="col-lg-7 col-md-9 col-12">
                            <!-- Main Menu -->
                            <div class="main-menu">
                                <nav class="navigation">
                                    <ul class="nav menu">
                                        <li><a href="../index.php">Inicio</a></li>
                                         <li><a href="publicacoes.php">Eventos <i class="icofont-rounded-down"></i></a>
                                            <ul class="dropdown">
                                                <li><a href="publicacoes.html">Publicações e actualizações</a></li>
                                                <li><a href="estadoCandidatura.php">Verificar estado de candidatura</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="cursos.php">Cursos</a></li>
                                        <li><a href="contactos.php">Contactos</a></li>
                                        <li><a href="sobre.php">Sobre nós</a></li>
                                        <li><a href="admin/login.php">Login</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-lg-2 col-12">
                            <div class="get-quote">
                                <a href="inscricao.php" class="btn">Inscrição</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contact Banner -->
    <section class="contact-banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Contacte-nos</h1>
                    <p>Estamos disponíveis para responder às suas questões</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="contact-info-section">
        <div class="container">
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?= $flash_message['type'] ?>">
                    <?= $flash_message['message'] ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="contact-info-box">
                        <h3>Informações de Contacto</h3>
                        <p><strong><i class="fa fa-map-marker"></i> Endereço:</strong> Av. 30 de Setembro, Luanda, Angola</p>
                        <p><strong><i class="fa fa-phone"></i> Telefone:</strong> +244 123 456 789</p>
                        <p><strong><i class="fa fa-envelope"></i> Email:</strong> info@ip30setembro.edu.ao</p>
                        <p><strong><i class="fa fa-clock-o"></i> Horário:</strong> Segunda a Sexta, 8:00 - 18:00</p>
                        
                        <div class="social-media">
                            <h4>Siga-nos nas redes sociais:</h4>
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                            <a href="#"><i class="fa fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="contact-form-box">
                        <h3>Envie-nos uma mensagem</h3>
                        <form action="contactos.php" method="POST">
                            <div class="form-group">
                                <input type="text" name="nome" class="form-control" placeholder="Seu nome" required 
                                    value="<?= isset($_SESSION['form_data']['nome']) ? htmlspecialchars($_SESSION['form_data']['nome']) : '' ?>">
                                <?php unset($_SESSION['form_data']['nome']); ?>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Seu email" required
                                    value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>">
                                <?php unset($_SESSION['form_data']['email']); ?>
                            </div>
                            <div class="form-group">
                                <input type="text" name="assunto" class="form-control" placeholder="Assunto" required
                                    value="<?= isset($_SESSION['form_data']['assunto']) ? htmlspecialchars($_SESSION['form_data']['assunto']) : '' ?>">
                                <?php unset($_SESSION['form_data']['assunto']); ?>
                            </div>
                            <div class="form-group">
                                <textarea name="mensagem" class="form-control" placeholder="Sua mensagem" required><?= isset($_SESSION['form_data']['mensagem']) ? htmlspecialchars($_SESSION['form_data']['mensagem']) : '' ?></textarea>
                                <?php unset($_SESSION['form_data']['mensagem']); ?>
                            </div>
                            <button type="submit" class="btn btn-contact">Enviar Mensagem</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Map Section -->
            <div class="row">
                <div class="col-12">
                    <div class="map-container">
                        <h3>Nossa Localização</h3>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1970.41704689055!2d13.153512147496391!3d-8.987409532815944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a5221b99e3e63f9%3A0x634019f3103c2341!2sIMP%2030%20de%20Setembro%20UGP!5e0!3m2!1spt-PT!2sao!4v1743249727822!5m2!1spt-PT!2sao" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="row">
                <div class="col-12">
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
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer">
                            <h2>Instituto politécnico 30 De Setembro</h2>
                            <p>Educação de qualidade para formar os líderes de amanhã.</p>
                            <ul class="social">
                                <li><a href="#"><i class="icofont-facebook"></i></a></li>
                                <li><a href="#"><i class="icofont-google-plus"></i></a></li>
                                <li><a href="#"><i class="icofont-twitter"></i></a></li>
                                <li><a href="#"><i class="icofont-vimeo"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer f-link">
                            <h2>Links</h2>
                            <ul>
                                <li><a href="../index.php"><i class="fa fa-caret-right"></i> Inicio</a></li>
                                <li><a href="eventos.php"><i class="fa fa-caret-right"></i> Eventos</a></li>
                                <li><a href="cursoinfor.html"><i class="fa fa-caret-right"></i> Cursos</a></li>
                                <li><a href="contactos.php"><i class="fa fa-caret-right"></i> Contactos</a></li>
                                <li><a href="sobre.php"><i class="fa fa-caret-right"></i> Sobre nós</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer">
                            <h2>Contactos</h2>
                            <p>Av. 30 de Setembro, Luanda, Angola</p>
                            <ul class="time-sidual">
                                <li class="day">Telefone: <span>+244 123 456 789</span></li>
                                <li class="day">Email: <span>info@ip30setembro.edu.ao</span></li>
                                <li class="day">Horário: <span>Seg-Sex: 8:00-18:00</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-12">
                        <div class="copyright-content">
                            <p>Instituto politécnico 30 de Setembro &copy; <?= date('Y') ?> - Todos os direitos reservados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-migrate-3.0.0.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/easing.js"></script>
    <script src="../js/colors.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/jquery.nav.js"></script>
    <script src="../js/slicknav.min.js"></script>
    <script src="../js/jquery.scrollUp.min.js"></script>
    <script src="../js/niceselect.js"></script>
    <script src="../js/tilt.jquery.min.js"></script>
    <script src="../js/owl-carousel.js"></script>
    <script src="../js/jquery.counterup.min.js"></script>
    <script src="../js/steller.js"></script>
    <script src="../js/wow.min.js"></script>
    <script src="../js/jquery.magnific-popup.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // FAQ Toggle Functionality
        $(document).ready(function() {
            $('.faq-question').click(function() {
                $(this).next('.faq-answer').slideToggle();
                $(this).find('.toggle-icon').text(function(_, text) {
                    return text === '+' ? '-' : '+';
                });
            });
        });
    </script>
</body>
</html>