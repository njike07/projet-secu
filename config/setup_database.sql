-- Script de mise à jour de la base de données pour le système d'inscription
-- À exécuter dans phpMyAdmin ou via ligne de commande MySQL

USE inscription;

-- Mise à jour de la table utilisateurs
ALTER TABLE utilisateurs 
ADD COLUMN IF NOT EXISTS type ENUM('etudiant', 'admin') DEFAULT 'etudiant',
ADD COLUMN IF NOT EXISTS date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS statut ENUM('actif', 'inactif', 'suspendu') DEFAULT 'actif',
ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) NULL,
ADD COLUMN IF NOT EXISTS remember_expiry TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS derniere_connexion TIMESTAMP NULL;

-- Mise à jour de la table fiches_inscription
ALTER TABLE fiches_inscription 
ADD COLUMN IF NOT EXISTS lieu_naissance VARCHAR(100),
ADD COLUMN IF NOT EXISTS adresse_postale TEXT,
ADD COLUMN IF NOT EXISTS statut_inscription ENUM('en_attente', 'validee', 'refusee') DEFAULT 'en_attente',
ADD COLUMN IF NOT EXISTS date_soumission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS date_derniere_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS commentaires_admin TEXT,
ADD COLUMN IF NOT EXISTS user_id INT,
ADD FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE;

-- Mise à jour de la table documents
ALTER TABLE documents 
ADD COLUMN IF NOT EXISTS user_id INT,
ADD COLUMN IF NOT EXISTS type_document ENUM('piece_identite', 'diplome', 'photo_identite', 'justificatif_domicile', 'autre') NOT NULL,
ADD COLUMN IF NOT EXISTS nom_fichier VARCHAR(255) NOT NULL,
ADD COLUMN IF NOT EXISTS chemin_fichier VARCHAR(500) NOT NULL,
ADD COLUMN IF NOT EXISTS taille_fichier INT,
ADD COLUMN IF NOT EXISTS type_mime VARCHAR(100),
ADD COLUMN IF NOT EXISTS statut ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
ADD COLUMN IF NOT EXISTS date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS commentaire_admin TEXT,
ADD FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE;

-- Mise à jour de la table modifications (audit)
ALTER TABLE modifications 
ADD COLUMN IF NOT EXISTS user_id INT,
ADD COLUMN IF NOT EXISTS action VARCHAR(50) NOT NULL,
ADD COLUMN IF NOT EXISTS details TEXT,
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45),
ADD COLUMN IF NOT EXISTS user_agent TEXT,
ADD COLUMN IF NOT EXISTS modification_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE SET NULL;

-- Mise à jour de la table tentatives_connexion
ALTER TABLE tentatives_connexion 
ADD COLUMN IF NOT EXISTS email VARCHAR(255),
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45),
ADD COLUMN IF NOT EXISTS user_agent TEXT,
ADD COLUMN IF NOT EXISTS details VARCHAR(255),
ADD COLUMN IF NOT EXISTS success BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS tentative_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Création d'un utilisateur admin par défaut
INSERT IGNORE INTO utilisateurs (nom, prenom, email, mot_de_passe, type, statut) 
VALUES ('Admin', 'Système', 'admin@cosendai.com', '$argon2id$v=19$m=65536,t=4,p=3$YWRtaW5wYXNzd29yZA$hash', 'admin', 'actif');

-- Index pour optimiser les performances
CREATE INDEX IF NOT EXISTS idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_type ON utilisateurs(type);
CREATE INDEX IF NOT EXISTS idx_fiches_statut ON fiches_inscription(statut_inscription);
CREATE INDEX IF NOT EXISTS idx_documents_user ON documents(user_id);
CREATE INDEX IF NOT EXISTS idx_documents_type ON documents(type_document);
CREATE INDEX IF NOT EXISTS idx_modifications_user ON modifications(user_id);
CREATE INDEX IF NOT EXISTS idx_modifications_time ON modifications(modification_time);
CREATE INDEX IF NOT EXISTS idx_tentatives_email ON tentatives_connexion(email);
CREATE INDEX IF NOT EXISTS idx_tentatives_time ON tentatives_connexion(tentative_time);

-- Trigger pour mettre à jour automatiquement la date de dernière modification
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS update_fiche_modification 
BEFORE UPDATE ON fiches_inscription
FOR EACH ROW
BEGIN
    SET NEW.date_derniere_modification = CURRENT_TIMESTAMP;
END$$
DELIMITER ;
