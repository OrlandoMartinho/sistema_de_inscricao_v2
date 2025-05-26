<?php
// Iniciar sessão
session_start();

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ip30set";

// Processar formulário de inscrição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar e sanitizar os dados
    $nome_completo = htmlspecialchars($_POST['nome_completo']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telefone = htmlspecialchars($_POST['telefone']);
    $bi_numero = htmlspecialchars($_POST['bi_numero']);
    $sexo = htmlspecialchars($_POST['sexo']);
    $curso = htmlspecialchars($_POST['curso']);
    
    // Processar uploads de arquivos
    $foto_passe = processarUploadParaBD('foto_passe');
    $documento_bi = processarUploadParaBD('documento_bi');
    $comprovativo = processarUploadParaBD('comprovativo');
    
    // Se todos os uploads foram bem sucedidos
    if ($foto_passe && $documento_bi && $comprovativo) {
        // Conectar ao banco de dados
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Inserir dados no banco de dados
        $stmt = $conn->prepare("INSERT INTO inscricoes (nome_completo, email, telefone, bi_numero, sexo, curso, foto_passe, documento_bi, comprovativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Bind parameters
        $stmt->bind_param("sssssssss", 
            $nome_completo, 
            $email, 
            $telefone, 
            $bi_numero, 
            $sexo, 
            $curso, 
            $foto_passe['conteudo'], 
            $documento_bi['conteudo'], 
            $comprovativo['conteudo']
        );
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Inscrição realizada com sucesso!";
            header("Location: inscricao.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Erro ao registrar inscrição: " . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error_message'] = "Erro no upload de arquivos. Por favor, verifique os arquivos enviados.";
    }
}

// Função para processar upload de arquivos e preparar para o banco de dados
function processarUploadParaBD($field_name) {
    if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == UPLOAD_ERR_OK) {
        // Verificar tamanho do arquivo (limite de 2MB)
        if ($_FILES[$field_name]['size'] > 2097152) {
            return false;
        }
        
        // Validar tipo de arquivo
        $file_type = $_FILES[$field_name]['type'];
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        
        if (!in_array($file_type, $allowed_types)) {
            return false;
        }
        
        // Ler o conteúdo do arquivo
        $file_content = file_get_contents($_FILES[$field_name]['tmp_name']);
        
        return [
            'nome' => $_FILES[$field_name]['name'],
            'tipo' => $file_type,
            'tamanho' => $_FILES[$field_name]['size'],
            'conteudo' => $file_content
        ];
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição - Instituto Politécnico 30 De Setembro</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/icofont.css">
    <link rel="stylesheet" href="css/slicknav.min.css">
    <link rel="stylesheet" href="css/owl-carousel.css">
    <link rel="stylesheet" href="css/datepicker.css">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    
    <style>
        /* Estilos específicos para o formulário de inscrição */
        .registration-form {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
            max-width: 800px;
        }
        
        .registration-form .title {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
        }
        
        .registration-form .form-group {
            margin-bottom: 20px;
        }
        
        .registration-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #34495e;
        }
        
        .registration-form input[type="text"],
        .registration-form input[type="email"],
        .registration-form input[type="tel"],
        .registration-form select,
        .registration-form textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .registration-form input[type="text"]:focus,
        .registration-form input[type="email"]:focus,
        .registration-form input[type="tel"]:focus,
        .registration-form select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        .registration-form .file-input {
            position: relative;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .registration-form .file-input input[type="file"] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
        
        .registration-form .file-input-label {
            display: block;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px dashed #ddd;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .registration-form .file-input-label:hover {
            background: #e9ecef;
        }
        
        .registration-form .file-input-info {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .registration-form .submit-btn {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            width: 100%;
            font-weight: 500;
        }
        
        .registration-form .submit-btn:hover {
            background: #27ae60;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .registration-form {
                padding: 20px;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="header-inner">
            <div class="container">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <!-- Logo -->
                            <div class="logo">
                                <img class="img_logo" width="70" src="img/30 DE SEPTEMBRO.png" alt="">
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
                                        <li><a href="eventos.php">Eventos</a></li>
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

    <!-- Formulário de Inscrição -->
    <div class="container">
        <div class="registration-form">
            <h2 class="title">Formulário de Inscrição</h2>
            
            <!-- Mensagens de sucesso/erro -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error_message']; ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <form action="inscricao.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_completo">Nome completo*</label>
                            <input type="text" id="nome_completo" name="nome_completo" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefone">Telefone*</label>
                            <input type="tel" id="telefone" name="telefone" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bi_numero">Número de identificação (BI)*</label>
                            <input type="text" id="bi_numero" name="bi_numero" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sexo">Sexo*</label>
                            <select id="sexo" name="sexo" required>
                                <option value="">Selecione...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="curso">Curso*</label>
                            <select id="curso" name="curso" required>
                                <option value="">Selecione...</option>
                                <option value="Informática">Informática</option>
                                <option value="Electricidade">Electricidade</option>
                                <option value="Mecânica">Mecânica</option>
                                <option value="Construção Civil">Construção Civil</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Foto (passe)*</label>
                            <div class="file-input">
                                <label class="file-input-label" for="foto_passe">Selecionar arquivo</label>
                                <input type="file" id="foto_passe" name="foto_passe" required accept="image/jpeg,image/png">
                            </div>
                            <div class="file-input-info">Formatos: JPG, PNG (Max: 2MB)</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Documento de identificação (BI)*</label>
                            <div class="file-input">
                                <label class="file-input-label" for="documento_bi">Selecionar arquivo</label>
                                <input type="file" id="documento_bi" name="documento_bi" required accept="image/jpeg,image/png,application/pdf">
                            </div>
                            <div class="file-input-info">Formatos: JPG, PNG, PDF (Max: 2MB)</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Comprovativo de pagamento*</label>
                            <div class="file-input">
                                <label class="file-input-label" for="comprovativo">Selecionar arquivo</label>
                                <input type="file" id="comprovativo" name="comprovativo" required accept="image/jpeg,image/png,application/pdf">
                            </div>
                            <div class="file-input-info">Formatos: JPG, PNG, PDF (Max: 2MB)</div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="submit-btn">Enviar candidatura</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer id="footer" class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer">
                            <h2>Instituto politécnico 30 De Setembro</h2>
                            <p>Lorem ipsum dolor sit am consectetur adipisicing elit do eiusmod tempor incididunt ut labore dolore magna.</p>
                            <ul class="social">
                                <li><a href="#"><i class="icofont-facebook"></i></a></li>
                                <li><a href="#"><i class="icofont-google-plus"></i></a></li>
                                <li><a href="#"><i class="icofont-twitter"></i></a></li>
                                <li><a href="#"><i class="icofont-vimeo"></i></a></li>
                                <li><a href="#"><i class="icofont-pinterest"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer f-link">
                            <h2>Links</h2>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <ul>
                                        <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Inicio</a></li>
                                        <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Eventos</a></li>
                                        <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Cursos</a></li>
                                        <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Contactos</a></li>
                                        <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Ajuda</a></li>	
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer">
                            <h2>Endereço</h2>
                            <p>Estamos localizados no benfica Via expresse</p>
                            <ul class="time-sidual">
                                <li class="day">Telefone:<span>9999999</span></li>
                                <li class="day">Email: <span><a href="">30desetembro@gmail.com</a></span></li>
                                <li class="day">Monday - Thusday <span>9.00-15.00</span></li>
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
                            <p>Instituto politécnico 30 de setembro <a href="https://www.wpthemesgrid.com" target="_blank">30desetembro.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.0.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/easing.js"></script>
    <script src="js/colors.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/jquery.nav.js"></script>
    <script src="js/slicknav.min.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
    <script src="js/niceselect.js"></script>
    <script src="js/tilt.jquery.min.js"></script>
    <script src="js/owl-carousel.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/steller.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    
    <script>
        // Mostrar nome do arquivo selecionado
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = this.files[0]?.name || 'Nenhum arquivo selecionado';
                this.previousElementSibling.textContent = fileName;
            });
        });
        
        // Validação do formulário antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar campos obrigatórios
            document.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            // Validar email
            const email = document.getElementById('email');
            if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                isValid = false;
                email.style.borderColor = 'red';
            } else {
                email.style.borderColor = '#ddd';
            }
            
            // Validar tamanho dos arquivos
            document.querySelectorAll('input[type="file"]').forEach(fileInput => {
                if (fileInput.files.length > 0 && fileInput.files[0].size > 2097152) {
                    isValid = false;
                    fileInput.previousElementSibling.style.borderColor = 'red';
                    alert(`O arquivo ${fileInput.files[0].name} excede o tamanho máximo de 2MB.`);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios corretamente.');
            }
        });
    </script>
</body>
</html>