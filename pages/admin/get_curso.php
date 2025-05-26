<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ip30set";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("HTTP/1.1 500 Internal Server Error");
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $curso = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($curso);
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Curso não encontrado']);
}

$stmt->close();
$conn->close();
?>