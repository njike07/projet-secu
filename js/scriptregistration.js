// Script pour les pages de connexion/inscription

document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée
    const container = document.querySelector('.container');
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'scale(0.9)';
        setTimeout(() => {
            container.style.transition = 'all 0.5s ease';
            container.style.opacity = '1';
            container.style.transform = 'scale(1)';
        }, 100);
    }

    // Validation des formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#e74c3c';
                    input.style.animation = 'shake 0.5s ease';
                } else {
                    input.style.borderColor = '#2ecc71';
                }
            });

            if (!isValid) {
                e.preventDefault();
                showError('Veuillez remplir tous les champs obligatoires');
            }
        });
    });

    // Validation email en temps réel
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.style.borderColor = '#e74c3c';
                showError('Format d\'email invalide');
            } else if (this.value) {
                this.style.borderColor = '#2ecc71';
            }
        });
    });

    // Validation mot de passe
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.length < 6 && this.value.length > 0) {
                this.style.borderColor = '#f39c12';
            } else if (this.value.length >= 6) {
                this.style.borderColor = '#2ecc71';
            }
        });
    });
});

// Fonction pour afficher les erreurs
function showError(message) {
    const existingError = document.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        background: #e74c3c;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        text-align: center;
        animation: fadeIn 0.3s ease;
    `;

    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
        setTimeout(() => errorDiv.remove(), 5000);
    }
}

// Styles pour les animations
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    input {
        transition: border-color 0.3s ease;
    }
`;
document.head.appendChild(style);