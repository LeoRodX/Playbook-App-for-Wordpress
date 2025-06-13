document.addEventListener('DOMContentLoaded', function() {
    const helpButton = document.querySelector('.help-button');
    const modal = document.getElementById('aboutModal');
    const closeButton = document.querySelector('.about-close');
    
    if (!helpButton || !modal || !closeButton) return;
    
    // Функция для открытия модального окна
    function openModal() {
        modal.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
        closeButton.focus();
    }
    
    // Функция для закрытия модального окна
    function closeModal() {
        modal.classList.remove('is-visible');
        document.body.style.overflow = '';
        helpButton.focus();
    }
    
    // Обработчики событий
    helpButton.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);
    
    // Закрытие при клике вне окна
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('is-visible')) {
            closeModal();
        }
    });
});