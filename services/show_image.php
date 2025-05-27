<?php
include('../../config/connection.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT foto FROM eventos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($foto);
    $stmt->fetch();
    $stmt->close();
    
    if (!empty($foto)) {
        header("Content-Type: image/jpeg"); // ou o tipo apropriado
        echo $foto;
        exit;
    }
}

// Se não encontrar a imagem, retorna uma imagem padrão
header("Location: ../../img/default-event.jpg");
exit;
?>