<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die("Acesso não autorizado");
}

include '../config/connection.php';

$id = $_GET['id'];
$sql = "SELECT foto_passe, documento_bi, comprovativo FROM inscricoes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo '<a href="download_document.php?type=foto_passe&id='.$id.'" class="document-link">Foto de Passe</a>';
    echo '<a href="download_document.php?type=documento_bi&id='.$id.'" class="document-link">Documento de Identificação</a>';
    echo '<a href="download_document.php?type=comprovativo&id='.$id.'" class="document-link">Comprovativo de Pagamento</a>';
}

$stmt->close();
$conn->close();
?>