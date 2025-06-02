<?php

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include('../config/connection.php');


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Tipos de documentos permitidos
$allowed_types = ['foto_passe', 'documento_bi', 'comprovativo'];
if (!in_array($type, $allowed_types)) {
    die('Tipo de documento inválido');
}

// Buscar o documento no banco de dados
$query = "SELECT $type FROM inscricoes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($document);
    $stmt->fetch();
    
    // Definir headers apropriados
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $type . '_' . $id . '.pdf"');
    echo $document;
} else {
    die('Documento não encontrado');
}

$stmt->close();
$conn->close();
?>