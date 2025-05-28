<?php
// Inicia a sessão para mensagens flash
session_start();

// Conexão com o banco de dados
require_once '../config/connection.php';

// Buscar eventos ativos do banco de dados
try {
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE status = 'ativo' ORDER BY data_evento DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $eventos = $result->fetch_all(MYSQLI_ASSOC);
} catch (PDOException $e) {
    $eventos = [];
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Erro ao carregar eventos: ' . $e->getMessage()
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
    <meta name="keywords" content="Instituto politécnico 30 De Setembro, eventos, agenda, Angola">
    <meta name="description" content="Eventos do Instituto politécnico 30 De Setembro">
    <meta name='copyright' content='Instituto politécnico 30 De Setembro'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Eventos - Instituto politécnico 30 De Setembro</title>

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
        .events-banner {
              background-image: url('../img/ac.jpg');
           
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .events-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .events-banner h1 {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
        }
        .events-section {
            padding: 80px 0;
        }
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        .section-title h2 {
            font-size: 32px;
            color: #2c2c2c;
            display: inline-block;
            padding-bottom: 10px;
        }
        .section-title h2:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background: #007bff;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        .search-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 12px 20px;
            padding-left: 45px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 16px;
            outline: none;
            transition: border 0.3s ease;
        }
        .search-box input:focus {
            border-color: #007bff;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        .filter-dropdown {
            min-width: 200px;
        }
        .filter-dropdown select {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 16px;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            transition: border 0.3s ease;
        }
        .filter-dropdown select:focus {
            border-color: #007bff;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .event-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .event-image-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .event-card:hover .event-image {
            transform: scale(1.05);
        }
        .event-date {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .event-content {
            padding: 20px;
        }
        .event-title {
            font-size: 20px;
            margin-bottom: 10px;
            color: #2c2c2c;
        }
        .event-location {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }
        .event-location i {
            margin-right: 8px;
            color: #e74c3c;
        }
        .event-description {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .event-button {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        .event-button:hover {
            background: #0056b3;
            color: #fff;
        }
        .no-events {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
            background: #f9f9f9;
            border-radius: 10px;
        }
        .event-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        .event-status {
            color: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-upcoming {
            background-color: #2ecc71;
        }
        .status-past {
            background-color: #e74c3c;
        }
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
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

    <!-- Events Banner -->
    <section class="events-banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Eventos</h1>
                    <p>Fique por dentro da nossa agenda de eventos acadêmicos e culturais</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="events-section">
        <div class="container">
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?= $flash_message['type'] ?>">
                    <?= $flash_message['message'] ?>
                </div>
            <?php endif; ?>

            <div class="section-title">
                <h2>Próximos Eventos</h2>
            </div>
            
            <div class="search-filter">
                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" placeholder="Pesquisar eventos..." id="searchInput">
                </div>
                <div class="filter-dropdown">
                    <select id="filterSelect">
                        <option value="all">Todos os Eventos</option>
                        <option value="this-month">Este Mês</option>
                        <option value="next-month">Próximo Mês</option>
                        <option value="past">Eventos Passados</option>
                    </select>
                </div>
            </div>
            
            <?php if(empty($eventos)): ?>
                <div class="no-events">
                    <p>No momento não temos nenhum evento agendado. Por favor, verifique novamente mais tarde.</p>
                </div>
            <?php else: ?>
                <div class="events-grid" id="eventsGrid">
                    <?php foreach($eventos as $evento): 
                        $dataEvento = new DateTime($evento['data_evento']);
                        $now = new DateTime();
                        $isPastEvent = $dataEvento < $now;
                    ?>
                        <div class="event-card" 
                             data-title="<?= strtolower(htmlspecialchars($evento['titulo'])) ?>" 
                             data-date="<?= $evento['data_evento'] ?>"
                             data-past="<?= $isPastEvent ? 'true' : 'false' ?>">
                            <div class="event-image-container">
                                <?php if(!empty($evento['foto'])): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($evento['foto']) ?>" class="event-image" alt="Imagem do evento">
                                <?php else: ?>
                                    <img src="../img/event-default.jpg" class="event-image" alt="Imagem padrão de evento">
                                <?php endif; ?>
                                <div class="event-date">
                                    <?= date('d M', strtotime($evento['data_evento'])) ?>
                                </div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?= htmlspecialchars($evento['titulo']) ?></h3>
                                <div class="event-location">
                                    <i class="fa fa-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($evento['local']) ?></span>
                                </div>
                                <p class="event-description">
                                    <?= htmlspecialchars(substr($evento['descricao'], 0, 100) . (strlen($evento['descricao']) > 100 ? '...' : '')) ?>
                                </p>
                                <div class="event-meta">
                                    <span class="event-status <?= $isPastEvent ? 'status-past' : 'status-upcoming' ?>">
                                        <i class="fa fa-<?= $isPastEvent ? 'calendar-times' : 'calendar-check' ?>"></i>
                                        <?= $isPastEvent ? 'Evento Realizado' : date('H:i', strtotime($evento['data_evento'])) ?>
                                    </span>
                                    <a href="evento-detalhes.php?id=<?= $evento['id'] ?>" class="event-button">
                                        Ver Detalhes <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
    
    <script>
        // Filtro e pesquisa de eventos
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterSelect = document.getElementById('filterSelect');
            const eventCards = document.querySelectorAll('.event-card');
            
            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                const filterValue = filterSelect.value;
                const now = new Date();
                const currentMonth = now.getMonth();
                const currentYear = now.getFullYear();
                
                eventCards.forEach(card => {
                    const title = card.getAttribute('data-title');
                    const eventDate = new Date(card.getAttribute('data-date'));
                    const isPastEvent = card.getAttribute('data-past') === 'true';
                    
                    // Verificar pesquisa
                    const matchesSearch = title.includes(searchTerm);
                    
                    // Verificar filtro
                    let matchesFilter = true;
                    
                    if (filterValue === 'this-month') {
                        matchesFilter = eventDate.getMonth() === currentMonth && 
                                         eventDate.getFullYear() === currentYear &&
                                         !isPastEvent;
                    } else if (filterValue === 'next-month') {
                        let nextMonth = currentMonth + 1;
                        let nextYear = currentYear;
                        if (nextMonth > 11) {
                            nextMonth = 0;
                            nextYear++;
                        }
                        matchesFilter = eventDate.getMonth() === nextMonth && 
                                       eventDate.getFullYear() === nextYear &&
                                       !isPastEvent;
                    } else if (filterValue === 'past') {
                        matchesFilter = isPastEvent;
                    }
                    
                    // Mostrar/ocultar card com base nos filtros
                    if (matchesSearch && matchesFilter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            searchInput.addEventListener('input', filterEvents);
            filterSelect.addEventListener('change', filterEvents);
            
            // Inicializar filtros
            filterEvents();
        });
    </script>
</body>
</html>