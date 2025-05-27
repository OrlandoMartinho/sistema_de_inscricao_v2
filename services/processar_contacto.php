<?php
// Inicia a sessão para mensagens flash
session_start();

// Inclui a configuração do banco de dados
include('../../config/connection.php');

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Valida e sanitiza os dados
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_STRING);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

    // Validação adicional
    if (empty($nome) || empty($email) || empty($assunto) || empty($mensagem)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Por favor, preencha todos os campos obrigatórios.'
        ];
        header('Location: contactos.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Por favor, insira um endereço de email válido.'
        ];
        header('Location: contactos.php');
        exit();
    }

    try {
        // Prepara a query SQL
        $sql = "INSERT INTO contactos (nome, email, assunto, mensagem, status) 
                VALUES (:nome, :email, :assunto, :mensagem, 'não lido')";
        
        $stmt = $pdo->prepare($sql);
        
        // Executa a query com os parâmetros
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':assunto' => $assunto,
            ':mensagem' => $mensagem
        ]);

        // Mensagem de sucesso
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Mensagem enviada com sucesso! Entraremos em contacto em breve.'
        ];

        // Redireciona de volta para a página de contactos
        header('Location: contactos.php');
        exit();

    } catch (PDOException $e) {
        // Log do erro
        error_log("Erro ao salvar mensagem de contacto: " . $e->getMessage());

        // Mensagem de erro
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Ocorreu um erro ao enviar a mensagem. Por favor, tente novamente mais tarde.'
        ];

        // Redireciona de volta para a página de contactos
        header('Location: contactos.php');
        exit();
    }
} else {
    // Se alguém tentar acessar diretamente este arquivo sem enviar o formulário
    header('Location: contactos.php');
    exit();
}
?>