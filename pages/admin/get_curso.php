<?php
// Inicia a sessão e configura o cabeçalho JSON
session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401); // Não autorizado
    die(json_encode(['error' => 'Acesso não autorizado']));
}

// Inclui o arquivo de conexão com o banco de dados
require_once('../../config/connection.php');

// Verifica se a conexão com o banco foi estabelecida
if (!$conn) {
    http_response_code(500);
    die(json_encode(['error' => 'Falha na conexão com o banco de dados']));
}

// Valida o parâmetro ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Bad Request
    die(json_encode(['error' => 'ID inválido ou não fornecido']));
}

$id = (int)$_GET['id'];

// Verifica se o ID é válido (maior que 0)
if ($id <= 0) {
    http_response_code(400);
    die(json_encode(['error' => 'ID deve ser um número positivo']));
}

try {
    // Prepara e executa a consulta
    $stmt = $conn->prepare("SELECT id, nome, descricao, duracao, nivel, instrutor, status FROM cursos WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Falha ao preparar a consulta");
    }
    
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception("Falha ao executar a consulta");
    }
    
    $result = $stmt->get_result();
    
    // Verifica se encontrou resultados
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        die(json_encode(['error' => 'Curso não encontrado']));
    }
    
    // Obtém os dados do curso
    $curso = $result->fetch_assoc();
    
    // Fecha a declaração e a conexão
    $stmt->close();
    $conn->close();
    
    // Retorna os dados em formato JSON
    echo json_encode($curso);
    
} catch (Exception $e) {
    // Fecha a conexão em caso de erro
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    
    http_response_code(500); // Internal Server Error
    die(json_encode([
        'error' => 'Erro no servidor',
        'details' => $e->getMessage() // Em desenvolvimento, pode ser útil
    ]));
}
?>