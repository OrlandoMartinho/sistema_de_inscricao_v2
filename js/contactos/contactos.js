
   
    const closeButtons = document.querySelectorAll(".close");
    const viewButtons = document.querySelectorAll(".view-btn");
    const deleteButtons = document.querySelectorAll(".delete-btn");

    // Abrir modal "Ver Mensagem"
    viewButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            modals.view.style.display = "block";
        });
    });

    // Abrir modal "Excluir Mensagem"
    deleteButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            modals.delete.style.display = "block";
        });
    });

    // Abrir modal "Responder" a partir do modal "Ver"
    function openReplyModal() {
        modals.view.style.display = "none";
        modals.reply.style.display = "block";
    }

    // Fechar modais ao clicar no "X" ou fora
    closeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            Object.values(modals).forEach(modal => {
                modal.style.display = "none";
            });
        });
    });

    window.addEventListener("click", (event) => {
        if (event.target.classList.contains("modal")) {
            Object.values(modals).forEach(modal => {
                modal.style.display = "none";
            });
        }
    });

    // Simular envio de resposta (substitua por AJAX/backend)
    document.querySelector(".send-btn").addEventListener("click", () => {
        alert("Resposta enviada com sucesso!");
        modals.reply.style.display = "none";
    });

    // Simular exclusão (substitua por AJAX/backend)
    document.querySelector(".confirm-delete-btn").addEventListener("click", () => {
        alert("Mensagem excluída!");
        modals.delete.style.display = "none";
        // Aqui você removeria a linha da tabela via JavaScript
    });


    // DOM Elements
    const tableBody = document.getElementById("messages-table-body");
    const searchBox = document.querySelector(".search-box");

    // Modals
    const modals = {
        view: document.getElementById("viewModal"),
        reply: document.getElementById("replyModal"),
        delete: document.getElementById("deleteModal")
    };

    // Variável global para armazenar mensagens
    let messages = [];
    let currentMessageId = null;

    // Carregar mensagens do "backend" (JSON)
    async function loadMessages() {
        try {
            
            messages = [
                {
                    "id": 1,
                    "nome": "João Silva",
                    "email": "joao@email.com",
                    "assunto": "Informações sobre cursos",
                    "mensagem": "Olá, gostaria de saber mais sobre os cursos disponíveis.",
                    "data": "10/10/2023",
                    "status": "respondido"
                },
                {
                    "id": 2,
                    "nome": "Maria Santos",
                    "email": "maria@email.com",
                    "assunto": "Dúvida sobre inscrição",
                    "mensagem": "Como faço para me inscrever no evento?",
                    "data": "12/10/2023",
                    "status": "pendente"
                }
            ]
            renderMessages(messages);
        } catch (error) {
            console.error("Erro ao carregar mensagens:", error);
        }
    }

    // Renderizar mensagens na tabela
    function renderMessages(messagesToRender) {
        tableBody.innerHTML = '';
        messagesToRender.forEach(msg => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${msg.nome}</td>
                <td>${msg.email}</td>
                <td>${msg.assunto}</td>
                <td>${msg.data}</td>
                <td><span class="status-badge status-${msg.status}">${msg.status.charAt(0).toUpperCase() + msg.status.slice(1)}</span></td>
                <td>
                    <button class="action-btn view-btn" data-id="${msg.id}">Ver</button>
                    <button class="action-btn delete-btn" data-id="${msg.id}">Excluir</button>
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Adicionar event listeners aos botões dinâmicos
        addEventListeners();
    }

    // Pesquisar mensagens
    searchBox.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredMessages = messages.filter(msg =>
            msg.nome.toLowerCase().includes(searchTerm) ||
            msg.email.toLowerCase().includes(searchTerm) ||
            msg.assunto.toLowerCase().includes(searchTerm)
        );
        renderMessages(filteredMessages);
    });

    // Abrir modal "Ver Mensagem"
    function openViewModal(id) {
        const message = messages.find(msg => msg.id === id);
        if (message) {
            document.getElementById("modal-nome").textContent = message.nome;
            document.getElementById("modal-email").textContent = message.email;
            document.getElementById("modal-assunto").textContent = message.assunto;
            document.getElementById("modal-data").textContent = message.data;
            document.getElementById("modal-mensagem").value = message.mensagem;
            currentMessageId = id;
            modals.view.style.display = "block";
        }
    }

    // Abrir modal "Responder"
    function openReplyModal() {
        const message = messages.find(msg => msg.id === currentMessageId);
        if (message) {
            document.getElementById("reply-email").textContent = message.email;
            document.getElementById("reply-subject").value = `Resposta: ${message.assunto}`;
            modals.view.style.display = "none";
            modals.reply.style.display = "block";
        }
    }

    // Abrir modal "Excluir"
    function openDeleteModal(id) {
        currentMessageId = id;
        modals.delete.style.display = "block";
    }

    // Fechar modais
    function closeModals() {
        Object.values(modals).forEach(modal => {
            modal.style.display = "none";
        });
    }

    // Adicionar event listeners aos botões dinâmicos
    function addEventListeners() {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', () => openViewModal(parseInt(btn.dataset.id)));
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => openDeleteModal(parseInt(btn.dataset.id)));
        });
    }

    // Event listeners para fechar modais
    document.querySelectorAll('.close').forEach(btn => {
        btn.addEventListener('click', closeModals);
    });

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) closeModals();
    });

    // Simular envio de resposta (substitua por AJAX real)
    document.querySelector('.send-btn').addEventListener('click', () => {
        alert(`Resposta enviada para: ${document.getElementById("reply-email").textContent}`);
        closeModals();
    });

    // Simular exclusão (substitua por AJAX real)
    document.querySelector('.confirm-delete-btn').addEventListener('click', () => {
        messages = messages.filter(msg => msg.id !== currentMessageId);
        renderMessages(messages);
        alert('Mensagem excluída!');
        closeModals();
    });

    // Inicializar
    loadMessages();