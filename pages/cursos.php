<?php
// Inicia a sessão para mensagens flash
session_start();

// Conexão com o banco de dados
require_once '../config/connection.php';

// Buscar cursos ativos do banco de dados
try {
    $stmt = $conn->query("SELECT * FROM cursos WHERE status = 'ativo' ORDER BY nome");
    $cursos = $stmt->fetch_all(MYSQLI_ASSOC);
} catch (PDOException $e) {
    $cursos = [];
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Erro ao carregar cursos: ' . $e->getMessage()
    ];
}

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
    <meta name="keywords" content="Instituto politécnico 30 De Setembro, cursos, Angola">
    <meta name="description" content="Página de cursos do Instituto politécnico 30 De Setembro">
    <meta name='copyright' content='Instituto politécnico 30 De Setembro'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Cursos - Instituto politécnico 30 De Setembro</title>

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
        .courses-banner {
            background-image: url('../img/courses-banner.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .courses-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .courses-banner h1 {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
        }
        .courses-banner p {
            font-size: 18px;
            position: relative;
        }
        .courses-section {
            padding: 80px 0;
        }
        .course-card {
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .course-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .course-img {
            height: 200px;
            overflow: hidden;
        }
        .course-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .course-card:hover .course-img img {
            transform: scale(1.05);
        }
        .course-content {
            padding: 20px;
        }
        .course-content h3 {
            margin-bottom: 15px;
            color: #2c2c2c;
        }
        .course-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: #666;
        }
        .course-meta span {
            display: flex;
            align-items: center;
        }
        .course-meta i {
            margin-right: 5px;
            color: #007bff;
        }
        .course-btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 8px 20px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .course-btn:hover {
            background: #0056b3;
            color: #fff;
        }
        .filter-section {
            margin-bottom: 40px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .filter-section h3 {
            margin-bottom: 20px;
            color: #2c2c2c;
        }
        .filter-group {
            margin-bottom: 15px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .filter-group select, 
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
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
                                        <li><a href="eventos.php">Eventos <i class="icofont-rounded-down"></i></a>
                                            <ul class="dropdown">
                                               <li><a href="publicacoes.php">Eventos <i class="icofont-rounded-down"></i></a>
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

    <!-- Courses Banner -->
    <section class="courses-banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Nossos Cursos</h1>
                    <p>Descubra as oportunidades de formação que oferecemos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="courses-section">
        <div class="container">
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?= $flash_message['type'] ?>">
                    <?= $flash_message['message'] ?>
                </div>
            <?php endif; ?>

            <!-- Filtros de Cursos -->
            <div class="filter-section">
                <h3>Filtrar Cursos</h3>
                <form method="GET" action="cursos.php">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="filter-group">
                                <label for="nivel">Nível</label>
                                <select name="nivel" id="nivel" class="form-control">
                                    <option value="">Todos os níveis</option>
                                    <option value="básico">Básico</option>
                                    <option value="intermédio">Intermédio</option>
                                    <option value="avançado">Avançado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-group">
                                <label for="duracao">Duração</label>
                                <select name="duracao" id="duracao" class="form-control">
                                    <option value="">Todas as durações</option>
                                    <option value="3 meses">3 meses</option>
                                    <option value="6 meses">6 meses</option>
                                    <option value="1 ano">1 ano</option>
                                    <option value="2 anos">2 anos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-group">
                                <label for="search">Pesquisar</label>
                                <input type="text" name="search" id="search" placeholder="Nome do curso ou instrutor" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="cursos.php" class="btn btn-secondary">Limpar filtros</a>
                </form>
            </div>

            <!-- Lista de Cursos -->
            <div class="row">
                <?php if (empty($cursos)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            Nenhum curso disponível no momento. Por favor, volte mais tarde.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($cursos as $curso): ?>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="course-card">
                               
                                <div class="course-content">
                                    <h3><?= htmlspecialchars($curso['nome']) ?></h3>
                                    <div class="course-meta">
                                        <span><i class="fa fa-clock-o"></i> <?= htmlspecialchars($curso['duracao']) ?></span>
                                        <span><i class="fa fa-level-up"></i> <?= htmlspecialchars($curso['nivel']) ?></span>
                                    </div>
                                    <p><?= nl2br(htmlspecialchars(substr($curso['descricao'], 0, 100))) ?>...</p>
                                    <div class="course-meta">
                                        <span><i class="fa fa-user"></i> <?= htmlspecialchars($curso['instrutor']) ?></span>
                                    </div>
                            
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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