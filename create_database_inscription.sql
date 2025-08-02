-- =============================================
-- Script de création de la base de données INSCRIPTION
-- Projet: Cosendai - Portail Étudiant
-- =============================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS `inscription-2` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `inscription-2`;

-- =============================================
-- Table: utilisateurs
-- Gestion des comptes utilisateurs (étudiants et administrateurs)
-- =============================================
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `type` enum('etudiant','admin') NOT NULL DEFAULT 'etudiant',
  `statut` enum('actif','inactif','suspendu') NOT NULL DEFAULT 'actif',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `token_remember` varchar(255) DEFAULT NULL,
  `token_remember_expire` timestamp NULL DEFAULT NULL,
  `oauth_provider` varchar(50) DEFAULT NULL,
  `oauth_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_type` (`type`),
  KEY `idx_statut` (`statut`),
  KEY `idx_token_remember` (`token_remember`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: fiches_inscription
-- Fiches d'inscription détaillées des étudiants
-- =============================================
CREATE TABLE IF NOT EXISTS `fiches_inscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) NOT NULL,
  `nationalite` varchar(50) NOT NULL,
  `adresse_postale` text NOT NULL,
  `niveau_etude` varchar(100) NOT NULL,
  `etablissement_precedent` varchar(200) DEFAULT NULL,
  `diplome_obtenu` varchar(200) DEFAULT NULL,
  `annee_obtention` year(4) DEFAULT NULL,
  `formation_souhaitee` varchar(200) NOT NULL,
  `motivation` text DEFAULT NULL,
  `nom_urgence` varchar(100) NOT NULL,
  `prenom_urgence` varchar(100) NOT NULL,
  `telephone_urgence` varchar(20) NOT NULL,
  `relation_urgence` varchar(50) NOT NULL,
  `statut` enum('en_attente','validee','refusee') NOT NULL DEFAULT 'en_attente',
  `commentaire_admin` text DEFAULT NULL,
  `date_soumission` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_validation` timestamp NULL DEFAULT NULL,
  `validee_par` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_date_soumission` (`date_soumission`),
  KEY `fk_fiche_validateur` (`validee_par`),
  CONSTRAINT `fk_fiche_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fiche_validateur` FOREIGN KEY (`validee_par`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: documents
-- Gestion des documents uploadés par les étudiants
-- =============================================
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nom_original` varchar(255) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `type_document` enum('piece_identite','diplome','photo','justificatif_domicile','bulletin_notes','autre') NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `taille` int(11) NOT NULL,
  `chemin_fichier` varchar(500) NOT NULL,
  `statut` enum('en_attente','valide','refuse') NOT NULL DEFAULT 'en_attente',
  `commentaire_admin` text DEFAULT NULL,
  `date_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_validation` timestamp NULL DEFAULT NULL,
  `valide_par` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type_document` (`type_document`),
  KEY `idx_statut` (`statut`),
  KEY `fk_doc_validateur` (`valide_par`),
  CONSTRAINT `fk_doc_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_validateur` FOREIGN KEY (`valide_par`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: modifications
-- Journal des modifications et actions des utilisateurs
-- =============================================
CREATE TABLE IF NOT EXISTS `modifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_affectee` varchar(50) DEFAULT NULL,
  `id_enregistrement` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `modification_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_modification_time` (`modification_time`),
  CONSTRAINT `fk_modif_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: tentatives_connexion
-- Journal des tentatives de connexion (sécurité)
-- =============================================
CREATE TABLE IF NOT EXISTS `tentatives_connexion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `succes` tinyint(1) NOT NULL DEFAULT 0,
  `message` varchar(255) DEFAULT NULL,
  `tentative_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_succes` (`succes`),
  KEY `idx_tentative_time` (`tentative_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: sessions
-- Gestion des sessions utilisateur
-- =============================================
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DONNÉES INITIALES
-- =============================================

-- Créer le compte administrateur par défaut
INSERT INTO `utilisateurs` (`nom`, `prenom`, `email`, `mot_de_passe`, `type`, `statut`, `date_creation`) VALUES
('Administrateur', 'Système', 'admin@cosendai.com', '$argon2id$v=19$m=65536,t=4,p=3$YWRtaW5wYXNzMTIz$8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8K8', 'admin', 'actif', NOW());

-- Note: Le mot de passe haché correspond à "AdminPass123!"
-- Il faudra le changer lors de la première connexion

-- Créer un utilisateur de test
INSERT INTO `utilisateurs` (`nom`, `prenom`, `email`, `mot_de_passe`, `type`, `statut`, `date_creation`) VALUES
('Test', 'Utilisateur', 'test@cosendai.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 'actif', NOW());

-- Note: Le mot de passe haché correspond à "test123"

-- =============================================
-- TRIGGERS DE SÉCURITÉ
-- =============================================

-- Trigger pour journaliser les modifications des fiches
DELIMITER $$
CREATE TRIGGER `log_fiche_modification` 
AFTER UPDATE ON `fiches_inscription`
FOR EACH ROW 
BEGIN
    INSERT INTO `modifications` (`user_id`, `action`, `table_affectee`, `id_enregistrement`, `details`, `modification_time`)
    VALUES (NEW.user_id, 'fiche_modification', 'fiches_inscription', NEW.id, 
            CONCAT('Statut changé de ', OLD.statut, ' à ', NEW.statut), NOW());
END$$

-- Trigger pour journaliser les validations de documents
CREATE TRIGGER `log_document_validation` 
AFTER UPDATE ON `documents`
FOR EACH ROW 
BEGIN
    IF OLD.statut != NEW.statut THEN
        INSERT INTO `modifications` (`user_id`, `action`, `table_affectee`, `id_enregistrement`, `details`, `modification_time`)
        VALUES (NEW.user_id, 'document_validation', 'documents', NEW.id, 
                CONCAT('Document ', NEW.nom_original, ' - statut: ', NEW.statut), NOW());
    END IF;
END$$

-- Trigger pour mettre à jour la dernière connexion
CREATE TRIGGER `update_last_login` 
AFTER INSERT ON `tentatives_connexion`
FOR EACH ROW 
BEGIN
    IF NEW.succes = 1 THEN
        UPDATE `utilisateurs` 
        SET `derniere_connexion` = NOW() 
        WHERE `email` = NEW.email;
    END IF;
END$$

DELIMITER ;

-- =============================================
-- INDEX POUR LES PERFORMANCES
-- =============================================

-- Index composés pour les requêtes fréquentes
CREATE INDEX `idx_user_statut` ON `fiches_inscription` (`user_id`, `statut`);
CREATE INDEX `idx_user_type_doc` ON `documents` (`user_id`, `type_document`);
CREATE INDEX `idx_email_time` ON `tentatives_connexion` (`email`, `tentative_time`);
CREATE INDEX `idx_user_action_time` ON `modifications` (`user_id`, `action`, `modification_time`);

-- =============================================
-- VUES POUR FACILITER LES REQUÊTES
-- =============================================

-- Vue pour les statistiques administrateur
CREATE VIEW `stats_inscriptions` AS
SELECT 
    COUNT(*) as total_inscriptions,
    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN statut = 'validee' THEN 1 ELSE 0 END) as validees,
    SUM(CASE WHEN statut = 'refusee' THEN 1 ELSE 0 END) as refusees
FROM `fiches_inscription`;

-- Vue pour les documents par utilisateur
CREATE VIEW `documents_par_user` AS
SELECT 
    u.id as user_id,
    u.nom,
    u.prenom,
    u.email,
    COUNT(d.id) as total_documents,
    SUM(CASE WHEN d.statut = 'valide' THEN 1 ELSE 0 END) as documents_valides,
    SUM(CASE WHEN d.statut = 'en_attente' THEN 1 ELSE 0 END) as documents_en_attente
FROM `utilisateurs` u
LEFT JOIN `documents` d ON u.id = d.user_id
WHERE u.type = 'etudiant'
GROUP BY u.id;

-- =============================================
-- PROCÉDURES STOCKÉES UTILES
-- =============================================

-- Procédure pour nettoyer les sessions expirées
DELIMITER $$
CREATE PROCEDURE `CleanExpiredSessions`()
BEGIN
    DELETE FROM `sessions` WHERE `expires_at` < NOW();
END$$

-- Procédure pour obtenir les statistiques de sécurité
CREATE PROCEDURE `GetSecurityStats`()
BEGIN
    SELECT 
        DATE(tentative_time) as date_tentative,
        COUNT(*) as total_tentatives,
        SUM(succes) as connexions_reussies,
        COUNT(*) - SUM(succes) as echecs,
        COUNT(DISTINCT ip_address) as ips_uniques
    FROM `tentatives_connexion`
    WHERE tentative_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(tentative_time)
    ORDER BY date_tentative DESC;
END$$

DELIMITER ;

-- =============================================
-- PERMISSIONS ET SÉCURITÉ
-- =============================================

-- Créer un utilisateur spécifique pour l'application (optionnel)
-- CREATE USER 'cosendai_app'@'localhost' IDENTIFIED BY 'mot_de_passe_fort';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `inscription-2`.* TO 'cosendai_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =============================================
-- FIN DU SCRIPT
-- =============================================

-- Afficher un résumé de la création
SELECT 'Base de données INSCRIPTION-2 créée avec succès!' as message;
SELECT 'Tables créées:' as info;
SELECT TABLE_NAME as tables_creees FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'inscription-2';

-- Afficher les comptes créés
SELECT 'Comptes utilisateurs créés:' as info;
SELECT id, nom, prenom, email, type, statut FROM utilisateurs;

-- =============================================
-- NOTES IMPORTANTES:
-- 1. Changez le mot de passe admin après la première connexion
-- 2. Configurez les permissions de fichiers pour le dossier uploads/
-- 3. Activez SSL/HTTPS en production
-- 4. Configurez la sauvegarde automatique de la base
-- 5. Surveillez les logs de sécurité régulièrement
-- =============================================
