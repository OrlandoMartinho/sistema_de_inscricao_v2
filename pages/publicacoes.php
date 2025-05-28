<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instituto politécnico 30 De Setembro - Eventos</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .title {
            text-align: center;
            position: absolute;
            top: 412px;
        }
        
        .events-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        
        .events-table th, .events-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .events-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .events-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .event-image {
            max-width: 100px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
        }
        
        .no-events {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .events-table {
                display: block;
                overflow-x: auto;
            }
            
            .title {
                top: 350px;
            }
        }
    </style>
</head>

<body>
    <!--inicio do header-->
    <header style="background-image: url('../img/img2.jpg');">
        <div class="container">
            <nav>
                <!--logo-->
                <a href=""></a>
                <!--end logo-->
                <ul>
                    <a href="../index.php">Home</a>
                    <a href="admin/login.php">Login</a>
                    <a href="#">Eventos</a>
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
                    <h1>Instituto politécnico 30 De Setembro</h1>
                   <h1 class="title">Eventos</h1>
                </div>
            </section>
        </div>
    </header>

    <div class="events-container">
        <?php
        include('../config/connection.php');
        
        $query = "SELECT * FROM eventos WHERE status = 'ativo' ORDER BY data_evento DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $eventos = [];
        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }
        ?>
        
        <?php if(empty($eventos)): ?>
            <div class="no-events">
                <p>No momento não temos nenhum evento agendado. Por favor, verifique novamente mais tarde.</p>
            </div>
        <?php else: ?>
            <table class="events-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Local</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($eventos as $evento): ?>
                        <tr>
                            <td>
                                <?php if(!empty($evento['foto'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($evento['foto']); ?>" class="event-image" alt="Imagem do evento">
                                <?php else: ?>
                                    <span>Sem imagem</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($evento['data_evento'])); ?></td>
                            <td><?php echo htmlspecialchars($evento['local']); ?></td>
                            <td><?php echo htmlspecialchars(substr($evento['descricao'], 0, 50) ). '...'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <footer style="background-color: #333; color: white; text-align: center; padding: 20px 0; margin-top: 50px;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Instituto Politécnico 30 De Setembro. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../js/main.js"></script>
</body>
</html>