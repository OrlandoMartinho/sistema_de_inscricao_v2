<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Tipos de documentos permitidos
$allowed_types = ['foto_passe', 'documento_bi', 'comprovativo'];
if (!in_array($type, $allowed_types)) {
    die('Tipo de documento inválido');
}

// Mapear tipos MIME para extensões de arquivo
$mime_to_extension = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'application/pdf' => 'pdf'
];

// Buscar o documento e o tipo MIME no banco de dados
$query = "SELECT $type, {$type}_type FROM inscricoes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($document, $mime_type);
    $stmt->fetch();

    if (empty($document)) {
        die('Documento não encontrado ou vazio');
    }

    // Determinar a extensão do arquivo com base no tipo MIME
    $extension = isset($mime_to_extension[$mime_type]) ? $mime_to_extension[$mime_type] : 'bin';
    
    // Definir headers apropriados
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $type . '_' . $id . '.' . $extension . '"');
    header('Content-Length: ' . strlen($document));
    
    echo $document;
} else {
    die('Documento não encontrado');
}

$stmt->close();
$conn->close();
?>