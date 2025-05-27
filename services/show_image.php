<?php
include('../../config/connection.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT foto FROM eventos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($foto);
    $stmt->fetch();
    
    header("Content-Type: image/jpeg"); // Ajuste para o tipo correto da imagem
    echo $foto;
} else {
    // Pode exibir uma imagem padrão ou mensagem de erro
    header("Content-Type: image/png");
    readfile('path/to/default-image.png');
}

$stmt->close();
$conn->close();
?>