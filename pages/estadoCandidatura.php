<?php
// Database connection
require_once '../config/connection.php';

// Initialize variables
$search_value = '';
$message = '';
$candidate_data = null;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_verificar'])) {
    $search_value = trim($_POST['verificar']);
    
    if (!empty($search_value)) {
        // Determine if the input is an email or BI number
        $search_by = filter_var($search_value, FILTER_VALIDATE_EMAIL) ? 'email' : 'bi_numero';
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM inscricoes WHERE $search_by = ?");
        $stmt->bind_param("s", $search_value);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $candidate_data = $result->fetch_assoc();
            $message = "Candidatura encontrada! Status: " . ucfirst($candidate_data['status']);
        } else {
            $message = "Nenhuma candidatura encontrada com estes dados.";
        }
        
        $stmt->close();
    } else {
        $message = "Por favor, insira um email ou número de BI válido.";
    }
}

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="pt-BR">
    <head>
        <!-- Meta Tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="keywords" content="Instituto politécnico, 30 de Setembro, candidatura, verificar inscrição">
        <meta name="description" content="Verifique o status da sua candidatura no Instituto politécnico 30 De Setembro">
        <meta name='copyright' content='Instituto politécnico 30 De Setembro'>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <!-- Title -->
        <title>Verificar Candidatura - Instituto politécnico 30 De Setembro</title>
        
        <!-- Favicon -->
        <link rel="icon" href="../img/favicon.png">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="../css/font-awesome.min.css">
        <!-- Medipro CSS -->
        <link rel="stylesheet" href="../style.css">
        
        <style>
            #verificar {
                width: 300px;
                outline: none;
                height: 40px;
                padding: 0 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            #btn_verificar {
                width: 90px;
                height: 40px;
                border: none;
                background-color: rgb(6, 65, 153);
                color: white;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.3s;
            }
            #btn_verificar:hover {
                background-color: rgb(5, 50, 120);
            }
            .result-message {
                margin-top: 20px;
                padding: 15px;
                border-radius: 4px;
                text-align: center;
            }
            .success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            .error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            .info {
                background-color: #d1ecf1;
                color: #0c5460;
                border: 1px solid #bee5eb;
            }
            .candidate-details {
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 5px;
                margin-top: 20px;
            }
            .btn-pdf {
                background-color: #dc3545;
                color: white;
                padding: 10px 15px;
                border-radius: 4px;
                text-decoration: none;
                display: inline-block;
                margin-top: 15px;
                transition: all 0.3s;
            }
            .btn-pdf:hover {
                background-color: #c82333;
                color: white;
            }
            #pdf-template {
                display: none;
            }
            .search-options {
                margin-bottom: 15px;
            }
            .search-options label {
                margin-right: 15px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
    
        <!-- Preloader -->
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
        
        <header class="header">
            <div class="header-inner">
                <div class="container">
                    <div class="inner">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-12">
                                <!-- Logo -->
                                <div class="logo">
                                    <img class="img_logo" width="70" src="../img/30 DE SEPTEMBRO.png" alt="Logo Instituto politécnico 30 De Setembro">
                                </div>
                                <div class="mobile-nav"></div>
                            </div>
                            <div class="col-lg-7 col-md-9 col-12">
                                <!-- Main Menu -->
                                <div class="main-menu">
                                    <nav class="navigation">
                                        <ul class="nav menu">
                                            <li><a href="../index.php">Inicio</a></li>
                                            <li><a href="#">Eventos <i class="icofont-rounded-down"></i></a>
                                                <ul class="dropdown">
                                                    <li><a href="publicacoes.php">Publicações e atualizações</a></li>
                                                    <li><a href="estadoCandidatura.php">Verificar candidatura</a></li>
                                                </ul>
                                            </li>
                                           <li><a href="cursos.php">Cursos</a></li>
                                            <li><a href="contactos.php">Contactos</a></li>
                                            <li><a href="sobre.php">Sobre nós</a></li>
                                            <li><a href="ajuda.php">Ajuda</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-lg-2 col-12">
                                <div class="get-quote">
                                    <a href="formulario_inscricao.php" class="btn">Inscrição</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Verification Section -->
        <section class="error-page section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-12">
                        <!-- Verification Form -->
                        <div class="error-inner">
                            <h1><span>Verificar estado de sua candidatura</span></h1>
                            <p>Digite o e-mail ou número do BI utilizado na sua inscrição para verificar o status da sua candidatura.</p>
                            <br>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <input type="text" name="verificar" id="verificar" placeholder="Digite seu email ou número do BI" value="<?php echo htmlspecialchars($search_value); ?>" required class="form-control" style="max-width: 400px; display: inline-block;">
                                    <input type="submit" name="btn_verificar" id="btn_verificar" value="Verificar" class="btn btn-primary">
                                </div>
                            </form>
                            
                            <?php if (!empty($message)): ?>
                                <div class="result-message <?php 
                                    echo strpos($message, 'encontrada') !== false ? 'success' : 
                                    (strpos($message, 'insira') !== false ? 'info' : 'error'); ?>">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                                
                                <?php if ($candidate_data): ?>
                                    <div class="candidate-details">
                                        <h3>Detalhes da Candidatura</h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Nome Completo:</strong> <?php echo htmlspecialchars($candidate_data['nome_completo']); ?></p>
                                                <p><strong>Número do BI:</strong> <?php echo htmlspecialchars($candidate_data['bi_numero']); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($candidate_data['email']); ?></p>
                                                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($candidate_data['telefone']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Curso:</strong> <?php echo htmlspecialchars($candidate_data['curso']); ?></p>
                                                <p><strong>Status:</strong> <span class="badge badge-<?php echo $candidate_data['status'] == 'aprovado' ? 'success' : ($candidate_data['status'] == 'pendente' ? 'warning' : 'danger'); ?>"><?php echo ucfirst(htmlspecialchars($candidate_data['status'])); ?></span></p>
                                                <p><strong>Data de Inscrição:</strong> <?php echo date('d/m/Y H:i', strtotime($candidate_data['data_inscricao'])); ?></p>
                                            </div>
                                        </div>
                                        
                                        <!-- Botão para gerar PDF -->
                                        <button id="generate-pdf" class="btn-pdf">
                                            <i class="fa fa-download"></i> Baixar Comprovante em PDF
                                        </button>
                                    </div>
                                    
                                    <!-- Template oculto para o PDF -->
                                    <div id="pdf-template" style="padding: 20px; font-family: Arial, sans-serif;">
                                        <div style="text-align: center; margin-bottom: 20px;">
                                            <img src="../img/30 DE SEPTEMBRO.png" width="70" style="margin-bottom: 10px;">
                                            <h1 style="margin: 0; font-size: 22px;">Instituto politécnico 30 De Setembro</h1>
                                            <h2 style="margin: 5px 0 10px; font-size: 18px;">Comprovante de Inscrição</h2>
                                            <p style="font-size: 14px;">Este documento serve como comprovante de inscrição no processo seletivo.</p>
                                        </div>
                                        
                                        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                            <tr>
                                                <th width="30%" style="background-color: #f2f2f2; text-align: left; padding: 8px;">Nome Completo</th>
                                                <td width="70%" style="padding: 8px;"><?php echo htmlspecialchars($candidate_data['nome_completo']); ?></td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f2f2f2; text-align: left; padding: 8px;">Número do BI</th>
                                                <td style="padding: 8px;"><?php echo htmlspecialchars($candidate_data['bi_numero']); ?></td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f2f2f2; text-align: left; padding: 8px;">Email</th>
                                                <td style="padding: 8px;"><?php echo htmlspecialchars($candidate_data['email']); ?></td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f2f2f2; text-align: left; padding: 8px;">Telefone</th>
                                                <td style="padding: 8px;"><?php echo htmlspecialchars($candidate_data['telefone']); ?></td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f2f2f2; text-align: left; padding: 8px;">Curso</th>
                                                <td style="padding: 8px;"><?php echo htmlspecialchars($candidate_data['curso']); ?></td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f2f2f2; text-align: left; padding: 8px;">Status</th>
                                                <td style="padding: 8px;"><?php echo ucfirst(htmlspecialchars($candidate_data['status'])); ?></td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f2f2f2; text-align: left; padding: 8px;">Data de Inscrição</th>
                                                <td style="padding: 8px;"><?php echo date('d/m/Y H:i', strtotime($candidate_data['data_inscricao'])); ?></td>
                                            </tr>
                                        </table>
                                        
                                        <div style="text-align: center; font-size: 10px; margin-top: 30px;">
                                            Documento gerado automaticamente em <?php echo date('d/m/Y H:i'); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>    

        <footer id="footer" class="footer">
            <!-- Footer Top -->
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer">
                                <h2>Instituto politécnico 30 De Setembro</h2>
                                <p>Educação de qualidade para formar os profissionais do futuro.</p>
                                <!-- Social -->
                                <ul class="social">
                                    <li><a href="#"><i class="icofont-facebook"></i></a></li>
                                    <li><a href="#"><i class="icofont-instagram"></i></a></li>
                                    <li><a href="#"><i class="icofont-twitter"></i></a></li>
                                    <li><a href="#"><i class="icofont-youtube"></i></a></li>
                                </ul>
                                <!-- End Social -->
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer f-link">
                                <h2>Links</h2>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <ul>
                                            <li><a href="index.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Inicio</a></li>
                                            <li><a href="publicacoes.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Eventos</a></li>
                                            <li><a href="cursos.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Cursos</a></li>
                                            <li><a href="contactos.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Contactos</a></li>
                                            <li><a href="ajuda.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Ajuda</a></li>    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer">
                                <h2>Endereço</h2>
                                <p>Estamos localizados no Benfica, Via expressa</p>
                                <ul class="time-sidual">
                                    <li class="day">Telefone:<span>+244 999 999 999</span></li>
                                    <li class="day">Email: <span><a href="mailto:30desetembro@gmail.com">30desetembro@gmail.com</a></span></li>
                                    <li class="day">Segunda - Sexta <span>8:00-16:00</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ End Footer Top -->
            <!-- Copyright -->
            <div class="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <div class="copyright-content">
                                <p>&copy; <?php echo date('Y'); ?> Instituto Politécnico 30 de Setembro. Todos os direitos reservados.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
       
        <!-- jQuery JS -->
        <script src="../js/jquery.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="../js/bootstrap.min.js"></script>
        <!-- jsPDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        
        <!-- Main JS -->
        <script src="../js/main.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const generatePdfBtn = document.getElementById('generate-pdf');
                
                if (generatePdfBtn) {
                    generatePdfBtn.addEventListener('click', function() {
                        // Criar um novo PDF
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        
                        // Adicionar conteúdo ao PDF manualmente
                        pdf.setFontSize(16);
                        pdf.setTextColor(40);
                        pdf.text('Instituto politécnico 30 De Setembro', 105, 20, { align: 'center' });
                        
                        pdf.setFontSize(14);
                        pdf.text('Comprovante de Inscrição', 105, 30, { align: 'center' });
                        
                        pdf.setFontSize(10);
                        pdf.text('Este documento serve como comprovante de inscrição no processo seletivo.', 105, 38, { align: 'center' });
                        
                        // Linha divisória
                        pdf.line(20, 42, 190, 42);
                        
                        // Dados da candidatura
                        pdf.setFontSize(12);
                        let yPosition = 50;
                        
                        // Função para adicionar campo
                        const addField = (label, value) => {
                            pdf.text(`${label}:`, 20, yPosition);
                            pdf.text(value, 70, yPosition);
                            yPosition += 8;
                        };
                        
                        // Adicionar campos dinamicamente
                        addField('Nome Completo', '<?php echo htmlspecialchars($candidate_data["nome_completo"] ?? ""); ?>');
                        addField('Número do BI', '<?php echo htmlspecialchars($candidate_data["bi_numero"] ?? ""); ?>');
                        addField('Email', '<?php echo htmlspecialchars($candidate_data["email"] ?? ""); ?>');
                        addField('Telefone', '<?php echo htmlspecialchars($candidate_data["telefone"] ?? ""); ?>');
                        addField('Curso', '<?php echo htmlspecialchars($candidate_data["curso"] ?? ""); ?>');
                        addField('Status', '<?php echo ucfirst(htmlspecialchars($candidate_data["status"] ?? "")); ?>');
                        addField('Data de Inscrição', '<?php echo isset($candidate_data["data_inscricao"]) ? date("d/m/Y H:i", strtotime($candidate_data["data_inscricao"])) : ""; ?>');
                        
                        // Rodapé
                        pdf.setFontSize(8);
                        pdf.text(`Documento gerado automaticamente em ${new Date().toLocaleString()}`, 105, 280, { align: 'center' });
                        
                        // Gerar nome do arquivo
                        const filename = 'comprovante_inscricao_' + 
                            '<?php echo isset($candidate_data) ? preg_replace("/[^a-zA-Z0-9]+/", "_", $candidate_data["nome_completo"]) : "candidato"; ?>' + 
                            '.pdf';
                        
                        // Salvar o PDF
                        pdf.save(filename);
                    });
                }
            });
        </script>
    </body>
</html>