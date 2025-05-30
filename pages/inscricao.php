<?php
session_start();
include '../config/connection.php';

// Buscar cursos ativos do banco de dados
$cursos_ativos = [];
$conn = new mysqli($servername, $username, $password, $dbname);
if (!$conn->connect_error) {
    $result = $conn->query("SELECT id, nome FROM cursos WHERE status = 'ativo' ORDER BY nome");
    while ($row = $result->fetch_assoc()) {
        $cursos_ativos[] = $row;
    }
    $conn->close();
}

// Processar formulário de inscrição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erro de segurança. Por favor, envie o formulário novamente.";
        header("Location: inscricao.php");
        exit();
    }

    // Validar e sanitizar os dados
    $nome_completo = htmlspecialchars(trim($_POST['nome_completo']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);
    $bi_numero = htmlspecialchars(trim($_POST['bi_numero']));
    $sexo = in_array($_POST['sexo'], ['Masculino', 'Feminino']) ? $_POST['sexo'] : null;
    $curso_id = (int)$_POST['curso_id'];
    $curso_nome = null;
    $data_de_nascimento = htmlspecialchars(trim($_POST['data_de_nascimento']));
    
    // Verificar dados obrigatórios
    if (empty($nome_completo) || empty($email) || empty($telefone) || empty($bi_numero) || !$sexo || !$curso_id || empty($data_de_nascimento)) {
        $_SESSION['error_message'] = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: inscricao.php");
        exit();
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Por favor, insira um email válido.";
        header("Location: inscricao.php");
        exit();
    }
    
    // Verificar se o curso selecionado existe e está ativo
    $curso_valido = false;
    foreach ($cursos_ativos as $curso) {
        if ($curso['id'] == $curso_id) {
            $curso_valido = true;
            $curso_nome = $curso['nome'];
            break;
        }
    }
    
    if (!$curso_valido) {
        $_SESSION['error_message'] = "Por favor, selecione um curso válido.";
        header("Location: inscricao.php");
        exit();
    }
    
    // Processar uploads de arquivos
    $uploads = [
        'foto_passe' => processarUpload('foto_passe', ['image/jpeg', 'image/png']),
        'documento_bi' => processarUpload('documento_bi', ['image/jpeg', 'image/png', 'application/pdf']),
        'comprovativo' => processarUpload('comprovativo', ['image/jpeg', 'image/png', 'application/pdf'])
    ];
    
    // Verificar se todos os uploads foram bem sucedidos
    $upload_errors = [];
    foreach ($uploads as $key => $upload) {
        if (!$upload) {
            $upload_errors[] = "Erro no arquivo " . str_replace('_', ' ', $key);
        }
    }
    
    if (!empty($upload_errors)) {
        $_SESSION['error_message'] = implode(', ', $upload_errors) . ". Por favor, verifique os arquivos (formatos permitidos: JPG, PNG, PDF, tamanho máximo 2MB).";
        header("Location: inscricao.php");
        exit();
    }
    
    // Inserir no banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        $_SESSION['error_message'] = "Erro de conexão com o banco de dados. Por favor, tente novamente mais tarde.";
        header("Location: inscricao.php");
        exit();
    }
    
    // Verificar se já existe inscrição com o mesmo BI ou email
    $stmt_check = $conn->prepare("SELECT id FROM inscricoes WHERE bi_numero = ? OR email = ?");
    $stmt_check->bind_param("ss", $bi_numero, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Já existe uma inscrição com este número de BI ou email.";
        $stmt_check->close();
        $conn->close();
        header("Location: inscricao.php");
        exit();
    }
    $stmt_check->close();
    $stmt = $conn->prepare("INSERT INTO inscricoes 
    (nome_completo, email, telefone, bi_numero, curso, sexo, curso_id, foto_passe, documento_bi, comprovativo, data_de_nascimento) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Usar tipos: s = string, i = integer
// Como os campos de arquivos são NULL, use "s" e passe `null` diretamente
$stmt->bind_param("ssssssissss", 
    $nome_completo, 
    $email, 
    $telefone, 
    $bi_numero, 
    $curso_nome,
    $sexo, 
    $curso_id,
    $foto_passe,     // null ou string base64 se estiver usando
    $documento_bi,   // idem
    $comprovativo ,
    $data_de_nascimento   // idem
);

    // Bind os parâmetros blob separadamente
    $stmt->send_long_data(6, $uploads['foto_passe']['conteudo']);
    $stmt->send_long_data(7, $uploads['documento_bi']['conteudo']);
    $stmt->send_long_data(8, $uploads['comprovativo']['conteudo']);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Inscrição realizada com sucesso! Você receberá um email de confirmação.";
        
        // Aqui você pode adicionar o envio de email de confirmação
        // enviarEmailConfirmacao($email, $nome_completo);
    } else {
        $_SESSION['error_message'] = "Erro ao registrar inscrição: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
    header("Location: inscricao.php");
    exit();
}

// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Função para processar upload de arquivos
function processarUpload($field_name, $allowed_types) {
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] != UPLOAD_ERR_OK) {
        return false;
    }
    
    // Verificar tamanho do arquivo (limite de 2MB)
    if ($_FILES[$field_name]['size'] > 2097152) {
        return false;
    }
    
    // Validar tipo de arquivo
    $file_type = $_FILES[$field_name]['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        return false;
    }
    
    // Verificar se é realmente um arquivo do tipo especificado
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES[$field_name]['tmp_name']);
    finfo_close($file_info);
    
    if (!in_array($mime_type, $allowed_types)) {
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição - Instituto Politécnico 30 De Setembro</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/icofont.css">
    <link rel="stylesheet" href="../css/slicknav.min.css">
    <link rel="stylesheet" href="../css/owl-carousel.css">
    <link rel="stylesheet" href="../css/datepicker.css">
    <link rel="stylesheet" href="../css/animate.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/responsive.css">
    
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
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .has-error {
            border-color: #ff0000 !important;
        }
        
        .error-text {
            color: #ff0000;
            font-size: 12px;
            margin-top: 5px;
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
                                <img class="img_logo" width="70" src="../img/30 DE SEPTEMBRO.png" alt="">
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
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <form id="formInscricao" action="inscricao.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_completo">Nome completo*</label>
                            <input type="text" id="nome_completo" name="nome_completo" required>
                            <div class="error-text" id="nome_completo_error"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" required>
                            <div class="error-text" id="email_error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefone">Telefone*</label>
                            <input type="tel" id="telefone" name="telefone" required>
                            <div class="error-text" id="telefone_error"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data_de_nascimento">Data de Nascimento*</label>
                            <input type="date" id="data_de_nascimento" name="data_de_nascimento" required>
                            <div class="error-text" id="data_de_nascimento_error"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bi_numero">Número de identificação (BI)*</label>
                            <input type="text" id="bi_numero" name="bi_numero" required>
                            <div class="error-text" id="bi_numero_error"></div>
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
                            <div class="error-text" id="sexo_error"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="curso_id">Curso*</label>
                            <select id="curso_id" name="curso_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($cursos_ativos as $curso): ?>
                                    <option value="<?php echo $curso['id']; ?>">
                                        <?php echo htmlspecialchars($curso['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-text" id="curso_id_error"></div>
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
                            <div class="error-text" id="foto_passe_error"></div>
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
                            <div class="error-text" id="documento_bi_error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Comprovativo de pagamento*</label>
                          
                            <input type="text" id="rup" name="rup" value="" readonly>  
                            <div class="file-input">
                                <label class="file-input-label" for="comprovativo">Selecionar arquivo</label>
                                <input type="file" id="comprovativo" name="comprovativo" required accept="image/jpeg,image/png,application/pdf">
                            </div>
                            <div class="file-input-info">Formatos: JPG, PNG, PDF (Max: 2MB)</div>
                            <div class="error-text" id="comprovativo_error"></div>
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
                            <p>Educação de qualidade para formar os profissionais do futuro.</p>
                            <ul class="social">
                                <li><a href="#"><i class="icofont-facebook"></i></a></li>
                                <li><a href="#"><i class="icofont-instagram"></i></a></li>
                                <li><a href="#"><i class="icofont-twitter"></i></a></li>
                                <li><a href="#"><i class="icofont-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="single-footer f-link">
                            <h2>Links</h2>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <ul>
                                        <li><a href="../index.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Inicio</a></li>
                                        <li><a href="eventos.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Eventos</a></li>
                                        <li><a href="cursos.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Cursos</a></li>
                                        <li><a href="contactos.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Contactos</a></li>
                                        <li><a href="sobre.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Sobre nós</a></li>	
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
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // Mostrar nome do arquivo selecionado
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = this.files[0]?.name || 'Nenhum arquivo selecionado';
                this.previousElementSibling.textContent = fileName;
            });
        });
        
        // Validação do formulário antes de enviar
        document.getElementById('formInscricao').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Limpar erros anteriores
            document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
            document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            
            // Validar campos obrigatórios
            document.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('has-error');
                    document.getElementById(field.id + '_error').textContent = 'Este campo é obrigatório';
                }
            });
            
            // Validar email
            const email = document.getElementById('email');
            if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                isValid = false;
                email.classList.add('has-error');
                document.getElementById('email_error').textContent = 'Por favor, insira um email válido';
            }
            
            // Validar telefone (pelo menos 9 dígitos)
            const telefone = document.getElementById('telefone');
            const telefoneNumeros = telefone.value.replace(/\D/g, '');
            if (telefoneNumeros.length < 9) {
                isValid = false;
                telefone.classList.add('has-error');
                document.getElementById('telefone_error').textContent = 'Por favor, insira um telefone válido';
            }
            
            // Validar tamanho dos arquivos
            document.querySelectorAll('input[type="file"]').forEach(fileInput => {
                if (fileInput.files.length > 0) {
                    if (fileInput.files[0].size > 2097152) {
                        isValid = false;
                        fileInput.classList.add('has-error');
                        document.getElementById(fileInput.id + '_error').textContent = 'O arquivo excede o tamanho máximo de 2MB';
                    }
                    
                    // Validar tipo de arquivo
                    const allowedTypes = fileInput.accept.split(',');
                    const fileType = fileInput.files[0].type;
                    if (!allowedTypes.some(type => type.trim() === fileType)) {
                        isValid = false;
                        fileInput.classList.add('has-error');
                        document.getElementById(fileInput.id + '_error').textContent = 'Tipo de arquivo não permitido';
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Rolar até o primeiro erro
                const firstError = document.querySelector('.has-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                // Mostrar loading no botão de submit
                const submitBtn = document.querySelector('.submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';
            }
        });
        
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        function gerarRUP() {
            let rupNumeros = '';
            for (let i = 0; i < 13; i++) {
                rupNumeros += Math.floor(Math.random() * 10); // Gera um dígito aleatório de 0 a 9
            }

            // Formata o RUP: xxxx.xxxx.xxxx.x
            let rupFormatado = '';
            for (let i = 0; i < rupNumeros.length; i++) {
                rupFormatado += rupNumeros[i];
                if ((i + 1) % 4 === 0 && i !== 12) {
                    rupFormatado += '.';
                }
            }

            const inputRup = document.getElementById('rup');
            if (inputRup) {
                inputRup.value ="RUP:"+ rupFormatado; // Define o valor do campo RUP formatado
            } else {
                console.error("Campo de RUP não encontrado no DOM.");
            }

            return rupFormatado;
        }


        // Exemplo de uso
        console.log("RUP gerado:", gerarRUP());


    </script>
</body>
</html>