<?php



include('../../config/connection.php');

// Variável para mensagens de sucesso/erro
$message = '';

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Adicionar novo evento
    if (isset($_POST['add_event'])) {
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descricao = $conn->real_escape_string($_POST['descricao']);
        $data_evento = $conn->real_escape_string($_POST['data'] . ' ' . $_POST['hora']);
        $local = $conn->real_escape_string($_POST['local']);
        $status = $conn->real_escape_string($_POST['status']);
        
        // Processar upload da imagem
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            // Verificar tipo e tamanho do arquivo
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 16 * 1024 * 1024; // 16MB
            
            if ($_FILES['foto']['size'] > $max_size) {
                $message = 'A imagem é muito grande. Tamanho máximo permitido: 16MB';
            } elseif (!in_array($_FILES['foto']['type'], $allowed_types)) {
                $message = 'Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.';
            } else {
                $foto = file_get_contents($_FILES['foto']['tmp_name']);
            }
        }
        
        if (empty($message)) {
            if ($foto !== null) {
                $stmt = $conn->prepare("INSERT INTO eventos (titulo, descricao, data_evento, local, foto, status) VALUES (?, ?, ?, ?, ?, ?)");
                $null = null;
                $stmt->bind_param("ssssbs", $titulo, $descricao, $data_evento, $local, $null, $status);
                $stmt->send_long_data(4, $foto); // Envia os dados blob
            } else {
                $stmt = $conn->prepare("INSERT INTO eventos (titulo, descricao, data_evento, local, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $titulo, $descricao, $data_evento, $local, $status);
            }
            
            if ($stmt->execute()) {
                $message = 'Evento adicionado com sucesso!';
                
                // Registrar atividade
                $acao = "Adicionou o evento: " . $titulo;
                $stmt_activity = $conn->prepare("INSERT INTO atividades (acao, usuario, data_acao) VALUES (?, ?, NOW())");
                $stmt_activity->bind_param("ss", $acao, $_SESSION['username']);
                $stmt_activity->execute();
                $stmt_activity->close();
            } else {
                $message = 'Erro ao adicionar evento: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Atualizar evento
    if (isset($_POST['update_event'])) {
        $id = intval($_POST['event_id']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descricao = $conn->real_escape_string($_POST['descricao']);
        $data_evento = $conn->real_escape_string($_POST['data'] . ' ' . $_POST['hora']);
        $local = $conn->real_escape_string($_POST['local']);
        $status = $conn->real_escape_string($_POST['status']);
        
        // Verificar se uma nova imagem foi enviada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 16 * 1024 * 1024; // 16MB
            
            if ($_FILES['foto']['size'] > $max_size) {
                $message = 'A imagem é muito grande. Tamanho máximo permitido: 16MB';
            } elseif (!in_array($_FILES['foto']['type'], $allowed_types)) {
                $message = 'Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.';
            } else {
                $foto = file_get_contents($_FILES['foto']['tmp_name']);
                
                $stmt = $conn->prepare("UPDATE eventos SET titulo=?, descricao=?, data_evento=?, local=?, foto=?, status=? WHERE id=?");
                $null = null;
                $stmt->bind_param("ssssbsi", $titulo, $descricao, $data_evento, $local, $null, $status, $id);
                $stmt->send_long_data(4, $foto); // Envia os dados blob
            }
        } else {
            $stmt = $conn->prepare("UPDATE eventos SET titulo=?, descricao=?, data_evento=?, local=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $titulo, $descricao, $data_evento, $local, $status, $id);
        }
        
        if (empty($message)) {
            if ($stmt->execute()) {
                $message = 'Evento atualizado com sucesso!';
                
                // Registrar atividade
                $acao = "Atualizou o evento: " . $titulo;
                $stmt_activity = $conn->prepare("INSERT INTO atividades (acao, usuario, data_acao) VALUES (?, ?, NOW())");
                $stmt_activity->bind_param("ss", $acao, $_SESSION['username']);
                $stmt_activity->execute();
                $stmt_activity->close();
            } else {
                $message = 'Erro ao atualizar evento: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Excluir evento
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Primeiro obtemos o título do evento para registrar a atividade
    $stmt = $conn->prepare("SELECT titulo FROM eventos WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($titulo);
    $stmt->fetch();
    $stmt->close();
    
    // Agora deletamos o evento
    $stmt = $conn->prepare("DELETE FROM eventos WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = 'Evento excluído com sucesso!';
        
        // Registrar atividade
        $acao = "Excluiu o evento: " . $titulo;
        $stmt_activity = $conn->prepare("INSERT INTO atividades (acao, usuario, data_acao) VALUES (?, ?, NOW())");
        $stmt_activity->bind_param("ss", $acao, $_SESSION['username']);
        $stmt_activity->execute();
        $stmt_activity->close();
    } else {
        $message = 'Erro ao excluir evento: ' . $stmt->error;
    }
    $stmt->close();
}

// Buscar eventos (incluindo a coluna de foto para verificação)
$eventos = [];
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT id, titulo, data_evento, local, status, foto IS NOT NULL AS has_image FROM eventos";

if (!empty($search)) {
    $query .= " WHERE titulo LIKE '%$search%' OR local LIKE '%$search%' OR descricao LIKE '%$search%'";
}

$query .= " ORDER BY data_evento DESC";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

// Buscar dados de um evento específico para edição
$evento_edicao = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT id, titulo, descricao, data_evento, local, status FROM eventos WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $evento_edicao = $result->fetch_assoc();
    $stmt->close();
    
    if ($evento_edicao) {
        // Separar data e hora
        $data_hora = explode(' ', $evento_edicao['data_evento']);
        $evento_edicao['data'] = $data_hora[0];
        $evento_edicao['hora'] = $data_hora[1];
    }
}

// Não feche a conexão aqui se for usada em outros includes
// $conn->close();
?>