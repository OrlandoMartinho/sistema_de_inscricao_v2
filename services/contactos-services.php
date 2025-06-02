<?php

// Processar ações (excluir, marcar como lido, etc.)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $id = $_POST['id'] ?? 0;
        
        switch ($_POST['action']) {
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM contactos WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Mensagem excluída com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao excluir mensagem.";
                }
                $stmt->close();
                break;
                
            case 'mark_read':
                $stmt = $conn->prepare("UPDATE contactos SET status = 'lido' WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'send_reply':
                $to = $_POST['email'];
                $subject = $_POST['subject'];
                $message = $_POST['message'];
                $headers = "From: admin@ip30set.com" . "\r\n" .
                           "Reply-To: admin@ip30set.com" . "\r\n" .
                           "X-Mailer: PHP/" . phpversion();
                
                if (mail($to, $subject, $message, $headers)) {
                    $_SESSION['success_message'] = "Resposta enviada com sucesso!";
                    $stmt = $conn->prepare("UPDATE contactos SET status = 'respondido' WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $_SESSION['error_message'] = "Erro ao enviar resposta por email.";
                }
                break;
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// Buscar mensagens
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$stmt = $conn->prepare("SELECT * FROM contactos WHERE nome LIKE ? OR email LIKE ? OR assunto LIKE ? ORDER BY data_envio DESC");
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


?>