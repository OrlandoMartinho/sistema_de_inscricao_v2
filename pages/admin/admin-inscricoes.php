<?php
// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include('../../config/connection.php');

// Diretório para upload de documentos
$upload_dir = '../../uploads/inscricoes/';

// Criar diretório se não existir
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

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

// Construir consulta SQL com filtros
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

// Preparar e executar a consulta
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$inscricoes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id'] ?? 0);
    
    if (isset($_POST['aprovar'])) {
        $status = 'aprovado';
        $observacoes = 'Inscrição aprovada pelo administrador';
    } elseif (isset($_POST['rejeitar'])) {
        $status = 'rejeitado';
        $observacoes = 'Inscrição rejeitada pelo administrador';
    } elseif (isset($_POST['update_status'])) {
        $status = $_POST['status'];
        $observacoes = $_POST['observacoes'] ?? '';
    }
    
    if (isset($status)) {
        $sql = "UPDATE inscricoes SET status = ?, observacoes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $observacoes, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Status da inscrição atualizado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar status da inscrição: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: admin-inscricoes.php");
        exit();
    }
}

// Processar exportação para CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inscricoes_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Cabeçalho do CSV
    fputcsv($output, [
        'ID', 'Nome', 'Email', 'Telefone', 'BI', 'Sexo', 
        'Curso', 'Data Inscrição', 'Status', 'Observações'
    ]);
    
    // Dados
    foreach ($inscricoes as $inscricao) {
        fputcsv($output, [
            $inscricao['id'],
            $inscricao['nome_completo'],
            $inscricao['email'],
            $inscricao['telefone'],
            $inscricao['bi_numero'],
            $inscricao['sexo'],
            $inscricao['curso_nome'],
            date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])),
            $inscricao['status'],
            $inscricao['observacoes']
        ]);
    }
    
    fclose($output);
    exit();
}

