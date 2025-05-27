<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

include('../../config/connection.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Tipos de documentos permitidos
$allowed_types = ['foto_passe', 'documento_bi', 'comprovativo'];

if ($id <= 0 || !in_array($type, $allowed_types)) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// Buscar o documento no banco de dados
$sql = "SELECT $type, nome_completo FROM inscricoes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

$row = $result->fetch_assoc();
$document_data = $row[$type];
$nome = $row['nome_completo'];

if (empty($document_data)) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// Determinar o tipo de conteúdo e nome do arquivo
switch ($type) {
    case 'foto_passe':
        $content_type = 'image/jpeg';
        $filename = 'foto_passe_' . $nome . '.jpg';
        break;
    case 'documento_bi':
        $content_type = 'application/pdf';
        $filename = 'documento_bi_' . $nome . '.pdf';
        break;
    case 'comprovativo':
        $content_type = 'application/pdf';
        $filename = 'comprovativo_' . $nome . '.pdf';
        break;
    default:
        $content_type = 'application/octet-stream';
        $filename = 'documento_' . $nome;
}

// Enviar o documento para download
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($document_data));
echo $document_data;
exit;
?>