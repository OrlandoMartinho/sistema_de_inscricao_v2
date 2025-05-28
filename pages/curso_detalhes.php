<?php
// Inicia a sessão para mensagens flash
session_start();

// Conexão com o banco de dados
require_once '../config/connection.php';

// Verificar se o ID do curso foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: cursos.php');
    exit;
}

$curso_id = $_GET['id'];

// Buscar detalhes do curso
try {
    $stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ? AND status = 'ativo'");
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch();
    
    if (!$curso) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Curso não encontrado ou indisponível.'
        ];
        header('Location: cursos.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Erro ao carregar curso: ' . $e->getMessage()
    ];
    header('Location: cursos.php');
    exit;
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
    <meta name="description" content="<?= htmlspecialchars($curso['nome']) ?> - Instituto politécnico 30 De Setembro">
    <meta name='copyright' content='Instituto politécnico 30 De Setembro'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title><?= htmlspecialchars($curso['nome']) ?> - Instituto politécnico 30 De Setembro</title>

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
        .course-detail-banner {
            background-image: url('../img/course-detail-banner.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .course-detail-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .course-detail-banner h1 {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
        }
        .course-detail-section {
            padding: 80px 0;
        }
        .course-detail-img {
            margin-bottom: 30px;
            border-radius: 5px;
            overflow: hidden;
        }
        .course-detail-img img {
            width: 100%;
            height: auto;
            display: block;
        }
        .course-detail-content h2 {
            margin-bottom: 20px;
            color: #2c2c2c;
        }
        .course-meta {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .meta-item {
            margin-right: 30px;
            margin-bottom: 15px;
        }
        .meta-item i {
            color: #007bff;
            margin-right: 5px;
        }
        .course-detail-content p {
            margin-bottom: 20px;
            line-height: 1.8;
        }
        .enroll-btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 12px 30px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .enroll-btn:hover {
            background: #0056b3;
            color: #fff;
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

    <!-- Course Detail Banner -->
    <section class="course-detail-banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><?= htmlspecialchars($curso['nome']) ?></h1>
                    <p>Conheça mais sobre este curso</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Detail Section -->
    <section class="course-detail-section">
        <div class="container">
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?= $flash_message['type'] ?>">
                    <?= $flash_message['message'] ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="course-detail-content">
                        <h2>Sobre o Curso</h2>
                        
                        <div class="course-meta">
                            <div class="meta-item">
                                <i class="fa fa-clock-o"></i>
                                <span>Duração: <?= htmlspecialchars($curso['duracao']) ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-level-up"></i>
                                <span>Nível: <?= htmlspecialchars($curso['nivel']) ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-user"></i>
                                <span>Instrutor: <?= htmlspecialchars($curso['instrutor']) ?></span>
                            </div>
                        </div>
                        
                        <?= nl2br(htmlspecialchars($curso['descricao'])) ?>
                        
                        <a href="inscricao.php?curso_id=<?= $curso['id'] ?>" class="enroll-btn">Inscreva-se Agora</a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-5 col-12">
                    <div class="course-detail-img">
                        <img src="../img/cursos/<?= htmlspecialchars($curso['id']) ?>.jpg" alt="<?= htmlspecialchars($curso['nome']) ?>">
                    </div>
                    
                    <div class="course-info-box">
                        <h3>Informações do Curso</h3>
                        <ul>
                            <li><strong>Duração:</strong> <?= htmlspecialchars($curso['duracao']) ?></li>
                            <li><strong>Nível:</strong> <?= htmlspecialchars($curso['nivel']) ?></li>
                            <li><strong>Instrutor:</strong> <?= htmlspecialchars($curso['instrutor']) ?></li>
                            <li><strong>Data de Início:</strong> Próxima turma em breve</li>
                            <li><strong>Vagas:</strong> Limitadas</li>
                        </ul>
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
                    <div class="col-lg-3 col-md-6 col-12