SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `user` (
  `socialId` decimal(21,0) NOT NULL,
  `avatarUrl` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` varchar(160) CHARACTER SET utf8 NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `socialNetwork` char(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `socialPageUrl` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lat` float NOT NULL,
  `lng` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `user`
 ADD PRIMARY KEY (`socialId`,`socialNetwork`), ADD KEY `latitude` (`lat`), ADD KEY `longitude` (`lng`);

ALTER TABLE `user`
 ADD FULLTEXT KEY `description` (`description`);
 
ALTER TABLE `user`
 ADD FULLTEXT KEY `name` (`name`);

CREATE TABLE IF NOT EXISTS `members` (
  `total` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `members` (`total`) VALUES
(0);

CREATE TABLE IF NOT EXISTS `quiz` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `threshold` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `solution` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;