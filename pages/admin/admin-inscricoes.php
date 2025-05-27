<?php
// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['loggedin']) ){
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');
include '../../services/inscricoes-services.php';

// Obter cursos para filtro
$cursos_query = "SELECT id, nome FROM cursos";
$cursos_result = $conn->query($cursos_query);
$cursos = [];
while ($row = $cursos_result->fetch_assoc()) {
    $cursos[$row['id']] = $row['nome'];
}

// Processar filtros
$curso_filter = isset($_GET['curso']) ? intval($_GET['curso']) : null;
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;

// Obter inscrições com filtros
$result = getInscricoes($conn, $curso_filter, $status_filter);

// Processar ação de atualização de status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];
    
    if (updateInscricaoStatus($conn, $id, $status, $observacoes)) {
        $_SESSION['success_message'] = "Status da inscrição atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar status da inscrição";
    }
    
    header("Location: admin-inscricoes.php");
    exit();
}

// Processar exportação de dados para CSV
if (isset($_GET['export'])) {
    exportInscricoesToCSV($conn, $curso_filter, $status_filter);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Inscrições</title>
    <link rel="stylesheet" href="../css1/admin/inscricoes/inscricoes.css">
    <style>
        /* Estilos dos modais movidos para aqui para evitar o erro 404 */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 5px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: black;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: capitalize;
        }
        
        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-aprovado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejeitado {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Estilos do dropdown de exportação */
        .export-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0;
        }
        
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .show {
            display: block;
        }
    </style>
    <!-- Adicionando bibliotecas para exportação PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
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
                <a href="admin-dashboard.php"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.php"><i>📅</i> Eventos</a>
                <a href="admin-cursos.php"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php" class="active"><i>📝</i> Inscrições</a>
                <a href="admin-config.php"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Inscrições de Candidatura</h2>
                <div class="export-dropdown">
                    <button class="export-btn" onclick="toggleExportDropdown()">Exportar Dados ▼</button>
                    <div id="exportDropdown" class="dropdown-content">
                        <a href="#" onclick="exportData('all', 'csv')">CSV - Todas</a>
                        <a href="#" onclick="exportData('aprovado', 'csv')">CSV - Aprovadas</a>
                        <a href="#" onclick="exportData('pendente', 'csv')">CSV - Pendentes</a>
                        <a href="#" onclick="exportData('rejeitado', 'csv')">CSV - Rejeitadas</a>
                        <a href="#" onclick="exportData('all', 'pdf')">PDF - Todas</a>
                        <a href="#" onclick="exportData('aprovado', 'pdf')">PDF - Aprovadas</a>
                        <a href="#" onclick="exportData('pendente', 'pdf')">PDF - Pendentes</a>
                        <a href="#" onclick="exportData('rejeitado', 'pdf')">PDF - Rejeitadas</a>
                    </div>
                </div>
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
                        <?php foreach ($cursos as $id => $nome): ?>
                            <option value="<?php echo $id; ?>" <?php echo ($curso_filter == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($nome); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">Todos os status</option>
                        <option value="pendente" <?php echo ($status_filter == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                        <option value="aprovado" <?php echo ($status_filter == 'aprovado') ? 'selected' : ''; ?>>Aprovado</option>
                        <option value="rejeitado" <?php echo ($status_filter == 'rejeitado') ? 'selected' : ''; ?>>Rejeitado</option>
                    </select>
                </form>
            </div>
            
            <table id="inscricoesTable">
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
                                <td><?php echo htmlspecialchars($row['curso_nome']); ?></td>
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
                                        '<?php echo htmlspecialchars($row['curso_nome'], ENT_QUOTES); ?>',
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
                                        '<?php echo htmlspecialchars($row['curso_nome'], ENT_QUOTES); ?>',
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
                            <a href="#" id="view-foto-passe" target="_blank">Foto de Passe</a><br>
                            <a href="#" id="view-documento-bi" target="_blank">Documento de BI</a><br>
                            <a href="#" id="view-comprovativo" target="_blank">Comprovativo</a>
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
            
            // Configurar links para download dos documentos
            document.getElementById('view-foto-passe').href = 'download_document.php?id=' + id + '&type=foto_passe';
            document.getElementById('view-documento-bi').href = 'download_document.php?id=' + id + '&type=documento_bi';
            document.getElementById('view-comprovativo').href = 'download_document.php?id=' + id + '&type=comprovativo';
            
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

        // Função para alternar o dropdown de exportação
        function toggleExportDropdown() {
            document.getElementById("exportDropdown").classList.toggle("show");
        }

        // Fechar o dropdown se clicar fora dele
        window.onclick = function(event) {
            if (!event.target.matches('.export-btn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        // Função para exportar dados
        function exportData(status, format) {
            // Fechar o dropdown
            document.getElementById("exportDropdown").classList.remove("show");
            
            // Obter os filtros atuais
            const cursoFilter = "<?php echo $curso_filter; ?>";
            
            if (format === 'csv') {
                // Exportação para CSV
                let url = 'admin-inscricoes.php?export=1';
                
                if (cursoFilter) {
                    url += '&curso=' + cursoFilter;
                }
                
                if (status !== 'all') {
                    url += '&status=' + status;
                }
                
                window.location.href = url;
            } else if (format === 'pdf') {
                // Exportação para PDF
                exportToPDF(status);
            }
        }

        // Função para exportar para PDF
        function exportToPDF(status) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Título do documento
            doc.setFontSize(18);
            doc.text('Relatório de Inscrições', 105, 15, { align: 'center' });
            
            // Filtros aplicados
            doc.setFontSize(12);
            let filtros = 'Status: ' + (status === 'all' ? 'Todos' : status.charAt(0).toUpperCase() + status.slice(1));
            
            const cursoFilter = "<?php echo $curso_filter; ?>";
            const cursoNome = "<?php echo $curso_filter ? htmlspecialchars($cursos[$curso_filter]) : 'Todos'; ?>";
            
            filtros += '\nCurso: ' + cursoNome;
            
            doc.text(filtros, 14, 25);
            
            // Data de emissão
            const dataEmissao = new Date().toLocaleDateString('pt-BR');
            doc.text(`Emitido em: ${dataEmissao}`, 14, 35);
            
            // Cabeçalho da tabela
            const headers = [
                ['ID', 'Nome', 'Curso', 'Data', 'Status']
            ];
            
            // Dados da tabela
            const table = document.getElementById('inscricoesTable');
            const rows = table.querySelectorAll('tbody tr');
            const data = [];
            
            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length >= 5) { // Verificar se a linha tem todas as colunas necessárias
                    const statusElement = cols[4].querySelector('span');
                    if (statusElement) {
                        const rowStatus = statusElement.textContent.toLowerCase();
                        
                        // Aplicar filtro de status
                        if (status === 'all' || rowStatus === status) {
                            data.push([
                                cols[0].textContent,
                                cols[1].textContent,
                                cols[2].textContent,
                                cols[3].textContent,
                                statusElement.textContent
                            ]);
                        }
                    }
                }
            });
            
            // Adicionar tabela ao PDF
            doc.autoTable({
                head: headers,
                body: data,
                startY: 40,
                styles: {
                    fontSize: 10,
                    cellPadding: 2
                },
                headStyles: {
                    fillColor: [41, 128, 185],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                columnStyles: {
                    0: { cellWidth: 15 },
                    1: { cellWidth: 50 },
                    2: { cellWidth: 50 },
                    3: { cellWidth: 25 },
                    4: { cellWidth: 25 }
                }
            });
            
            // Salvar o PDF
            doc.save(`inscricoes_${status}_${dataEmissao.replace(/\//g, '-')}.pdf`);
        }
    </script>
</body>
</html>