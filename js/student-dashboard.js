// Script pour le dashboard étudiant

document.addEventListener('DOMContentLoaded', function() {
    // Animation au chargement
    const cards = document.querySelectorAll('.card, .option');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Animation de la barre de progression
    const progressBar = document.querySelector('.progress');
    if (progressBar) {
        const percentage = progressBar.textContent;
        progressBar.style.width = '0%';
        setTimeout(() => {
            progressBar.style.transition = 'width 2s ease-in-out';
            progressBar.style.width = percentage;
        }, 500);
    }

    // Effet hover sur les options
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
        option.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });

        option.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        });
    });

    // Gestion des notifications
    const statusBar = document.querySelector('.status-bar');
    if (statusBar) {
        statusBar.addEventListener('click', function() {
            this.style.animation = 'pulse 0.5s ease';
        });
    }

    // Mise à jour dynamique du statut
    updateStatusDisplay();
});

// Fonction pour mettre à jour l'affichage du statut
function updateStatusDisplay() {
    const statusElements = document.querySelectorAll('[class*="status"]');
    statusElements.forEach(element => {
        const status = element.textContent.toLowerCase();
        if (status.includes('validé') || status.includes('validee')) {
            element.style.background = 'linear-gradient(45deg, #2ecc71, #27ae60)';
        } else if (status.includes('refusé') || status.includes('refusee')) {
            element.style.background = 'linear-gradient(45deg, #e74c3c, #c0392b)';
        } else {
            element.style.background = 'linear-gradient(45deg, #f39c12, #e67e22)';
        }
    });
}

// Fonction pour afficher des notifications toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${getToastIcon(type)}</span>
            <span class="toast-message">${message}</span>
        </div>
    `;

    // Styles pour le toast
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${getToastColor(type)};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        animation: slideInUp 0.4s ease;
        max-width: 300px;
    `;

    document.body.appendChild(toast);

    // Auto-suppression
    setTimeout(() => {
        toast.style.animation = 'slideOutDown 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// Fonctions utilitaires pour les toasts
function getToastIcon(type) {
    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    return icons[type] || icons.info;
}

function getToastColor(type) {
    const colors = {
        success: '#2ecc71',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    return colors[type] || colors.info;
}

// Gestion des documents
function handleDocumentClick(docType) {
    showToast(`Gestion des documents ${docType} - Fonctionnalité en cours de développement`, 'info');
}

// Styles pour les animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInUp {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes slideOutDown {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(100%); opacity: 0; }
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .toast-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .toast-icon {
        font-weight: bold;
        font-size: 16px;
    }
    .option {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .progress {
        transition: width 2s ease-in-out;
    }
`;
document.head.appendChild(style);