// Общие функции для всех страниц
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация модальных окон
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            document.body.classList.add('modal-open');
        });
        
        modal.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            const modalBackdrops = document.querySelectorAll('.modal-backdrop');
            modalBackdrops.forEach(backdrop => backdrop.remove());
        });
    });
    
    // Форматирование чисел
    window.formatMoney = function(amount) {
        return new Intl.NumberFormat('ru-RU', {
            style: 'currency',
            currency: 'RUB',
            minimumFractionDigits: 0
        }).format(amount);
    };
    
    // Обработка форм
    const authForms = document.querySelectorAll('.auth-form');
    authForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = this.getAttribute('data-action');
            formData.append('action', action);
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Обработка...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('<?= SITE_URL ?>/app/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при отправке запроса');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    });
    
    // Проверка премиум статуса
    if (document.getElementById('premium-status')) {
        checkPremiumStatus();
    }
});

async function checkPremiumStatus() {
    try {
        const response = await fetch('<?= SITE_URL ?>/app/premium.php?action=check');
        const result = await response.json();
        
        const statusElement = document.getElementById('premium-status');
        if (result.isPremium) {
            statusElement.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-crown"></i> Премиум доступ активен
                    <div>Осталось расчетов: ${result.calculationsLeft}</div>
                </div>
            `;
        } else {
            statusElement.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> У вас нет премиум доступа
                    <button class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#premiumModal">
                        Получить доступ
                    </button>
                </div>
            `;
        }
    } catch (error) {
        console.error('Ошибка при проверке статуса:', error);
    }
}