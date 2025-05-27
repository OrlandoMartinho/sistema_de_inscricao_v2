<?php
// Arquivo: inscricoes-services.php

/**
 * Obtém todas as inscrições com filtros opcionais
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param int|null $curso_id ID do curso para filtrar
 * @param string|null $status Status para filtrar
 * @return mysqli_result Resultado da consulta
 */
function getInscricoes($conn, $curso_id = null, $status = null) {
    $query = "SELECT i.*, c.nome as curso_nome 
              FROM inscricoes i 
              JOIN cursos c ON i.curso_id = c.id 
              WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if (!empty($curso_id)) {
        $query .= " AND i.curso_id = ?";
        $params[] = $curso_id;
        $types .= 'i';
    }
    
    if (!empty($status)) {
        $query .= " AND i.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $query .= " ORDER BY i.data_inscricao DESC";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Atualiza o status de uma inscrição
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param int $id ID da inscrição
 * @param string $status Novo status
 * @param string $observacoes Observações/notas
 * @return bool True se atualizado com sucesso
 */
function updateInscricaoStatus($conn, $id, $status, $observacoes) {
    $query = "UPDATE inscricoes SET status = ?, observacoes = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $status, $observacoes, $id);
    return $stmt->execute();
}

/**
 * Obtém os detalhes de uma inscrição específica
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param int $id ID da inscrição
 * @return array|null Dados da inscrição ou null se não encontrado
 */
function getInscricaoById($conn, $id) {
    $query = "SELECT i.*, c.nome as curso_nome 
              FROM inscricoes i 
              JOIN cursos c ON i.curso_id = c.id 
              WHERE i.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Exporta inscrições para CSV
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param int|null $curso_id ID do curso para filtrar
 * @param string|null $status Status para filtrar
 */
function exportInscricoesToCSV($conn, $curso_id = null, $status = null) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inscricoes_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Cabeçalho do CSV
    fputcsv($output, array(
        'ID', 'Nome Completo', 'Email', 'Telefone', 'BI Número', 
        'Sexo', 'Curso', 'Data Inscrição', 'Status', 'Observações'
    ));
    
    // Obter dados
    $result = getInscricoes($conn, $curso_id, $status);
    
    // Escrever dados
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['nome_completo'],
            $row['email'],
            $row['telefone'],
            $row['bi_numero'],
            $row['sexo'],
            $row['curso_nome'],
            $row['data_inscricao'],
            $row['status'],
            $row['observacoes']
        ));
    }
    
    fclose($output);
    exit();
}

/**
 * Obtém um documento específico de uma inscrição
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param int $id ID da inscrição
 * @param string $type Tipo de documento (foto_passe, documento_bi, comprovativo)
 * @return array|null Dados do documento ou null se não encontrado
 */
function getDocumentoInscricao($conn, $id, $type) {
    $allowed_types = ['foto_passe', 'documento_bi', 'comprovativo'];
    if (!in_array($type, $allowed_types)) {
        return null;
    }
    
    $query = "SELECT $type FROM inscricoes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Cria uma nova inscrição
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param array $dados Dados da inscrição
 * @return int|false ID da nova inscrição ou false em caso de erro
 */
function createInscricao($conn, $dados) {
    $query = "INSERT INTO inscricoes (
                nome_completo, email, telefone, bi_numero, sexo, 
                curso_id, foto_passe, documento_bi, comprovativo, 
                data_inscricao, status, observacoes
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pendente', '')";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssssibbb",
        $dados['nome_completo'],
        $dados['email'],
        $dados['telefone'],
        $dados['bi_numero'],
        $dados['sexo'],
        $dados['curso_id'],
        $dados['foto_passe'],
        $dados['documento_bi'],
        $dados['comprovativo']
    );
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    
    return false;
}

/**
 * Exclui uma inscrição
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @param int $id ID da inscrição
 * @return bool True se excluído com sucesso
 */
function deleteInscricao($conn, $id) {
    $query = "DELETE FROM inscricoes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Obtém estatísticas das inscrições
 * 
 * @param mysqli $conn Conexão com o banco de dados
 * @return array Estatísticas das inscrições
 */
function getInscricoesStats($conn) {
    $stats = [];
    
    // Total de inscrições
    $query = "SELECT COUNT(*) as total FROM inscricoes";
    $result = $conn->query($query);
    $stats['total'] = $result->fetch_assoc()['total'];
    
    // Por status
    $query = "SELECT status, COUNT(*) as count FROM inscricoes GROUP BY status";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $stats[$row['status']] = $row['count'];
    }
    
    // Por curso
    $query = "SELECT c.nome as curso, COUNT(*) as count 
              FROM inscricoes i 
              JOIN cursos c ON i.curso_id = c.id 
              GROUP BY c.nome";
    $result = $conn->query($query);
    $stats['por_curso'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['por_curso'][$row['curso']] = $row['count'];
    }
    
    return $stats;
}
?>