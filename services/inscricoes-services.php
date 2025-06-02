<?php
function getInscricoes($conn, $curso_filter = null, $status_filter = null) {
    $sql = "SELECT i.id, i.nome_completo, i.email, i.telefone, i.bi_numero, i.sexo, 
                   i.data_inscricao, i.status, i.observacoes, c.nome as curso_nome
            FROM inscricoes i
            JOIN cursos c ON i.curso_id = c.id
            WHERE 1=1";

    $params = [];
    $types = '';

    if ($curso_filter) {
        $sql .= " AND i.curso_id = ?";
        $params[] = $curso_filter;
        $types .= 'i';
    }

    if ($status_filter) {
        $sql .= " AND i.status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }

    $sql .= " ORDER BY i.data_inscricao DESC";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function updateInscricaoStatus($conn, $id, $status, $observacoes) {
    $sql = "UPDATE inscricoes SET status = ?, observacoes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $observacoes, $id);
    return $stmt->execute();
}

function exportInscricoesToCSV($conn, $curso_filter = null, $status_filter = null) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inscricoes_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Cabeçalho do CSV
    fputcsv($output, [
        'ID', 'Nome', 'Email', 'Telefone', 'BI', 'Sexo', 
        'Curso', 'Data Inscrição', 'Status', 'Observações'
    ]);
    
    // Obter dados
    $result = getInscricoes($conn, $curso_filter, $status_filter);
    
    // Escrever dados
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['nome_completo'],
            $row['email'],
            $row['telefone'],
            $row['bi_numero'],
            $row['sexo'],
            $row['curso_nome'],
            date('d/m/Y H:i', strtotime($row['data_inscricao'])),
            $row['status'],
            $row['observacoes']
        ]);
    }
    
    fclose($output);
    exit();
}
?>