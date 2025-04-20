   
    // Variável para armazenar o ID do evento sendo editado/excluído
    let currentEventId = null;
    
    // Funções para abrir modais
    function openEditModal(eventId) {
        currentEventId = eventId;
        
        // Aqui você normalmente buscaria os dados do evento do servidor
        // Estou simulando com dados fixos para demonstração
        if (eventId === 1) {
            document.getElementById('edit-title').value = 'Workshop de Programação';
            document.getElementById('edit-date').value = '2023-10-15';
            document.getElementById('edit-time').value = '14:00';
            document.getElementById('edit-location').value = 'Auditório Principal';
            document.getElementById('edit-description').value = 'Um workshop prático sobre programação web com HTML, CSS e JavaScript.';
            document.getElementById('edit-status').value = 'active';
        } else if (eventId === 2) {
            document.getElementById('edit-title').value = 'Feira de Emprego';
            document.getElementById('edit-date').value = '2023-10-22';
            document.getElementById('edit-time').value = '09:00';
            document.getElementById('edit-location').value = 'Pátio Central';
            document.getElementById('edit-description').value = 'Feira anual de emprego com diversas empresas participantes.';
            document.getElementById('edit-status').value = 'active';
        } else if (eventId === 3) {
            document.getElementById('edit-title').value = 'Palestra sobre IA';
            document.getElementById('edit-date').value = '2023-11-05';
            document.getElementById('edit-time').value = '16:00';
            document.getElementById('edit-location').value = 'Sala 203';
            document.getElementById('edit-description').value = 'Palestra sobre inteligência artificial e suas aplicações no mercado atual.';
            document.getElementById('edit-status').value = 'inactive';
        }
        
        document.getElementById('editModal').style.display = 'block';
    }
    
    function openDeleteModal(eventId) {
        currentEventId = eventId;
        document.getElementById('deleteModal').style.display = 'block';
    }
    
    // Funções para fechar modais
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    // Funções para salvar/excluir eventos
    function saveEvent() {
        // Aqui você normalmente enviaria os dados para o servidor
        // Simulando uma operação bem-sucedida
        document.getElementById('confirmMessage').textContent = 'Evento atualizado com sucesso!';
        closeModal('editModal');
        document.getElementById('confirmModal').style.display = 'block';
        
        // Atualiza a tabela (simulação)
        // Em uma aplicação real, você recarregaria os dados do servidor
        console.log('Evento salvo:', {
            id: currentEventId,
            title: document.getElementById('edit-title').value,
            date: document.getElementById('edit-date').value,
            time: document.getElementById('edit-time').value,
            location: document.getElementById('edit-location').value,
            description: document.getElementById('edit-description').value,
            status: document.getElementById('edit-status').value
        });
    }
    
    function deleteEvent() {
        // Aqui você normalmente enviaria uma requisição para excluir o evento
        // Simulando uma operação bem-sucedida
        document.getElementById('confirmMessage').textContent = 'Evento excluído com sucesso!';
        closeModal('deleteModal');
        document.getElementById('confirmModal').style.display = 'block';
        
        // Remove o evento da tabela (simulação)
        // Em uma aplicação real, você recarregaria os dados do servidor
        console.log('Evento excluído:', currentEventId);
    }
    
    // Fechar modais ao clicar fora do conteúdo
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                modals[i].style.display = 'none';
            }
        }
    }
