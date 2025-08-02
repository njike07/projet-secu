// Animation au chargement
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeIn 0.5s ease forwards ${index * 0.1}s`;
    });
});

// Gestion des documents
document.querySelectorAll('.doc-item').forEach(item => {
    item.addEventListener('click', () => {
        alert('Fonctionnalité d\'upload à implémenter');
    });
});

// Notification dynamique
function showNotification(message, type = 'success') {
    const notif = document.createElement('div');
    notif.className = `notification ${type}`;
    notif.innerHTML = message;
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.classList.add('fade-out');
        setTimeout(() => notif.remove(), 500);
    }, 3000);
}

// Exemple d'utilisation :
// showNotification('Profil mis à jour avec succès !');