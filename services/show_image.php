<?php
include('../../config/connection.php');

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.0 400 Bad Request");
    exit;
}

$id = (int)$_GET['id'];

// Buscar a imagem no banco de dados
$stmt = $conn->prepare("SELECT foto, LENGTH(foto) as size FROM eventos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Se não encontrou, mostrar imagem padrão
    $defaultImage = '../../img/default-event.jpg';
    header("Content-Type: image/jpeg");
    header("Content-Length: " . filesize($defaultImage));
    readfile($defaultImage);
    exit;
}

$stmt->bind_result($foto, $size);
$stmt->fetch();
$stmt->close();

if (empty($foto)) {
    // Imagem vazia no banco
    $defaultImage = '../../img/default-event.jpg';
    header("Content-Type: image/jpeg");
    header("Content-Length: " . filesize($defaultImage));
    readfile($defaultImage);
    exit;
}

// Determinar o tipo de conteúdo com base nos primeiros bytes
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($foto);

// Enviar cabeçalhos e imagem
header("Content-Type: " . $mime);
header("Content-Length: " . $size);
header("Cache-Control: public, max-age=604800");
echo $foto;
exit;
?>