// Verificar se há edição de inscrição
$inscricao_edicao = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $sql = "SELECT id, nome_completo, curso_id, DATE(data_inscricao) as data, 
                   status, observacoes FROM inscricoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $inscricao_edicao = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Inscrições</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
       <link rel="stylesheet" href="../css1/admin/inscricoes/inscricoes.css">
       <link rel="stylesheet" href="../css1/modaiscss2.css">
    <!-- Bibliotecas para exportação PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
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
            <h3 class="text-center">Painel de Administração</h3>
            <div class="admin-menu">
                <a href="admin-dashboard.php" class="d-block p-3"><i>📊</i> Dashboard</a>
                <a href="admin-eventos.php" class="d-block p-3"><i>📅</i> Eventos</a>
                <a href="admin-cursos.php" class="d-block p-3"><i>🎓</i> Cursos</a>
                <a href="admin-contactos.php" class="d-block p-3"><i>✉️</i> Contactos</a>
                <a href="admin-inscricoes.php" class="d-block p-3 active"><i>📝</i> Inscrições</a>
                <a href="admin-config.php" class="d-block p-3"><i>⚙️</i> Configurações</a>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-header">
                <h2>Inscrições de Candidatura</h2>
                <div>
                    <div class="export-dropdown">
                        <button class="btn btn-primary" onclick="toggleExportDropdown()">Exportar Dados ▼</button>
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
                    <button class="btn btn-secondary ml-2" onclick="window.location.href='logout.php'">Sair</button>
                </div>
            </div>
            
            <!-- Mensagens de sucesso/erro -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="filter-options">
                <form method="get" action="admin-inscricoes.php" class="d-flex gap-3">
                    <select name="curso" class="filter-select" onchange="this.form.submit()">
                        <option value="">Todos os cursos</option>
                        <?php foreach ($cursos as $id => $nome): ?>
                            <option value="<?php echo $id; ?>" <?php echo ($curso_filter == $id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nome); ?>
                            </option>
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
            
            <div class="table-responsive">
                <table id="inscricoesTable" class="table">
                    <thead class="thead-light">
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
                        <?php if (!empty($inscricoes)): ?>
                            <?php foreach ($inscricoes as $inscricao): ?>
                                <tr>
                                    <td><?php echo $inscricao['id']; ?></td>
                                    <td><?php echo htmlspecialchars($inscricao['nome_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($inscricao['curso_nome']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($inscricao['data_inscricao'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $inscricao['status']; ?>">
                                            <?php echo ucfirst($inscricao['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="quick-actions">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $inscricao['id']; ?>">
                                                <button type="submit" name="aprovar" class="btn btn-success btn-sm" 
                                                    <?php echo $inscricao['status'] == 'aprovado' ? 'disabled' : ''; ?>>
                                                    Aprovar
                                                </button>
                                            </form>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $inscricao['id']; ?>">
                                                <button type="submit" name="rejeitar" class="btn btn-danger btn-sm" 
                                                    <?php echo $inscricao['status'] == 'rejeitado' ? 'disabled' : ''; ?>>
                                                    Rejeitar
                                                </button>
                                            </form>
                                            <button class="btn btn-primary btn-sm" onclick="openViewModal(
                                                <?php echo $inscricao['id']; ?>,
                                                '<?php echo htmlspecialchars($inscricao['nome_completo'], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($inscricao['curso_nome'], ENT_QUOTES); ?>',
                                                '<?php echo date('d/m/Y', strtotime($inscricao['data_inscricao'])); ?>',
                                                '<?php echo $inscricao['status']; ?>',
                                                '<?php echo htmlspecialchars($inscricao['email'], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($inscricao['telefone'], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($inscricao['bi_numero'], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($inscricao['sexo'], ENT_QUOTES); ?>'
                                            )">Ver</button>
                                            <button class="btn btn-secondary btn-sm" onclick="openEditModal(
                                                <?php echo $inscricao['id']; ?>,
                                                '<?php echo htmlspecialchars($inscricao['nome_completo'], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($inscricao['curso_nome'], ENT_QUOTES); ?>',
                                                '<?php echo date('Y-m-d', strtotime($inscricao['data_inscricao'])); ?>',
                                                '<?php echo $inscricao['status']; ?>',
                                                '<?php echo htmlspecialchars($inscricao['observacoes'] ?? '', ENT_QUOTES); ?>'
                                            )">Editar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Nenhuma inscrição encontrada</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
                            <a href="#" id="view-foto-passe" target="_blank">Foto de Passe</a>
                            <a href="#" id="view-documento-bi" target="_blank">Documento de BI</a>
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
                        <input type="text" id="edit-nome" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-curso">Curso</label>
                        <input type="text" id="edit-curso" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-data">Data</label>
                        <input type="text" id="edit-data" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select id="edit-status" name="status" class="form-control" required>
                            <option value="pendente">Pendente</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="rejeitado">Rejeitado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-observacoes">Observações</label>
                        <textarea id="edit-observacoes" name="observacoes" class="form-control" rows="3"></textarea>
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
        
        function openEditModal(id, nome, curso, data, status, observacoes) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nome').value = nome;
            document.getElementById('edit-curso').value = curso;
            document.getElementById('edit-data').value = data;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-observacoes').value = observacoes || '';
            
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
            if (!event.target.matches('.export-btn') && !event.target.matches('.btn-primary')) {
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
            
            if (format === 'csv') {
                // Exportação para CSV
                let url = 'admin-inscricoes.php?export=1';
                
                const cursoFilter = "<?php echo $curso_filter; ?>";
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

       function exportToPDF(status) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');

            // Configurações iniciais
            const pageWidth = doc.internal.pageSize.getWidth();
            const margin = 40;
            const tableStartY = 110;

            // Cabeçalho
            doc.setFontSize(20);
            doc.setTextColor(33, 37, 41);
            doc.setFont('helvetica', 'bold');
            doc.text('RELATÓRIO DE INSCRIÇÕES', pageWidth / 2, 50, { align: 'center' });

            // Linha decorativa
            doc.setDrawColor(41, 128, 185);
            doc.setLineWidth(1.5);
            doc.line(margin, 65, pageWidth - margin, 65);

            // Filtros
            doc.setFontSize(11);
            doc.setTextColor(100);
            doc.setFont('helvetica', 'normal');

            let filtros = [
                `Status: ${status === 'all' ? 'Todos' : status.charAt(0).toUpperCase() + status.slice(1)}`,
                `Curso: ${"<?php echo $curso_filter ? htmlspecialchars($cursos[$curso_filter]) : 'Todos'; ?>"}`,
                `Emitido em: ${new Date().toLocaleDateString('pt-BR')}`
            ];

            const colWidth = (pageWidth - 2 * margin) / 3;
            filtros.forEach((text, index) => {
                doc.text(text, margin + (colWidth * index), 80, {
                    align: 'left',
                    maxWidth: colWidth - 10
                });
            });

            // Cabeçalho da tabela
            const headers = [[
                { content: 'ID', styles: { fontStyle: 'bold', fillColor: [13, 110, 253], textColor: 255, cellPadding: 6 } },
                { content: 'NOME', styles: { fontStyle: 'bold', fillColor: [13, 110, 253], textColor: 255, cellPadding: 6 } },
                { content: 'CURSO', styles: { fontStyle: 'bold', fillColor: [13, 110, 253], textColor: 255, cellPadding: 6 } },
                { content: 'DATA', styles: { fontStyle: 'bold', fillColor: [13, 110, 253], textColor: 255, cellPadding: 6 } },
                { content: 'STATUS', styles: { fontStyle: 'bold', fillColor: [13, 110, 253], textColor: 255, cellPadding: 6 } }
            ]];

            // Obter dados da tabela HTML
            const table = document.getElementById('inscricoesTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));

            const data = rows.map(row => {
                const cells = row.querySelectorAll('td');
                const rowStatus = cells[4].querySelector('span').textContent.toLowerCase().trim();

                if (status === 'all' || rowStatus === status) {
                    return [
                        cells[0].textContent,
                        cells[1].textContent,
                        cells[2].textContent,
                        cells[3].textContent,
                        {
                            content: cells[4].textContent.trim(),
                            styles: {
                                fontStyle: 'bold',
                                textColor: rowStatus === 'aprovado' ? [25, 135, 84] :
                                        rowStatus === 'pendente' ? [255, 193, 7] :
                                        [220, 53, 69]
                            }
                        }
                    ];
                }
                return null;
            }).filter(row => row !== null);

            // Adicionar tabela centralizada ao PDF
            doc.autoTable({
                head: headers,
                body: data,
                startY: tableStartY,
                theme: 'striped',
                tableWidth: 'wrap',
                pagebreak: 'auto',

                styles: {
                    fontSize: 10,
                    cellPadding: 5,
                    overflow: 'linebreak',
                    valign: 'middle',
                    font: 'helvetica',
                    textColor: [33, 37, 41],
                    lineColor: [222, 226, 230],
                    lineWidth: 0.5
                },
                headStyles: {
                    fillColor: [13, 110, 253],
                    textColor: 255,
                    fontStyle: 'bold',
                    halign: 'center'
                },
                columnStyles: {
                    0: { cellWidth: 40, halign: 'center' },
                    1: { cellWidth: 100, halign: 'left' },
                    2: { cellWidth: 80, halign: 'left' },
                    3: { cellWidth: 60, halign: 'center' },
                    4: { cellWidth: 50, halign: 'center' }
                },
                alternateRowStyles: {
                    fillColor: [248, 249, 250]
                },

                didDrawPage: function (data) {
                    // Centralizar a tabela dinamicamente
                    const tableWidth = data.table.width;
                    const centerMargin = (pageWidth - tableWidth) / 2;
                    data.settings.margin.left = centerMargin;

                    // Rodapé
                    doc.setFontSize(9);
                    doc.setTextColor(100);
                    doc.setFont('helvetica', 'italic');

                    const footerText = `Instituto Politécnico 30 de Setembro - Página ${doc.internal.getNumberOfPages()}`;
                    doc.text(
                        footerText,
                        pageWidth / 2,
                        doc.internal.pageSize.getHeight() - 20,
                        { align: 'center' }
                    );

                    // Linha do rodapé
                    doc.setDrawColor(222, 226, 230);
                    doc.setLineWidth(0.5);
                    doc.line(
                        margin,
                        doc.internal.pageSize.getHeight() - 30,
                        pageWidth - margin,
                        doc.internal.pageSize.getHeight() - 30
                    );
                }
            });

            // Nome do ficheiro
            const statusLabel = status === 'all' ? 'Todas' :
                                status === 'aprovado' ? 'Aprovadas' :
                                status === 'pendente' ? 'Pendentes' :
                                'Rejeitadas';

            const filename = `Inscricoes_${statusLabel}_${new Date().toISOString().slice(0, 10)}.pdf`;
            doc.save(filename);
        }


                // Fechar modal ao pressionar ESC
                document.onkeydown = function(evt) {
                    evt = evt || window.event;
                    if (evt.key === "Escape") {
                        const modals = document.querySelectorAll('.modal');
                        modals.forEach(modal => {
                            if (modal.style.display === 'block') {
                                modal.style.display = 'none';
                            }
                        });
                    }
                };

    </script>
</body>
</html>