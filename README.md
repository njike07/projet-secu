# COENDAI - Plateforme d'inscription étudiante

## 📋 Description
Plateforme web moderne pour la gestion des inscriptions étudiantes avec interface administrateur et étudiant.

## 🚀 Installation

### Prérequis
- XAMPP (Apache + MySQL + PHP 8.0+)
- Navigateur web moderne

### Configuration
1. Cloner le projet dans `C:\xampp\htdocs\mon-projet`
2. Importer `inscription (1).sql` dans phpMyAdmin
3. Démarrer Apache et MySQL dans XAMPP
4. Accéder à `http://localhost/mon-projet`

## 👥 Comptes de test

### Administrateur
- **Email:** admin@etablissement.com
- **Mot de passe:** password

### Étudiant
- **Email:** njikeelsie91@gmail.com
- **Mot de passe:** password

## 🔧 Fonctionnalités

### Page d'accueil
- Design moderne avec gradient
- Boutons de connexion/inscription
- Statistiques de la plateforme

### Espace Étudiant
- Dashboard personnalisé
- Gestion du profil
- Suivi des inscriptions
- Upload de documents

### Espace Administrateur
- Tableau de bord avec statistiques
- Gestion des étudiants
- Validation des fiches
- Gestion des documents
- Graphiques dynamiques

## 🔒 Sécurité
- Hachage Argon2ID des mots de passe
- Protection CSRF
- Protection contre brute force
- Sessions sécurisées
- Validation des données

## 📁 Structure
```
mon-projet/
├── index.php              # Page d'accueil
├── pageLogin.php          # Connexion
├── pageSignup.php         # Inscription
├── admindash.php          # Dashboard admin
├── studash.php            # Dashboard étudiant
├── config.php             # Configuration BDD
├── security.php           # Fonctions sécurité
├── js/                    # Scripts JavaScript
├── style/                 # Feuilles de style
└── uploads/               # Documents uploadés
```

## 🌐 Accès
- **URL:** http://localhost/mon-projet
- **Base de données:** inscription
- **Port MySQL:** 3306