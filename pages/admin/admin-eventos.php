<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');
include '../../services/eventos-services.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Eventos</title>
    <link rel="stylesheet" href="../css1/events-styles.css">
    <style>
        .event-image {
            max-width: 100px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
        }
        .image-preview {
            max-width: 200px;
            max-height: 120px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <header style="background-image: url('../../img/ac.jpg'); height: 150px;">
        <div class="container">
            <nav>
                <a href="../index.php" class="logo">Instituto politécnico</a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Painel de Administração</h3>
            <div class="admin-menu">
                <a href="admin-dashboard.php"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.php" class="active"><i>📅</i> Eventos</a>
                <a href="admin-cursos.php"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php"><i>📝</i> Inscrições</a>
                <a href="admin-config.php"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Gerenciar Eventos</h2>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Sair</button>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo strpos($message, 'sucesso') !== false ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <button class="add-btn" onclick="openAddModal()">+ Adicionar Evento</button>
                <form method="GET" action="admin-eventos.php" style="display: flex; width: 100%; max-width: 400px;">
                    <input type="text" class="search-box" name="search" placeholder="Pesquisar eventos..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" style="margin-left: 5px; padding: 0 15px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">Buscar</button>
                </form>
            </div>
            
            <table id="eventsTable">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Local</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($eventos)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Nenhum evento encontrado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eventos as $evento): ?>
                            <tr>
                                <td>
                                    <?php if (isset($evento['id'])): ?>
                                        <img src="show_image.php?id=<?php echo $evento['id']; ?>" class="event-image" alt="Imagem do evento">
                                    <?php else: ?>
                                        <span>Sem imagem</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($evento['data_evento'])); ?></td>
                                <td><?php echo htmlspecialchars($evento['local']); ?></td>
                                <td><?php echo htmlspecialchars($evento['status']); ?></td>
                                <td>
                                    <button class="action-btn edit-btn" onclick="window.location.href='admin-eventos.php?edit=<?php echo $evento['id']; ?>'">Editar</button>
                                    <button class="action-btn delete-btn" onclick="confirmDelete(<?php echo $evento['id']; ?>)">Excluir</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal de Cadastro -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Adicionar Novo Evento</h3>
                <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form id="addEventForm" method="POST" enctype="multipart/form-data" action="admin-eventos.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add-title">Título*</label>
                        <input type="text" id="add-title" name="titulo" placeholder="Digite o título do evento" required>
                    </div>
                    <div class="form-group">
                        <label for="add-date">Data*</label>
                        <input type="date" id="add-date" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="add-time">Hora*</label>
                        <input type="time" id="add-time" name="hora" required>
                    </div>
                    <div class="form-group">
                        <label for="add-location">Local*</label>
                        <input type="text" id="add-location" name="local" placeholder="Digite o local do evento" required>
                    </div>
                    <div class="form-group">
                        <label for="add-description">Descrição</label>
                        <textarea id="add-description" name="descricao" placeholder="Descreva o evento"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="add-image">Imagem do Evento</label>
                        <input type="file" id="add-image" name="foto" accept="image/*" onchange="previewImage(this, 'add-preview')">
                        <small>Formatos aceitos: JPG, PNG, GIF (Max: 16MB)</small>
                        <img id="add-preview" class="image-preview" alt="Pré-visualização da imagem">
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
                    <button type="submit" class="btn btn-primary" name="add_event">Cadastrar Evento</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Edição -->
    <?php if ($evento_edicao): ?>
    <div id="editModal" class="modal" style="display: block;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Evento</h3>
                <span class="close-btn" onclick="window.location.href='admin-eventos.php'">&times;</span>
            </div>
            <form id="editEventForm" method="POST" enctype="multipart/form-data" action="admin-eventos.php">
                <input type="hidden" name="event_id" value="<?php echo $evento_edicao['id']; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-title">Título*</label>
                        <input type="text" id="edit-title" name="titulo" value="<?php echo htmlspecialchars($evento_edicao['titulo']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-date">Data*</label>
                        <input type="date" id="edit-date" name="data" value="<?php echo $evento_edicao['data']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-time">Hora*</label>
                        <input type="time" id="edit-time" name="hora" value="<?php echo $evento_edicao['hora']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-location">Local*</label>
                        <input type="text" id="edit-location" name="local" value="<?php echo htmlspecialchars($evento_edicao['local']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Descrição</label>
                        <textarea id="edit-description" name="descricao"><?php echo htmlspecialchars($evento_edicao['descricao']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Imagem Atual</label>
                        <div>
                            <img src="show_image.php?id=<?php echo $evento_edicao['id']; ?>" class="event-image" alt="Imagem atual do evento">
                        </div>
                        <label for="edit-image" style="margin-top: 10px;">Nova Imagem do Evento (opcional)</label>
                        <input type="file" id="edit-image" name="foto" accept="image/*" onchange="previewImage(this, 'edit-preview')">
                        <small>Deixe em branco para manter a imagem atual (Max: 16MB)</small>
                        <img id="edit-preview" class="image-preview" alt="Pré-visualização da nova imagem">
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status*</label>
                        <select id="edit-status" name="status" required>
                            <option value="ativo" <?php echo $evento_edicao['status'] == 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                            <option value="inativo" <?php echo $evento_edicao['status'] == 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="admin-eventos.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" name="update_event">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Confirmar Exclusão</h3>
                <span class="close-btn" onclick="closeModal('deleteConfirmModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este evento? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteConfirmModal')">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
            </div>
        </div>
    </div>

    <script>
        // Funções para manipulação dos modais
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function confirmDelete(eventId) {
            // Configurar o botão de confirmação
            document.getElementById('confirmDeleteBtn').onclick = function() {
                window.location.href = 'admin-eventos.php?delete=' + eventId;
            };
            
            // Mostrar o modal
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }
        
        // Função para pré-visualizar imagens antes do upload
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
        
        // Fechar modais ao clicar fora deles
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
        
        // Fechar modal de edição ao pressionar ESC
        document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.key === "Escape") {
                var editModal = document.getElementById('editModal');
                if (editModal && editModal.style.display === 'block') {
                    window.location.href = 'admin-eventos.php';
                } else {
                    closeModal('addModal');
                    closeModal('deleteConfirmModal');
                }
            }
        };
        
        // Validação do formulário de adição
        document.getElementById('addEventForm').addEventListener('submit', function(e) {
            var title = document.getElementById('add-title').value.trim();
            var date = document.getElementById('add-date').value;
            var time = document.getElementById('add-time').value;
            var location = document.getElementById('add-location').value.trim();
            
            if (!title || !date || !time || !location) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                e.preventDefault();
            }
        });
        
        // Validação do formulário de edição
        if (document.getElementById('editEventForm')) {
            document.getElementById('editEventForm').addEventListener('submit', function(e) {
                var title = document.getElementById('edit-title').value.trim();
                var date = document.getElementById('edit-date').value;
                var time = document.getElementById('edit-time').value;
                var location = document.getElementById('edit-location').value.trim();
                
                if (!title || !date || !time || !location) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>