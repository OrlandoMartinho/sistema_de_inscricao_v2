<?php


// Processar filtros
$curso_filter = isset($_GET['curso']) ? $_GET['curso'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Construir query com filtros
$sql = "SELECT id, nome_completo, email, telefone, bi_numero, sexo, curso, data_inscricao, status FROM inscricoes WHERE 1=1";

if (!empty($curso_filter)) {
    $sql .= " AND curso = '" . $conn->real_escape_string($curso_filter) . "'";
}

if (!empty($status_filter)) {
    $sql .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}

$sql .= " ORDER BY data_inscricao DESC";

$result = $conn->query($sql);

// Processar ação de atualização de status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];
    
    $stmt = $conn->prepare("UPDATE inscricoes SET status = ?, observacoes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $observacoes, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Status da inscrição atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar status: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: admin-inscricoes.php");
    exit();
}

// Processar exportação de dados
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inscricoes_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Cabeçalho do CSV
    fputcsv($output, array('ID', 'Nome', 'Email', 'Telefone', 'BI', 'Sexo', 'Curso', 'Data Inscrição', 'Status'));
    
    // Dados
    $export_result = $conn->query($sql);
    while ($row = $export_result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}




?>