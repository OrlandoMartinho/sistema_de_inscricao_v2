<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');
include '../../services/cursos-services.php';


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cursos</title>
    <link rel="stylesheet" href="../css1/admin/cursos/cursos.css">
    <link rel="stylesheet" href="../css1/cursos-modais.css">
</head>
<body>
    <header style="background-image: url('../../img/ac.jpg'); height: 150px;">
        <div class="container">
            <nav>
                <a href="../index.php" class="logo">IP30SET</a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Painel de Administração</h3>
            <div class="admin-menu">
                <a href="admin-dashboard.php"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.php"><i>📅</i> Eventos</a>
                <a href="admin-cursos.php" class="active"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php"><i>📝</i> Inscrições</a>
                <a href="admin-config.php"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Gerenciar Cursos</h2>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Sair</button>
            </div>
            
            <!-- Mensagens de sucesso/erro -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <button class="add-btn" onclick="openAddModal()">+ Adicionar Curso</button>
                <form method="GET" action="" style="display: inline;">
                    <input type="text" name="search" class="search-box" placeholder="Pesquisar cursos..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" style="display: none;">Pesquisar</button>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Nome do Curso</th>
                        <th>Duração</th>
                        <th>Nível</th>
                        <th>Instrutor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $curso): ?>
                        <tr data-id="<?php echo $curso['id']; ?>">
                            <td><?php echo htmlspecialchars($curso['nome']); ?></td>
                            <td><?php echo htmlspecialchars($curso['duracao']); ?></td>
                            <td><?php echo htmlspecialchars($curso['nivel']); ?></td>
                            <td><?php echo htmlspecialchars($curso['instrutor']); ?></td>
                            <td><?php echo ucfirst($curso['status']); ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $curso['id']; ?>)">Editar</button>
                                <button class="action-btn delete-btn" onclick="openDeleteModal(<?php echo $curso['id']; ?>, '<?php echo addslashes($curso['nome']); ?>')">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($cursos)): ?>
                        <tr>
                            <td colspan="6">Nenhum curso encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal de Adicionar Curso -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Adicionar Novo Curso</h3>
                <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form id="addForm" method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add-course-name">Nome do Curso*</label>
                        <input type="text" id="add-course-name" name="nome" placeholder="Digite o nome do curso" required>
                    </div>
                    <div class="form-group">
                        <label for="add-description">Descrição*</label>
                        <textarea id="add-description" name="descricao" placeholder="Descreva o conteúdo do curso..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="add-duration">Duração*</label>
                        <input type="text" id="add-duration" name="duracao" placeholder="Ex: 8 semanas, 3 meses" required>
                    </div>
                    <div class="form-group">
                        <label for="add-level">Nível*</label>
                        <select id="add-level" name="nivel" required>
                            <option value="Iniciante">Iniciante</option>
                            <option value="Intermediário">Intermediário</option>
                            <option value="Avançado">Avançado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add-instructor">Instrutor*</label>
                        <input type="text" id="add-instructor" name="instrutor" placeholder="Nome do instrutor" required>
                    </div>
                    <div class="form-group">
                        <label for="add-status">Status*</label>
                        <select id="add-status" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar Curso</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Curso</h3>
                <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-title">Nome do Curso*</label>
                        <input type="text" id="edit-title" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Descrição*</label>
                        <textarea id="edit-description" name="descricao" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-duration">Duração*</label>
                        <input type="text" id="edit-duration" name="duracao" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-level">Nível*</label>
                        <select id="edit-level" name="nivel" required>
                            <option value="Iniciante">Iniciante</option>
                            <option value="Intermediário">Intermediário</option>
                            <option value="Avançado">Avançado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-instructor">Instrutor*</label>
                        <input type="text" id="edit-instructor" name="instrutor" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status*</label>
                        <select id="edit-status" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Exclusão -->
    <div id="deleteModal" class="modal confirm-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Exclusão</h3>
                <span class="close-btn" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <form id="deleteForm" method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-id">
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o curso "<span id="course-to-delete"></span>"? Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Funções para abrir/fechar modais
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function openEditModal(id) {
            // Buscar dados do curso (em uma aplicação real, isso viria do servidor)
            fetch(`get_curso.php?id=${id}`)
                .then(response => response.json())
                .then(curso => {
                    document.getElementById('edit-id').value = curso.id;
                    document.getElementById('edit-title').value = curso.nome;
                    document.getElementById('edit-description').value = curso.descricao;
                    document.getElementById('edit-duration').value = curso.duracao;
                    document.getElementById('edit-level').value = curso.nivel;
                    document.getElementById('edit-instructor').value = curso.instrutor;
                    document.getElementById('edit-status').value = curso.status;
                    
                    document.getElementById('editModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Erro ao buscar curso:', error);
                    alert('Erro ao carregar dados do curso');
                });
        }
        
        function openDeleteModal(id, nome) {
            document.getElementById('delete-id').value = id;
            document.getElementById('course-to-delete').textContent = nome;
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
        
        // Validação de formulários
        document.getElementById('addForm').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
            }
        });
        
        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>