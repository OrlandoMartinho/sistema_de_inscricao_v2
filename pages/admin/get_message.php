<?php
// Certifique-se de que o cabeçalho Content-Type é JSON em todos os casos
header('Content-Type: application/json');
include('../../config/connection.php');
// Iniciar log de depuração
error_log("Iniciando script de busca de contato");

try {
    // Verifique se a conexão $conn existe e é válida
    error_log("Verificando conexão com o banco de dados");
    if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
        error_log("Erro na conexão: " . ($conn->connect_error ?? "Conexão não definida"));
        throw new Exception("Erro na conexão com o banco de dados");
    }
    error_log("Conexão com o banco de dados verificada com sucesso");

    // Obter e validar ID
    error_log("Obtendo parâmetro ID");
    $id = $_GET['id'] ?? 0;
    error_log("ID recebido: " . $id);
    
    // Valide o ID como inteiro
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        error_log("ID inválido (não é inteiro): " . $id);
        throw new Exception("ID inválido: deve ser um número inteiro");
    }
    
    if ($id <= 0) {
        error_log("ID inválido (menor ou igual a zero): " . $id);
        throw new Exception("ID inválido: deve ser maior que zero");
    }
    error_log("ID validado com sucesso: " . $id);

    // Preparar consulta SQL
    error_log("Preparando consulta SQL");
    $sql = "SELECT * FROM contactos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Erro ao preparar consulta: " . $conn->error);
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }
    error_log("Consulta preparada com sucesso: " . $sql);

    // Bind parameters
    error_log("Fazendo bind do parâmetro ID: " . $id);
    if (!$stmt->bind_param("i", $id)) {
        error_log("Erro no bind_param: " . $stmt->error);
        throw new Exception("Erro ao vincular parâmetros: " . $stmt->error);
    }

    // Executar consulta
    error_log("Executando consulta");
    if (!$stmt->execute()) {
        error_log("Erro na execução: " . $stmt->error);
        throw new Exception("Erro na execução da consulta: " . $stmt->error);
    }
    error_log("Consulta executada com sucesso");

    // Obter resultados
    error_log("Obtendo resultados");
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        error_log("Registro encontrado");
        $message = $result->fetch_assoc();
        error_log("Dados do registro: " . print_r($message, true));
        echo json_encode($message);
    } else {
        error_log("Nenhum registro encontrado para ID: " . $id);
        http_response_code(404);
        echo json_encode(['error' => 'Mensagem não encontrada']);
    }

    // Fechar conexões
    error_log("Fechando statement e conexão");
    $stmt->close();
    $conn->close();
    error_log("Operação concluída com sucesso");
    
} catch (Exception $e) {
    error_log("Erro capturado: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ]
    ]);
}
?>