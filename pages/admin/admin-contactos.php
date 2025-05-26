<?php
session_start();

include('../../config/connection.php');
include '../../services/contactos-services.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contactos</title>
    <link rel="stylesheet" href="../css1/admin/contactos/contactos.css">
    <link rel="stylesheet" href="../css1/contacts-modal.css">
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
                <a href="admin-cursos.php"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php" class="active"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php"><i>📝</i> Inscrições</a>
                <a href="admin-config.php"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Mensagens de Contacto</h2>
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
                <form method="GET" action="">
                    <input type="text" name="search" class="search-box" placeholder="Pesquisar mensagens..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" style="display: none;">Pesquisar</button>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Assunto</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="messages-table-body">
                    <?php foreach ($messages as $message): ?>
                        <tr data-id="<?php echo $message['id']; ?>" class="<?php echo $message['status']; ?>">
                            <td><?php echo htmlspecialchars($message['nome']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['assunto']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($message['data_envio'])); ?></td>
                            <td><?php echo ucfirst($message['status']); ?></td>
                            <td>
                                <button class="view-btn" onclick="openViewModal(<?php echo $message['id']; ?>)">Ver</button>
                                <button class="delete-btn" onclick="openDeleteModal(<?php echo $message['id']; ?>)">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="6">Nenhuma mensagem encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Ver Mensagem -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
            <h3>Detalhes da Mensagem</h3>
            <div class="modal-body">
                <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                <p><strong>Email:</strong> <span id="modal-email"></span></p>
                <p><strong>Assunto:</strong> <span id="modal-assunto"></span></p>
                <p><strong>Data:</strong> <span id="modal-data"></span></p>
                <p><strong>Mensagem:</strong></p>
                <textarea id="modal-mensagem" readonly></textarea>
            </div>
            <div class="modal-footer">
                <button class="action-btn reply-btn" onclick="openReplyModal()">Responder</button>
            </div>
        </div>
    </div>

    <!-- Modal Responder Mensagem -->
    <div id="replyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('replyModal')">&times;</span>
            <h3>Responder Mensagem</h3>
            <form id="replyForm" method="POST">
                <input type="hidden" name="action" value="send_reply">
                <input type="hidden" name="id" id="reply-id">
                <div class="modal-body">
                    <p><strong>Para:</strong> <span id="reply-email"></span></p>
                    <p><strong>Assunto:</strong></p>
                    <input type="text" id="reply-subject" name="subject" value="">
                    <p><strong>Resposta:</strong></p>
                    <textarea id="reply-message" name="message">Prezado(a),  

Agradecemos seu contato. Seguem as informações solicitadas:  

[Insira sua resposta aqui]  

Atenciosamente,  
Equipe IP30SET</textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="action-btn send-btn">Enviar Resposta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Excluir Mensagem -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <h3>Confirmar Exclusão</h3>
            <form id="deleteForm" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-id">
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn cancel-btn" onclick="closeModal('deleteModal')">Cancelar</button>
                    <button type="submit" class="action-btn confirm-delete-btn">Excluir</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variável para armazenar os dados da mensagem atual
        let currentMessage = null;
        
        // Função para abrir o modal de visualização
        function openViewModal(id) {
            // Buscar os dados da mensagem (em uma aplicação real, isso viria do servidor)
            fetchMessageData(id).then(message => {
                currentMessage = message;
                
                // Preencher o modal com os dados
                document.getElementById('modal-nome').textContent = message.nome;
                document.getElementById('modal-email').textContent = message.email;
                document.getElementById('modal-assunto').textContent = message.assunto;
                document.getElementById('modal-data').textContent = formatDate(message.data_envio);
                document.getElementById('modal-mensagem').value = message.mensagem;
                
                // Preencher dados para resposta
                document.getElementById('reply-email').textContent = message.email;
                document.getElementById('reply-subject').value = 'Resposta: ' + message.assunto;
                document.getElementById('reply-id').value = message.id;
                
                // Mostrar o modal
                document.getElementById('viewModal').style.display = 'block';
                
                // Marcar como lido
                if (message.status !== 'lido' && message.status !== 'respondido') {
                    markAsRead(id);
                }
            });
        }
        
        // Função para abrir o modal de resposta
        function openReplyModal() {
            closeModal('viewModal');
            document.getElementById('replyModal').style.display = 'block';
        }
        
        // Função para abrir o modal de exclusão
        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        // Função para fechar modais
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
        
        // Função para buscar dados da mensagem (simulada)
        function fetchMessageData(id) {
            return fetch(`get_message.php?id=${id}`)
                .then(response => response.json())
                .catch(error => {
                    console.error('Erro ao buscar mensagem:', error);
                    return null;
                });
        }
        
        // Função para marcar mensagem como lida
        function markAsRead(id) {
            fetch('admin-contactos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=mark_read&id=${id}`
            });
        }
        
        // Função para formatar data
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>