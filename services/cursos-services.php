<?php
// Processar ações (adicionar, editar, excluir)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nome = $_POST['nome'];
                $descricao = $_POST['descricao'];
                $duracao = $_POST['duracao'];
                $nivel = $_POST['nivel'];
                $instrutor = $_POST['instrutor'];
                $status = $_POST['status'];
                
                $stmt = $conn->prepare("INSERT INTO cursos (nome, descricao, duracao, nivel, instrutor, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $nome, $descricao, $duracao, $nivel, $instrutor, $status);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Curso adicionado com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao adicionar curso.";
                }
                $stmt->close();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $nome = $_POST['nome'];
                $descricao = $_POST['descricao'];
                $duracao = $_POST['duracao'];
                $nivel = $_POST['nivel'];
                $instrutor = $_POST['instrutor'];
                $status = $_POST['status'];
                
                $stmt = $conn->prepare("UPDATE cursos SET nome=?, descricao=?, duracao=?, nivel=?, instrutor=?, status=? WHERE id=?");
                $stmt->bind_param("ssssssi", $nome, $descricao, $duracao, $nivel, $instrutor, $status, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Curso atualizado com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao atualizar curso.";
                }
                $stmt->close();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM cursos WHERE id=?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Curso excluído com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao excluir curso.";
                }
                $stmt->close();
                break;
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// Buscar cursos
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$stmt = $conn->prepare("SELECT * FROM cursos WHERE nome LIKE ? OR instrutor LIKE ? OR nivel LIKE ? ORDER BY nome");
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();




?>