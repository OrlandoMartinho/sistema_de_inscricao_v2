

// Variável para armazenar o ID do curso sendo editado/excluído
let currentCourseId = null;

// Funções para abrir modais
function openAddModal() {
    // Limpa os campos do formulário
    document.getElementById('add-course-name').value = '';
    document.getElementById('add-description').value = '';
    document.getElementById('add-duration').value = '';
   
    
    document.getElementById('addModal').style.display = 'block';
}

function openEditModal(courseId) {
    currentCourseId = courseId;
    
    // Simulação de dados - em uma aplicação real, buscaria do servidor
    if (courseId === 1) {
        document.getElementById('edit-title').value = 'Introdução à Programação';
        document.getElementById('edit-description').value = 'Curso introdutório sobre lógica de programação e algoritmos.';
        document.getElementById('edit-duration').value = '8 semanas';
        document.getElementById('course-to-delete').textContent = 'Introdução à Programação';
    } else if (courseId === 2) {
        document.getElementById('edit-title').value = 'Desenvolvimento Web Avançado';
        document.getElementById('edit-description').value = 'Curso avançado sobre frameworks modernos de desenvolvimento web.';
        document.getElementById('edit-duration').value = '12 semanas';
        document.getElementById('course-to-delete').textContent = 'Desenvolvimento Web Avançado';
    } else if (courseId === 3) {
        document.getElementById('edit-title').value = 'Data Science Fundamentals';
        document.getElementById('edit-description').value = 'Fundamentos de ciência de dados e análise de dados.';
        document.getElementById('edit-duration').value = '10 semanas';
        document.getElementById('course-to-delete').textContent = 'Data Science Fundamentals';
    }
    
    document.getElementById('editModal').style.display = 'block';
}

function openDeleteModal(courseId) {
    currentCourseId = courseId;
    document.getElementById('deleteModal').style.display = 'block';
}

// Funções para fechar modais
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Funções para manipulação de cursos
function addCourse() {
    // Validação dos campos obrigatórios
    const courseName = document.getElementById('add-course-name').value;
    const description = document.getElementById('add-description').value;
    const duration = document.getElementById('add-duration').value;
   
    
    if (!courseName || !description || !duration ) {
        alert('Por favor, preencha todos os campos obrigatórios (*)');
        return;
    }
    
    // Simulação de envio para o servidor
    document.getElementById('confirmTitle').textContent = 'Sucesso!';
    document.getElementById('confirmMessage').textContent = 'Curso cadastrado com sucesso!';
    closeModal('addModal');
    document.getElementById('confirmModal').style.display = 'block';
    
    console.log('Novo curso adicionado:', {
        name: courseName,
        description: description,
        duration: duration,
    });
}

function saveCourse() {
    // Validação dos campos obrigatórios
    const courseName = document.getElementById('edit-title').value;
    const description = document.getElementById('edit-description').value;
    const duration = document.getElementById('edit-duration').value;
    
    
    if (!courseName || !description || !duration ) {
        alert('Por favor, preencha todos os campos obrigatórios (*)');
        return;
    }
    
    // Simulação de envio para o servidor
    document.getElementById('confirmTitle').textContent = 'Sucesso!';
    document.getElementById('confirmMessage').textContent = 'Curso atualizado com sucesso!';
    closeModal('editModal');
    document.getElementById('confirmModal').style.display = 'block';
    
    console.log('Curso atualizado:', {
        id: currentCourseId,
        name: courseName,
        description: description,
        duration: duration,
    });
}

function deleteCourse() {
    // Simulação de exclusão no servidor
    document.getElementById('confirmTitle').textContent = 'Sucesso!';
    document.getElementById('confirmMessage').textContent = 'Curso excluído com sucesso!';
    closeModal('deleteModal');
    document.getElementById('confirmModal').style.display = 'block';
    
    console.log('Curso excluído:', currentCourseId);
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
