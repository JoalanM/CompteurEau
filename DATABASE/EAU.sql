-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 10 mars 2022 à 08:55
-- Version du serveur :  10.3.34-MariaDB-0ubuntu0.20.04.1
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `EAU`
--

-- --------------------------------------------------------

--
-- Structure de la table `CONSOMMATION`
--

CREATE TABLE `CONSOMMATION` (
  `id` int(11) NOT NULL,
  `consommation` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `CONSOMMATION`
--

INSERT INTO `CONSOMMATION` (`id`, `consommation`, `date`) VALUES
(1, 5, '2022-03-08 08:11:14'),
(2, 12, '2022-03-08 08:11:23'),
(3, 4, '2022-03-08 08:11:32'),
(4, 10, '2022-03-08 08:11:47');

-- --------------------------------------------------------

--
-- Structure de la table `RELAIS`
--

CREATE TABLE `RELAIS` (
  `id` int(11) NOT NULL,
  `etat` varchar(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `RELAIS`
--

INSERT INTO `RELAIS` (`id`, `etat`, `date`) VALUES
(1, 'ON', '2022-03-08 07:16:11'),
(2, 'ON', '2022-03-08 07:16:14'),
(3, 'OFF', '2022-03-08 07:16:28'),
(4, 'ON', '2022-03-08 07:16:49'),
(5, 'ON', '2022-03-08 07:16:51');

-- --------------------------------------------------------

--
-- Structure de la table `VACANCES`
--

CREATE TABLE `VACANCES` (
  `id` int(11) NOT NULL,
  `vac` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `VACANCES`
--

INSERT INTO `VACANCES` (`id`, `vac`) VALUES
(1, 'oui'),
(2, 'non');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `CONSOMMATION`
--
ALTER TABLE `CONSOMMATION`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `VACANCES`
--
ALTER TABLE `VACANCES`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `CONSOMMATION`
--
ALTER TABLE `CONSOMMATION`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `VACANCES`
--
ALTER TABLE `VACANCES`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
