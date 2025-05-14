
  function openViewModal(id) {
    // Simulação de dados — substitua pelo fetch ou integração real
    const dados = {
        2023001: {
            nome: "Ana Pereira",
            curso: "Informática",
            data: "05/10/2023",
            status: "Pendente",
            observacoes: "Nenhuma observação registada.",
            documentos: ["BI.pdf", "Certificado.pdf", "Foto.jpg"]
        },
        2023002: {
            nome: "Pedro Mendes",
            curso: "Gestão",
            data: "07/10/2023",
            status: "Aprovado",
            observacoes: "Documentos completos.",
            documentos: ["BI.pdf", "Histórico.pdf"]
        },
        2023003: {
            nome: "Luís Fernandes",
            curso: "Engenharia",
            data: "10/10/2023",
            status: "Rejeitado",
            observacoes: "Faltam documentos.",
            documentos: []
        }
    };

    const item = dados[id];
    if (item) {
      document.getElementById('view-id').innerText = id;
      document.getElementById('view-nome').innerText = item.nome;
      document.getElementById('view-curso').innerText = item.curso;
      document.getElementById('view-data').innerText = item.data;
      document.getElementById('view-status').innerText = item.status;
      document.getElementById('view-status').className = "status-badge status-" + item.status.toLowerCase();
      document.getElementById('view-observacoes').innerText = item.observacoes;

      const docsContainer = document.querySelector('.documents-list');
      docsContainer.innerHTML = "";
      item.documentos.forEach(doc => {
          const a = document.createElement("a");
          a.href = "#";
          a.className = "document-link";
          a.innerText = doc;
          docsContainer.appendChild(a);
      });

      document.getElementById('viewModal').style.display = "block";
    }
  }

  function openEditModal(id) {
    // Aqui você pode carregar os dados do mesmo modo acima se quiser
    document.getElementById('edit-id').value = id;
    // Exemplo: popular outros campos via JS...
    document.getElementById('editModal').style.display = "block";
  }

  function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
  }

  function saveInscricao() {
    // Aqui você pode adicionar lógica de salvar
    closeModal('editModal');
    document.getElementById('confirmMessage').innerText = "Alterações salvas com sucesso!";
    document.getElementById('confirmModal').style.display = "block";
  }

  function exportData() {
    closeModal('exportModal');
    alert("Dados exportados com sucesso!"); // ou sua lógica de exportação
  }

  // Fecha o modal ao clicar fora do conteúdo
  window.onclick = function(event) {
    const modals = document.querySelectorAll(".modal");
    modals.forEach(modal => {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    });
  }

