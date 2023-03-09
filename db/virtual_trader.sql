-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 09 mars 2023 à 20:05
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `virtual_trader`
--

-- --------------------------------------------------------

--
-- Structure de la table `action`
--

CREATE TABLE `action` (
  `ID_Action` int(11) NOT NULL,
  `nomAction` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `mois` int(11) NOT NULL,
  `prix` int(11) NOT NULL,
  `dividende` int(11) NOT NULL,
  `pourcentageM` int(11) NOT NULL,
  `pourcentageA` int(11) NOT NULL,
  `ID_Partie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='table des infos sur les actions';

-- --------------------------------------------------------

--
-- Structure de la table `actionpossede`
--

CREATE TABLE `actionpossede` (
  `ID_Action` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `amis`
--

CREATE TABLE `amis` (
  `ID_Follower` int(11) NOT NULL,
  `ID_Followed` int(11) NOT NULL,
  `ami` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historiqueaction`
--

CREATE TABLE `historiqueaction` (
  `ID_Action` int(11) NOT NULL,
  `mois` int(11) NOT NULL,
  `prix` int(11) NOT NULL,
  `ID_Partie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historiquetrade`
--

CREATE TABLE `historiquetrade` (
  `ID_Action` int(11) NOT NULL,
  `moisTot` int(11) NOT NULL,
  `prix` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `ID_Partie` int(11) NOT NULL,
  `ID_User` int(11) NOT NULL,
  `nomPartie` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `mois` int(11) NOT NULL,
  `soldeJoueur` int(11) NOT NULL,
  `etatPartie` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='table des parties';

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `ID_User` int(11) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `photo` longblob NOT NULL,
  `soldeJoueur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='table des infos des joueurs';

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `action`
--
ALTER TABLE `action`
  ADD PRIMARY KEY (`ID_Action`),
  ADD UNIQUE KEY `ID_Action` (`ID_Action`),
  ADD UNIQUE KEY `ID_Partie_2` (`ID_Partie`),
  ADD KEY `ID_Partie` (`ID_Partie`);

--
-- Index pour la table `actionpossede`
--
ALTER TABLE `actionpossede`
  ADD KEY `ID_Action` (`ID_Action`),
  ADD KEY `ID_User` (`ID_User`);

--
-- Index pour la table `amis`
--
ALTER TABLE `amis`
  ADD KEY `ID_Follower` (`ID_Follower`),
  ADD KEY `ID_Followed` (`ID_Followed`);

--
-- Index pour la table `historiqueaction`
--
ALTER TABLE `historiqueaction`
  ADD UNIQUE KEY `ID_Partie` (`ID_Partie`),
  ADD UNIQUE KEY `ID_Action_2` (`ID_Action`),
  ADD KEY `ID_Action` (`ID_Action`),
  ADD KEY `ID_Partie_2` (`ID_Partie`);

--
-- Index pour la table `historiquetrade`
--
ALTER TABLE `historiquetrade`
  ADD KEY `ID_Action` (`ID_Action`);

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`ID_Partie`),
  ADD UNIQUE KEY `ID_Partie` (`ID_Partie`),
  ADD UNIQUE KEY `ID_User_2` (`ID_User`),
  ADD KEY `ID_User` (`ID_User`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_User`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ID_User` (`ID_User`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `action`
--
ALTER TABLE `action`
  MODIFY `ID_Action` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `partie`
--
ALTER TABLE `partie`
  MODIFY `ID_Partie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `action`
--
ALTER TABLE `action`
  ADD CONSTRAINT `action_ibfk_1` FOREIGN KEY (`ID_Partie`) REFERENCES `partie` (`ID_Partie`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `actionpossede`
--
ALTER TABLE `actionpossede`
  ADD CONSTRAINT `actionpossede_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `actionpossede_ibfk_2` FOREIGN KEY (`ID_Action`) REFERENCES `action` (`ID_Action`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `amis`
--
ALTER TABLE `amis`
  ADD CONSTRAINT `amis_ibfk_1` FOREIGN KEY (`ID_Follower`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `amis_ibfk_2` FOREIGN KEY (`ID_Followed`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `historiqueaction`
--
ALTER TABLE `historiqueaction`
  ADD CONSTRAINT `historiqueaction_ibfk_1` FOREIGN KEY (`ID_Partie`) REFERENCES `partie` (`ID_Partie`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historiqueaction_ibfk_2` FOREIGN KEY (`ID_Action`) REFERENCES `action` (`ID_Action`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `historiquetrade`
--
ALTER TABLE `historiquetrade`
  ADD CONSTRAINT `historiquetrade_ibfk_1` FOREIGN KEY (`ID_Action`) REFERENCES `action` (`ID_Action`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `partie`
--
ALTER TABLE `partie`
  ADD CONSTRAINT `partie_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
