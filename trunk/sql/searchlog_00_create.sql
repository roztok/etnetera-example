--
-- Databáze: `searchlog`
--
CREATE DATABASE IF NOT EXISTS `searchlog` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `searchlog`;

-- --------------------------------------------------------

--
-- Struktura tabulky `search_log`
--

DROP TABLE IF EXISTS `search_log`;
CREATE TABLE IF NOT EXISTS `search_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_patern` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `search_date` datetime NOT NULL,
  `ip` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `searchlog_idx_searchdate` (`search_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


--
-- Uzivatel - opravneni, pristup pouze z localhost
--

CREATE USER 'frodo'@'localhost' IDENTIFIED BY 'baggins';
GRANT SELECT, INSERT, UPDATE, DELETE  ON searchlog . * TO 'frodo'@'localhost';
FLUSH PRIVILEGES;
