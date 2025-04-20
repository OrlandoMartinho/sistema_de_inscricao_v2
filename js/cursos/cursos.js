 // Variável para armazenar o ID do evento sendo editado/excluído
 let currentEventId = null;
        
 // Funções para abrir modais
 function openAddModal() {
     // Limpa os campos do formulário
     document.getElementById('add-title').value = '';
     document.getElementById('add-date').value = '';
     document.getElementById('add-time').value = '';
     document.getElementById('add-location').value = '';
     document.getElementById('add-description').value = '';
     document.getElementById('add-image').value = '';
     document.getElementById('add-status').value = '';
     
     document.getElementById('addModal').style.display = 'block';
 }
 
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
         document.getElementById('edit-image').value = 'https://exemplo.com/workshop.jpg';
         document.getElementById('edit-status').value = 'active';
         document.getElementById('event-to-delete').textContent = 'Workshop de Programação';
     } else if (eventId === 2) {
         document.getElementById('edit-title').value = 'Feira de Emprego';
         document.getElementById('edit-date').value = '2023-10-22';
         document.getElementById('edit-time').value = '09:00';
         document.getElementById('edit-location').value = 'Pátio Central';
         document.getElementById('edit-description').value = 'Feira anual de emprego com diversas empresas participantes.';
         document.getElementById('edit-image').value = 'https://exemplo.com/feira-emprego.jpg';
         document.getElementById('edit-status').value = 'active';
         document.getElementById('event-to-delete').textContent = 'Feira de Emprego';
     } else if (eventId === 3) {
         document.getElementById('edit-title').value = 'Palestra sobre IA';
         document.getElementById('edit-date').value = '2023-11-05';
         document.getElementById('edit-time').value = '16:00';
         document.getElementById('edit-location').value = 'Sala 203';
         document.getElementById('edit-description').value = 'Palestra sobre inteligência artificial e suas aplicações no mercado atual.';
         document.getElementById('edit-image').value = 'https://exemplo.com/palestra-ia.jpg';
         document.getElementById('edit-status').value = 'inactive';
         document.getElementById('event-to-delete').textContent = 'Palestra sobre IA';
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
 
 // Funções para salvar/editar/excluir eventos
 function addEvent() {
     // Validação simples
     const title = document.getElementById('add-title').value;
     const date = document.getElementById('add-date').value;
     const time = document.getElementById('add-time').value;
     const location = document.getElementById('add-location').value;
     const status = document.getElementById('add-status').value;
     
     if (!title || !date || !time || !location || !status) {
         alert('Por favor, preencha todos os campos obrigatórios (*)');
         return;
     }
     
     // Aqui você normalmente enviaria os dados para o servidor
     // Simulando uma operação bem-sucedida
     document.getElementById('confirmTitle').textContent = 'Sucesso!';
     document.getElementById('confirmMessage').textContent = 'Evento cadastrado com sucesso!';
     closeModal('addModal');
     document.getElementById('confirmModal').style.display = 'block';
     
     // Em uma aplicação real, você recarregaria os dados do servidor
     console.log('Novo evento adicionado:', {
         title: title,
         date: date,
         time: time,
         location: location,
         description: document.getElementById('add-description').value,
         image: document.getElementById('add-image').value,
         status: status
     });
 }
 
 function saveEvent() {
     // Validação simples
     const title = document.getElementById('edit-title').value;
     const date = document.getElementById('edit-date').value;
     const time = document.getElementById('edit-time').value;
     const location = document.getElementById('edit-location').value;
     const status = document.getElementById('edit-status').value;
     
     if (!title || !date || !time || !location || !status) {
         alert('Por favor, preencha todos os campos obrigatórios (*)');
         return;
     }
     
     // Aqui você normalmente enviaria os dados para o servidor
     // Simulando uma operação bem-sucedida
     document.getElementById('confirmTitle').textContent = 'Sucesso!';
     document.getElementById('confirmMessage').textContent = 'Evento atualizado com sucesso!';
     closeModal('editModal');
     document.getElementById('confirmModal').style.display = 'block';
     
     // Atualiza a tabela (simulação)
     console.log('Evento salvo:', {
         id: currentEventId,
         title: title,
         date: date,
         time: time,
         location: location,
         description: document.getElementById('edit-description').value,
         image: document.getElementById('edit-image').value,
         status: status
     });
 }
 
 function deleteEvent() {
     // Aqui você normalmente enviaria uma requisição para excluir o evento
     // Simulando uma operação bem-sucedida
     document.getElementById('confirmTitle').textContent = 'Sucesso!';
     document.getElementById('confirmMessage').textContent = 'Evento excluído com sucesso!';
     closeModal('deleteModal');
     document.getElementById('confirmModal').style.display = 'block';
     
     // Remove o evento da tabela (simulação)
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
 
 // Define a data mínima para o campo de data como hoje
 document.addEventListener('DOMContentLoaded', function() {
     const today = new Date().toISOString().split('T')[0];
     document.getElementById('add-date').min = today;
     document.getElementById('edit-date').min = today;
 });
