<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die("Acesso não autorizado");
}

include '../config/connection.php';

$id = $_GET['id'];
$type = $_GET['type'];

$allowed_types = ['foto_passe', 'documento_bi', 'comprovativo'];
if (!in_array($type, $allowed_types)) {
    die("Tipo de documento inválido");
}

$sql = "SELECT $type FROM inscricoes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($document);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    header("Content-Type: application/octet-stream");
    
    // Determinar a extensão do arquivo com base no tipo MIME (simplificado)
    $extension = 'bin';
    if (strpos($document, '%PDF') === 0) {
        $extension = 'pdf';
    } elseif (strpos($document, "\xFF\xD8\xFF") === 0) {
        $extension = 'jpg';
    } elseif (strpos($document, "\x89PNG") === 0) {
        $extension = 'png';
    }
    
    header("Content-Disposition: attachment; filename=" . $type . "_" . $id . "." . $extension);
    echo $document;
} else {
    echo "Documento não encontrado";
}

$stmt->close();
$conn->close();
?>