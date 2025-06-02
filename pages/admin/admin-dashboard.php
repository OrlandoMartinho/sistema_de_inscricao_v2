<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');
include '../../services/dashboard-services.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="../css1/admin/home.css">
    <link rel="stylesheet" href="../css1/dashboard-styles.css">
       <!-- Favicon -->
    <link rel="icon" href="../../img/30 DE SEPTEMBRO.png">

</head>
<body>
    <header style="background-image: url('../../img/ac.jpg'); height: 150px;">
        <div class="container">
            <nav>
                <a href="../index.php" class="logo">Instituto politécnico</a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Painel de Administração</h3>
            <div class="admin-menu">
                <a href="admin-dashboard.php" class="active"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.php"><i>📅</i> Eventos</a>
                <a href="admin-cursos.php"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php"><i>📝</i> Inscrições</a>
                <a href="admin-config.php"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Dashboard</h2>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Sair</button>
            </div>
            
            <div class="dashboard-cards">
                <div class="card">
                    <h4>Novos Eventos</h4>
                    <div class="number"><?php echo $stats['eventos']; ?></div>
                    <a href="admin-eventos.php">Ver todos</a>
                </div>
                
                <div class="card">
                    <h4>Novos Contactos</h4>
                    <div class="number"><?php echo $stats['contactos']; ?></div>
                    <a href="admin-contactos.php">Ver todos</a>
                </div>
                
                <div class="card">
                    <h4>Novas Inscrições</h4>
                    <div class="number"><?php echo $stats['inscricoes']; ?></div>
                    <a href="admin-inscricoes.php">Ver todos</a>
                </div>
                
               
            </div>
            
        </div>
    </div>
</body>
</html>