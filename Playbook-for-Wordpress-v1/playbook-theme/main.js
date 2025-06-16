document.addEventListener('DOMContentLoaded', function() {
    const helpButton = document.querySelector('.help-button');
    const helpTooltip = document.getElementById('help-tooltip');
    
    if (helpButton && helpTooltip) {
        helpButton.addEventListener('click', function(e) {
            e.preventDefault();
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Переключаем состояние
            this.setAttribute('aria-expanded', !isExpanded);
            helpTooltip.style.display = isExpanded ? 'none' : 'block';
            
            // Закрытие при клике вне тултипа
            if (!isExpanded) {
                setTimeout(() => {
                    document.addEventListener('click', closeTooltipOutside);
                }, 10);
            } else {
                document.removeEventListener('click', closeTooltipOutside);
            }
        });
        
        function closeTooltipOutside(e) {
            if (!helpTooltip.contains(e.target) && e.target !== helpButton) {
                helpButton.setAttribute('aria-expanded', 'false');
                helpTooltip.style.display = 'none';
                document.removeEventListener('click', closeTooltipOutside);
            }
        }
    }
});