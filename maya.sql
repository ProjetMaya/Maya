-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 29 mars 2020 à 21:32
-- Version du serveur :  10.1.36-MariaDB
-- Version de PHP :  7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `maya`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `libelle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `libelle`) VALUES
(8, 'Fruits'),
(9, 'Aromatiques'),
(10, 'Légumes'),
(11, 'Confitures'),
(12, 'Miels');

-- --------------------------------------------------------

--
-- Structure de la table `employe`
--

CREATE TABLE `employe` (
  `id` int(11) NOT NULL,
  `fonction_id` int(11) NOT NULL,
  `nom` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rue` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_embauche` datetime NOT NULL,
  `salaire` decimal(7,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fonction`
--

CREATE TABLE `fonction` (
  `id` int(11) NOT NULL,
  `lib_fonction` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20191216080509', '2019-12-16 08:05:54'),
('20200319140217', '2020-03-19 14:02:57');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id` int(11) NOT NULL,
  `libelle` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` decimal(7,2) NOT NULL,
  `date_creation` datetime NOT NULL,
  `categorie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `libelle`, `prix`, `date_creation`, `categorie_id`) VALUES
(34, 'mirabelle', '2.50', '2020-03-19 17:19:08', 8),
(35, 'pomme', '2.30', '2020-03-19 17:19:08', 8),
(36, 'poire', '2.70', '2020-03-19 17:19:08', 8),
(37, 'cerise', '3.30', '2020-03-19 17:19:08', 8),
(38, 'basilic', '1.00', '2020-03-19 17:19:08', 9),
(39, 'romarin', '1.00', '2020-03-19 17:19:08', 9),
(40, 'persil', '1.00', '2020-03-19 17:19:08', 9),
(41, 'menthe', '1.00', '2020-03-19 17:19:08', 9),
(42, 'coriandre', '1.00', '2020-03-19 17:19:08', 9),
(43, 'courgette', '2.50', '2020-03-19 17:19:08', 10),
(44, 'aubergine', '2.30', '2020-03-19 17:19:08', 10),
(45, 'laitue', '1.10', '2020-03-19 17:19:08', 10),
(46, 'carotte', '1.30', '2020-03-19 17:19:08', 10),
(47, 'brocoli', '2.30', '2020-03-19 17:19:08', 10),
(48, 'pomme de terre', '2.70', '2020-03-19 17:19:08', 10),
(49, 'chou rouge', '1.30', '2020-03-19 17:19:08', 10),
(50, 'mirabelle', '2.50', '2020-03-19 17:19:08', 11),
(51, 'fraise', '2.30', '2020-03-19 17:19:08', 11),
(52, 'framboise', '2.70', '2020-03-19 17:19:08', 11),
(53, 'cerise', '3.30', '2020-03-19 17:19:08', 11),
(54, 'acacia', '2.50', '2020-03-19 17:19:08', 12),
(55, 'sapin', '2.30', '2020-03-19 17:19:08', 12),
(56, 'montagne', '2.70', '2020-03-19 17:19:08', 12);

-- --------------------------------------------------------

--
-- Structure de la table `recette`
--

CREATE TABLE `recette` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recette`
--

INSERT INTO `recette` (`id`, `nom`) VALUES
(2, 'ratatouille'),
(3, 'clafoutis'),
(4, 'tarte aux pommes'),
(5, 'gratin dauphinois'),
(6, 'salade Caesar'),
(7, 'potée lorraine');

-- --------------------------------------------------------

--
-- Structure de la table `recette_produit`
--

CREATE TABLE `recette_produit` (
  `recette_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recette_produit`
--

INSERT INTO `recette_produit` (`recette_id`, `produit_id`) VALUES
(2, 34),
(2, 37),
(2, 39),
(2, 44),
(2, 45),
(2, 47),
(2, 48),
(2, 49),
(2, 51),
(2, 52),
(2, 54),
(3, 34),
(3, 35),
(3, 36),
(3, 40),
(3, 42),
(3, 44),
(3, 45),
(3, 48),
(3, 49),
(3, 51),
(3, 52),
(4, 35),
(4, 40),
(4, 41),
(4, 42),
(4, 43),
(4, 44),
(4, 45),
(4, 47),
(4, 48),
(4, 49),
(4, 53),
(4, 54),
(5, 34),
(5, 36),
(5, 38),
(5, 42),
(5, 48),
(5, 49),
(5, 55),
(6, 35),
(6, 36),
(6, 37),
(6, 41),
(6, 42),
(6, 44),
(6, 51),
(6, 52),
(6, 54),
(7, 34),
(7, 36),
(7, 37),
(7, 41),
(7, 45),
(7, 47),
(7, 48),
(7, 49),
(7, 50),
(7, 53),
(7, 54);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `employe`
--
ALTER TABLE `employe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F804D3B957889920` (`fonction_id`);

--
-- Index pour la table `fonction`
--
ALTER TABLE `fonction`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_29A5EC27BCF5E72D` (`categorie_id`);

--
-- Index pour la table `recette`
--
ALTER TABLE `recette`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `recette_produit`
--
ALTER TABLE `recette_produit`
  ADD PRIMARY KEY (`recette_id`,`produit_id`),
  ADD KEY `IDX_EDDD365D89312FE9` (`recette_id`),
  ADD KEY `IDX_EDDD365DF347EFB` (`produit_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `employe`
--
ALTER TABLE `employe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `fonction`
--
ALTER TABLE `fonction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT pour la table `recette`
--
ALTER TABLE `recette`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `employe`
--
ALTER TABLE `employe`
  ADD CONSTRAINT `FK_F804D3B957889920` FOREIGN KEY (`fonction_id`) REFERENCES `fonction` (`id`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `FK_29A5EC27BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);

--
-- Contraintes pour la table `recette_produit`
--
ALTER TABLE `recette_produit`
  ADD CONSTRAINT `FK_EDDD365D89312FE9` FOREIGN KEY (`recette_id`) REFERENCES `recette` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EDDD365DF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
