-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 02 août 2025 à 20:31
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12  

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `inscription`
--

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `fiche_id` int(11) NOT NULL,
  `type_document` enum('piece_identite','diplomes','photo_identite','justificatif_domicile') NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin_fichier` varchar(500) NOT NULL,
  `taille_fichier` int(11) NOT NULL,
  `type_mime` varchar(100) NOT NULL,
  `date_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `upload_par_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fiches_inscription`
--

CREATE TABLE `fiches_inscription` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) NOT NULL,
  `sexe` enum('Homme','Femme','Autre') NOT NULL,
  `nationalite` varchar(100) NOT NULL,
  `adresse_postale` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `dernier_diplome` varchar(255) NOT NULL,
  `etablissement_precedent` varchar(255) NOT NULL,
  `formation_demandee` varchar(255) NOT NULL,
  `specialisation` varchar(255) NOT NULL,
  `nom_contact_urgence` varchar(100) NOT NULL,
  `relation_contact` varchar(100) NOT NULL,
  `telephone_contact` varchar(20) NOT NULL,
  `email_contact` varchar(255) NOT NULL,
  `statut` enum('en_attente','validee','refusee') NOT NULL DEFAULT 'en_attente',
  `date_soumission` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_derniere_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `commentaires_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `modifications`
--

CREATE TABLE `modifications` (
  `id` int(11) NOT NULL,
  `fiche_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `action` enum('creation','modification','validation','refus','suppression') NOT NULL,
  `champ_modifie` varchar(100) DEFAULT NULL,
  `ancienne_valeur` text DEFAULT NULL,
  `nouvelle_valeur` text DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_utilisateur` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tentatives_connexion`
--

CREATE TABLE `tentatives_connexion` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_adresse` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `succes` tinyint(1) NOT NULL DEFAULT 0,
  `date_tentative` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tentatives_connexion`
--

INSERT INTO `tentatives_connexion` (`id`, `email`, `ip_adresse`, `user_agent`, `succes`, `date_tentative`) VALUES
(1, 'njikeelsie91@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, '2025-07-27 01:50:16'),
(2, 'njikeelsie91@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, '2025-07-27 01:56:13'),
(3, 'admin@mail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 0, '2025-07-27 02:00:21'),
(4, 'njikeelsie91@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, '2025-07-27 02:14:50');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('etudiant','administrateur') NOT NULL DEFAULT 'etudiant',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `date_creation`, `derniere_connexion`, `actif`) VALUES
(1, 'Admin', 'System', 'admin@etablissement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur', '2025-01-01 00:00:00', NULL, 1);


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fiche_id` (`fiche_id`);

--
-- Index pour la table `fiches_inscription`
--
ALTER TABLE `fiches_inscription`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utilisateur_unique` (`utilisateur_id`),
  ADD KEY `idx_fiches_statut` (`statut`),
  ADD KEY `idx_fiches_date_soumission` (`date_soumission`);

--
-- Index pour la table `modifications`
--
ALTER TABLE `modifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fiche_id` (`fiche_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `idx_modifications_date` (`date_modification`);

--
-- Index pour la table `tentatives_connexion`
--
ALTER TABLE `tentatives_connexion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_date` (`email`,`date_tentative`),
  ADD KEY `idx_tentatives_ip` (`ip_adresse`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD KEY `idx_utilisateurs_actif` (`actif`);

--
-- Index de sécurité supplémentaires
--
CREATE INDEX IF NOT EXISTS idx_tentatives_email_date ON tentatives_connexion(email, date_tentative);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_actif ON utilisateurs(actif);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
--
-- Déchargement des données de la table `documents`
--



-- --------------------------------------------------------

-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;



-- --------------------------------------------------------

-- AUTO_INCREMENT pour la table `fiches_inscription`
--
ALTER TABLE `fiches_inscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
--
-- Déchargement des données de la table `modifications`
--

INSERT INTO `modifications` (`id`, `fiche_id`, `utilisateur_id`, `action`, `commentaire`, `date_modification`, `ip_utilisateur`) VALUES
(1, 1, 2, 'creation', 'Création de la fiche d\'inscription', '2025-01-10 10:00:00', '127.0.0.1'),
(2, 2, 3, 'creation', 'Création de la fiche d\'inscription', '2025-01-11 14:30:00', '127.0.0.1'),
(3, 2, 1, 'validation', 'Fiche validée par l\'administrateur', '2025-01-11 16:00:00', '127.0.0.1'),
(4, 3, 4, 'creation', 'Création de la fiche d\'inscription', '2025-01-12 09:15:00', '127.0.0.1'),
(5, 3, 1, 'refus', 'Documents incomplets', '2025-01-12 10:00:00', '127.0.0.1');

-- --------------------------------------------------------

-- AUTO_INCREMENT pour la table `modifications`
--
ALTER TABLE `modifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `tentatives_connexion`
--
ALTER TABLE `tentatives_connexion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`fiche_id`) REFERENCES `fiches_inscription` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiches_inscription`
--
ALTER TABLE `fiches_inscription`
  ADD CONSTRAINT `fiches_inscription_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `modifications`
--
ALTER TABLE `modifications`
  ADD CONSTRAINT `modifications_ibfk_1` FOREIGN KEY (`fiche_id`) REFERENCES `fiches_inscription` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `modifications_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
