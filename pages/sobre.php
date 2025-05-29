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
<!doctype html>
<html class="no-js" lang="pt-br">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Instituto politécnico 30 De Setembro, sobre, história, Angola">
    <meta name="description" content="Conheça mais sobre o Instituto politécnico 30 De Setembro">
    <meta name='copyright' content='Instituto politécnico 30 De Setembro'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Sobre Nós - Instituto politécnico 30 De Setembro</title>

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
        .about-banner {
            background-image: url('../img/ab.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .about-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .about-banner h1 {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
        }
        .about-section {
            padding: 80px 0;
        }
        .about-content h2 {
            color: #2c2c2c;
            margin-bottom: 20px;
            font-size: 32px;
        }
        .about-content p {
            margin-bottom: 20px;
            line-height: 1.8;
            color: #555;
        }
        .about-content ul {
            margin-bottom: 30px;
            padding-left: 20px;
        }
        .about-content ul li {
            margin-bottom: 10px;
            color: #555;
        }
        .mission-vision {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 40px;
        }
        .mission, .vision {
            flex: 1;
            min-width: 300px;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .mission h3, .vision h3 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 24px;
        }
        .values-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .value-item {
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
            border-radius: 3px;
        }
        .value-item h4 {
            color: #2c2c2c;
            margin-bottom: 10px;
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
                                                <li><a href="publicacoes.php">Publicações e actualizações</a></li>
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

    <!-- About Banner -->
    <section class="about-banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Sobre Nós</h1>
                    <p>Conheça nossa história, missão e valores</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?= $flash_message['type'] ?>">
                    <?= $flash_message['message'] ?>
                </div>
            <?php endif; ?>

            <div class="about-content">
                <div class="row">
                    <div class="col-12">
                        <h2>Nossa História</h2>
                        <p>O Instituto Politécnico 30 De Setembro foi fundado em [ano de fundação] com o objetivo de proporcionar educação técnica e profissional de qualidade, contribuindo para o desenvolvimento do país. Desde então, temos nos dedicado a formar profissionais competentes e cidadãos comprometidos com o progresso da sociedade.</p>
                        
                        <h2>Nossos Valores</h2>
                        <div class="values-list">
                            <div class="value-item">
                                <h4>Excelência acadêmica</h4>
                                <p>Compromisso com o ensino de qualidade e a constante atualização dos conhecimentos.</p>
                            </div>
                            <div class="value-item">
                                <h4>Inovação e criatividade</h4>
                                <p>Incentivo ao pensamento criativo e à busca por soluções inovadoras.</p>
                            </div>
                            <div class="value-item">
                                <h4>Responsabilidade social</h4>
                                <p>Comprometimento com o desenvolvimento sustentável e o bem-estar da comunidade.</p>
                            </div>
                            <div class="value-item">
                                <h4>Integridade e ética</h4>
                                <p>Conduta baseada em princípios éticos e valores morais sólidos.</p>
                            </div>
                        </div>
                        
                        <div class="mission-vision">
                            <div class="mission">
                                <h3>Missão</h3>
                                <p>Formar profissionais qualificados e cidadãos responsáveis através do ensino técnico e profissional, promovendo o desenvolvimento pessoal e contribuindo para o progresso da sociedade.</p>
                            </div>
                            <div class="vision">
                                <h3>Visão</h3>
                                <p>Ser referência nacional em educação politécnica, reconhecido pela qualidade do ensino, pela empregabilidade dos seus graduados e pelo impacto positivo na comunidade.</p>
                            </div>
                        </div>
                        
                        <h2>Nossa Estrutura</h2>
                        <p>O Instituto conta com modernas instalações, laboratórios equipados e um corpo docente qualificado, proporcionando aos alunos as melhores condições para o seu desenvolvimento acadêmico e profissional. Nossas salas de aula são projetadas para oferecer conforto e tecnologia, enquanto nossos laboratórios permitem a prática e aplicação dos conhecimentos teóricos.</p>
                        
                        <p>Além disso, temos uma biblioteca bem equipada, espaços de convivência para os alunos e áreas esportivas que complementam a formação integral dos nossos estudantes.</p>
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
                                <li><a href="cursos.php"><i class="fa fa-caret-right"></i> Cursos</a></li>
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
</body>
</html>