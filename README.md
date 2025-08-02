# Système de Gestion des Fiches d'Inscription - Cosendai

## 📋 Description

Cosendai est un portail étudiant complet développé en PHP permettant la gestion sécurisée des inscriptions étudiantes. Le système offre une interface moderne et intuitive pour les étudiants et les administrateurs.

## ✨ Fonctionnalités

### Pour les Étudiants
- ✅ **Inscription sécurisée** avec validation des mots de passe forts
- ✅ **Connexion traditionnelle** ou via OAuth (Google/Facebook)
- ✅ **Dashboard personnalisé** avec progression d'inscription
- ✅ **Formulaire d'inscription complet** avec toutes les informations requises
- ✅ **Upload sécurisé de documents** (pièce d'identité, diplômes, etc.)
- ✅ **Gestion de profil** avec modification limitée selon le statut
- ✅ **Suivi du statut** d'inscription en temps réel

### Pour les Administrateurs
- ✅ **Dashboard administrateur** avec statistiques complètes
- ✅ **Gestion des inscriptions** (validation/refus)
- ✅ **Visualisation détaillée** des fiches étudiantes
- ✅ **Gestion des documents** uploadés
- ✅ **Journalisation complète** des actions
- ✅ **Monitoring de sécurité** (tentatives de connexion suspectes)

## 🔒 Sécurité Implémentée

### Authentification
- **Mots de passe sécurisés** : Hachage Argon2ID avec paramètres renforcés
- **Protection contre le brute force** : Limitation des tentatives de connexion
- **Sessions sécurisées** : Regeneration d'ID, cookies HttpOnly/Secure
- **OAuth 2.0** : Intégration Google et Facebook
- **Token CSRF** : Protection contre les attaques Cross-Site Request Forgery

### Protection des données
- **Requêtes préparées** : Protection complète contre l'injection SQL
- **Validation stricte** : Échappement et validation de toutes les entrées
- **Protection XSS** : Échappement des sorties avec htmlspecialchars
- **Upload sécurisé** : Validation des types MIME, taille, extensions
- **Journalisation** : Audit trail complet des actions utilisateurs

### Contrôle d'accès
- **Gestion des rôles** : Séparation stricte étudiant/administrateur
- **Vérification systématique** : Contrôle des droits à chaque action
- **Protection des données** : Accès limité aux propres informations
- **Sessions timeout** : Expiration automatique des sessions

## 🛠️ Technologies Utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript, W3.CSS
- **Sécurité** : Argon2ID, OAuth 2.0, CSRF Protection
- **Icons** : Font Awesome 6.0

## 📦 Installation

### Prérequis
- Serveur web (Apache/Nginx)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extension PHP : PDO, cURL, OpenSSL

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone [repository-url]
cd cosendai
```

2. **Configuration de la base de données**
```sql
-- Créer la base de données
CREATE DATABASE inscription CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Exécuter le script de structure
mysql -u root -p inscription < config/setup_database.sql
```

3. **Configuration des fichiers**
```php
// config/database.php - Modifier les paramètres de connexion
private $host = 'localhost';
private $dbname = 'inscription';
private $username = 'root';
private $password = '';
```

4. **Configuration OAuth (optionnel)**
```php
// config/oauth.php - Ajouter vos clés API
private $googleClientId = 'VOTRE_GOOGLE_CLIENT_ID';
private $googleClientSecret = 'VOTRE_GOOGLE_CLIENT_SECRET';
private $facebookAppId = 'VOTRE_FACEBOOK_APP_ID';
private $facebookAppSecret = 'VOTRE_FACEBOOK_APP_SECRET';
```

5. **Permissions des dossiers**
```bash
chmod 755 uploads/
chmod 755 uploads/documents/
```

6. **Compte administrateur par défaut**
- Email : `admin@cosendai.com`
- Mot de passe : `AdminPass123!`

## 🗄️ Structure de la Base de Données

### Table `utilisateurs`
- Informations d'authentification et rôles
- Support OAuth (Google/Facebook)
- Gestion des statuts (actif/inactif/suspendu)

### Table `fiches_inscription`
- Données complètes des inscriptions
- Statuts : en_attente, validee, refusee
- Horodatage des modifications

### Table `documents`
- Références aux fichiers uploadés
- Validation par les administrateurs
- Types : pièce_identité, diplome, photo_identite, etc.

### Table `modifications`
- Journal d'audit complet
- Traçabilité de toutes les actions
- Informations IP et User-Agent

### Table `tentatives_connexion`
- Monitoring des tentatives de connexion
- Détection des activités suspectes
- Limitation du brute force

## 🚀 Utilisation

### Premier démarrage
1. Accéder à `http://localhost/cosendai/pageLogin.php`
2. Créer un compte étudiant via `pageSignup.php`
3. Se connecter avec les identifiants créés
4. Remplir le formulaire d'inscription complet
5. Uploader les documents requis

### Administration
1. Se connecter avec le compte admin
2. Accéder au dashboard administrateur
3. Gérer les inscriptions en attente
4. Valider ou refuser les dossiers
5. Monitorer la sécurité

## 🔧 Configuration Avancée

### Sécurité des mots de passe
```php
// Politique de mots de passe (config/auth.php)
- Minimum 8 caractères
- Au moins 1 majuscule
- Au moins 1 minuscule  
- Au moins 1 chiffre
- Au moins 1 caractère spécial
```

### Limitation des tentatives
```php
// Protection brute force (config/auth.php)
- 5 tentatives maximum par IP/email
- Blocage de 15 minutes
- Journalisation automatique
```

### Upload de fichiers
```php
// Sécurité uploads (upload_document.php)
- Taille maximum : 5MB
- Types autorisés : JPG, PNG, GIF, PDF, DOC, DOCX
- Validation MIME type
- Noms de fichiers sécurisés
```

## 📊 Monitoring et Logs

### Journalisation
- Toutes les actions importantes sont loggées
- Informations IP et User-Agent conservées
- Historique des modifications accessible

### Sécurité
- Tentatives de connexion suspectes détectées
- Alertes automatiques pour les administrateurs
- Audit trail complet

## 🐛 Dépannage

### Problèmes courants

**Erreur de connexion à la base**
```
Solution : Vérifier les paramètres dans config/database.php
```

**Upload de fichiers échoue**
```
Solution : Vérifier les permissions du dossier uploads/
```

**OAuth ne fonctionne pas**
```
Solution : Configurer les clés API dans config/oauth.php
```

**Sessions expirées rapidement**
```
Solution : Vérifier la configuration PHP session.gc_maxlifetime
```

## 🔐 Sécurité - Vulnérabilités Résolues

### Injection SQL
- ✅ Toutes les requêtes utilisent des requêtes préparées
- ✅ Validation stricte des types de données
- ✅ Échappement des entrées utilisateur

### Cross-Site Scripting (XSS)
- ✅ Échappement systématique avec htmlspecialchars()
- ✅ Validation des entrées côté serveur
- ✅ Content Security Policy recommandée

### Cross-Site Request Forgery (CSRF)
- ✅ Tokens CSRF sur tous les formulaires
- ✅ Validation côté serveur obligatoire
- ✅ Regeneration des tokens après utilisation

### Upload de fichiers
- ✅ Validation des types MIME
- ✅ Restriction des extensions
- ✅ Limitation de taille
- ✅ Noms de fichiers sécurisés

### Gestion des sessions
- ✅ Regeneration d'ID après connexion
- ✅ Cookies HttpOnly et Secure
- ✅ Timeout automatique
- ✅ Destruction sécurisée

## 📝 Changelog

### Version 2.0.0 (Actuelle)
- ✅ Système d'authentification sécurisé complet
- ✅ Gestion des rôles étudiant/administrateur
- ✅ Dashboard administrateur avec statistiques
- ✅ Upload sécurisé de documents
- ✅ Intégration OAuth Google/Facebook
- ✅ Journalisation complète des actions
- ✅ Protection contre les attaques courantes
- ✅ Interface responsive moderne

### Version 1.0.0 (Précédente)
- Système de base avec inscription/connexion simple
- Formulaire d'inscription étudiant
- Interface basique

## 🤝 Contribution

Pour contribuer au projet :
1. Fork le repository
2. Créer une branche feature
3. Commiter les changements
4. Pousser vers la branche
5. Créer une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- Email : support@cosendai.com
- Documentation : [URL de la documentation]
- Issues : [URL du repository]/issues

---

**Développé avec ❤️ pour l'éducation moderne**
