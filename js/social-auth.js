// Social Authentication JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Google authentication
    const googleBtns = document.querySelectorAll('.google-btn');
    googleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Placeholder for Google OAuth integration
            alert('Fonctionnalité Google OAuth en cours de développement');
            // Future implementation:
            // window.location.href = 'auth/google.php';
        });
    });

    // Facebook authentication
    const facebookBtns = document.querySelectorAll('.facebook-btn');
    facebookBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Placeholder for Facebook OAuth integration
            alert('Fonctionnalité Facebook OAuth en cours de développement');
            // Future implementation:
            // window.location.href = 'auth/facebook.php';
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    input.style.borderColor = '#ccc';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires');
            }
        });
    });

    // Password strength indicator (for signup)
    const passwordInput = document.querySelector('input[name="mot_de_passe"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            showPasswordStrength(strength);
        });
    }
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    return strength;
}

function showPasswordStrength(strength) {
    const colors = ['#e74c3c', '#e67e22', '#f39c12', '#27ae60', '#2ecc71'];
    const texts = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
    const requirements = [
        '8+ caractères, majuscule, minuscule, chiffre, symbole requis',
        'Ajouter une majuscule, un chiffre et un symbole',
        'Ajouter un chiffre et un symbole',
        'Ajouter un symbole',
        'Mot de passe sécurisé !'
    ];
    
    let indicator = document.getElementById('password-strength');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'password-strength';
        indicator.style.cssText = 'font-size: 11px; margin-top: 5px; font-weight: bold; padding: 5px; border-radius: 4px;';
        document.querySelector('input[name="mot_de_passe"]').parentNode.appendChild(indicator);
    }
    
    if (strength > 0) {
        indicator.textContent = `${texts[strength - 1]}: ${requirements[strength - 1]}`;
        indicator.style.color = colors[strength - 1];
        indicator.style.backgroundColor = colors[strength - 1] + '20';
    } else {
        indicator.textContent = requirements[0];
        indicator.style.color = colors[0];
        indicator.style.backgroundColor = colors[0] + '20';
    }
}