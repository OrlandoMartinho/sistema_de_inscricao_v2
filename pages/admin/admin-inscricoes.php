<?php
// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include './config/connection.php';
include './services/inscricoes-services.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Inscrições</title>
    <link rel="stylesheet" href="../css1/admin/inscricoes/inscricoes.css">
    <link rel="stylesheet" href="../css1/modals-Incricoes.css">
</head>
<body>
    <header style="background-image: url('../../img/ac.jpg'); height: 150px;">
        <div class="container">
            <nav>
                <a href="../index.html" class="logo">IP30SET</a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Painel de Administração</h3>
            <div class="admin-menu">
                <a href="admin-dashboard.html"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.html"><i>📅</i> Eventos</a>
                <a href="admin-cursos.html"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.html"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php" class="active"><i>📝</i> Inscrições</a>
                <a href="admin-config.php"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Inscrições de Candidatura</h2>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Sair</button>
            </div>
            
            <!-- Mensagens de sucesso/erro -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo $_SESSION['success_message']; ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo $_SESSION['error_message']; ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <form method="get" action="admin-inscricoes.php" class="filter-options">
                    <select name="curso" class="filter-select" onchange="this.form.submit()">
                        <option value="">Todos os cursos</option>
                        <option value="Informática" <?php echo ($curso_filter == 'Informática') ? 'selected' : ''; ?>>Informática</option>
                        <option value="Electricidade" <?php echo ($curso_filter == 'Electricidade') ? 'selected' : ''; ?>>Electricidade</option>
                        <option value="Mecânica" <?php echo ($curso_filter == 'Mecânica') ? 'selected' : ''; ?>>Mecânica</option>
                        <option value="Construção Civil" <?php echo ($curso_filter == 'Construção Civil') ? 'selected' : ''; ?>>Construção Civil</option>
                    </select>
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">Todos os status</option>
                        <option value="pendente" <?php echo ($status_filter == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                        <option value="aprovado" <?php echo ($status_filter == 'aprovado') ? 'selected' : ''; ?>>Aprovado</option>
                        <option value="rejeitado" <?php echo ($status_filter == 'rejeitado') ? 'selected' : ''; ?>>Rejeitado</option>
                    </select>
                </form>
                <button class="export-btn" onclick="window.location.href='admin-inscricoes.php?export=1&curso=<?php echo $curso_filter; ?>&status=<?php echo $status_filter; ?>'">Exportar Dados</button>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Nome</th>
                        <th>Curso</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nome_completo']); ?></td>
                                <td><?php echo htmlspecialchars($row['curso']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['data_inscricao'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $row['status']; ?>">
                                        <?php 
                                        echo ucfirst($row['status']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn view-btn" onclick="openViewModal(
                                        <?php echo $row['id']; ?>,
                                        '<?php echo htmlspecialchars($row['nome_completo'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($row['curso'], ENT_QUOTES); ?>',
                                        '<?php echo date('d/m/Y', strtotime($row['data_inscricao'])); ?>',
                                        '<?php echo $row['status']; ?>',
                                        '<?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($row['telefone'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($row['bi_numero'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($row['sexo'], ENT_QUOTES); ?>'
                                    )">Ver</button>
                                    <button class="action-btn edit-btn" onclick="openEditModal(
                                        <?php echo $row['id']; ?>,
                                        '<?php echo htmlspecialchars($row['nome_completo'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($row['curso'], ENT_QUOTES); ?>',
                                        '<?php echo date('Y-m-d', strtotime($row['data_inscricao'])); ?>',
                                        '<?php echo $row['status']; ?>'
                                    )">Editar</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Nenhuma inscrição encontrada</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Visualização -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalhes da Inscrição</h3>
                <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="inscricao-details">
                    <div class="detail-row">
                        <span class="detail-label">Nº Inscrição:</span>
                        <span class="detail-value" id="view-id"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nome:</span>
                        <span class="detail-value" id="view-nome"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value" id="view-email"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Telefone:</span>
                        <span class="detail-value" id="view-telefone"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">BI Nº:</span>
                        <span class="detail-value" id="view-bi"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sexo:</span>
                        <span class="detail-value" id="view-sexo"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Curso:</span>
                        <span class="detail-value" id="view-curso"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Data:</span>
                        <span class="detail-value" id="view-data"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value"><span id="view-status" class="status-badge"></span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Documentos:</span>
                        <div class="documents-list" id="view-documents">
                            <!-- Documentos serão carregados via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeModal('viewModal')">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Inscrição</h3>
                <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="post" action="admin-inscricoes.php">
                <input type="hidden" name="id" id="edit-id">
                <input type="hidden" name="update_status" value="1">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-nome">Nome</label>
                        <input type="text" id="edit-nome" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-curso">Curso</label>
                        <input type="text" id="edit-curso" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-data">Data</label>
                        <input type="text" id="edit-data" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select id="edit-status" name="status" required>
                            <option value="pendente">Pendente</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="rejeitado">Rejeitado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-observacoes">Observações</label>
                        <textarea id="edit-observacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funções para manipulação dos modais
        function openViewModal(id, nome, curso, data, status, email, telefone, bi, sexo) {
            document.getElementById('view-id').textContent = id;
            document.getElementById('view-nome').textContent = nome;
            document.getElementById('view-email').textContent = email;
            document.getElementById('view-telefone').textContent = telefone;
            document.getElementById('view-bi').textContent = bi;
            document.getElementById('view-sexo').textContent = sexo;
            document.getElementById('view-curso').textContent = curso;
            document.getElementById('view-data').textContent = data;
            
            const statusBadge = document.getElementById('view-status');
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            statusBadge.className = 'status-badge status-' + status;
            
            // Carregar documentos via AJAX
            fetch('get_documents.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('view-documents').innerHTML = data;
                });
            
            document.getElementById('viewModal').style.display = 'block';
        }
        
        function openEditModal(id, nome, curso, data, status) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nome').value = nome;
            document.getElementById('edit-curso').value = curso;
            document.getElementById('edit-data').value = data;
            document.getElementById('edit-status').value = status;
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>