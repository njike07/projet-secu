<?php
// NJIKE Elsie
require_once 'config.php';

// Rediriger si déjà connecté
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'administrateur') {
        header("Location: admindash.php");
    } else {
        header("Location: student-home.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COENDAI - Plateforme d'inscription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text {
            color: white;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .logo i {
            margin-right: 15px;
            color: #ffd700;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 3rem;
        }

        .feature {
            display: flex;
            align-items: center;
            color: white;
            opacity: 0.9;
        }

        .feature i {
            font-size: 1.5rem;
            margin-right: 15px;
            color: #ffd700;
        }

        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }

        .auth-card h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .auth-card p {
            color: #666;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            border-color: #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 3rem;
        }

        .stat {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffd700;
        }

        .stat-label {
            opacity: 0.9;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .stats {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements::before {
            top: 20%;
            right: 10%;
            animation-delay: -2s;
        }

        .floating-elements::after {
            bottom: 20%;
            left: 10%;
            animation-delay: -4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="floating-elements"></div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        COENDAI
                    </div>
                    
                    <h1>Votre avenir commence ici</h1>
                    <p>Plateforme moderne d'inscription et de gestion académique pour les étudiants ambitieux</p>
                    
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-rocket"></i>
                            <span>Inscription rapide</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>Données sécurisées</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-clock"></i>
                            <span>Suivi en temps réel</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-users"></i>
                            <span>Support 24/7</span>
                        </div>
                    </div>

                    <div class="stats">
                        <div class="stat">
                            <div class="stat-number">2,500+</div>
                            <div class="stat-label">Étudiants inscrits</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">15+</div>
                            <div class="stat-label">Formations</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfaction</div>
                        </div>
                    </div>
                </div>

                <div class="auth-card">
                    <h2>Commencez votre parcours</h2>
                    <p>Connectez-vous à votre espace ou créez votre compte étudiant</p>
                    
                    <a href="pageLogin.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                    
                    <a href="pageSignup.php" class="btn btn-outline">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </a>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                        <p style="font-size: 0.9rem; color: #888;">Vous êtes administrateur ?</p>
                        <a href="pageLogin.php" style="color: #667eea; text-decoration: none; font-weight: 600;">
                            Accès administrateur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.hero-text > *, .auth-card > *');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>