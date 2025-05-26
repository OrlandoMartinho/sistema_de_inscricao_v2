<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

include 'connection.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM contactos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $message = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($message);
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Mensagem não encontrada']);
}

$stmt->close();
$conn->close();
?>