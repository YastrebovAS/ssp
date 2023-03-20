-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 20 2023 г., 22:07
-- Версия сервера: 8.0.31
-- Версия PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `patent`
--

-- --------------------------------------------------------

--
-- Структура таблицы `allinformation`
--

DROP TABLE IF EXISTS `allinformation`;
CREATE TABLE IF NOT EXISTS `allinformation` (
  `active_articles` int DEFAULT NULL,
  `written_articles` int DEFAULT NULL,
  `plans` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `author`
--

DROP TABLE IF EXISTS `author`;
CREATE TABLE IF NOT EXISTS `author` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `pas` varchar(20) NOT NULL,
  `reg_date` date DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `author`
--

INSERT INTO `author` (`id`, `username`, `email`, `pas`, `reg_date`, `deleted`) VALUES
(1, 'newauthor', 'new_author@mail.ru', 'new12345', '2023-02-16', 0),
(2, 'Alex', 'yastreb@mail.ru', 'yastreb', '2023-02-16', 0),
(3, 'blita', 'blita@mail.ru', 'blitapass', '2023-02-16', 0),
(4, 'Yomama', 'mam@mail.ru', 'mom', '2023-03-05', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `corrector`
--

DROP TABLE IF EXISTS `corrector`;
CREATE TABLE IF NOT EXISTS `corrector` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `passwd` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `corrector`
--

INSERT INTO `corrector` (`id`, `username`, `passwd`, `email`) VALUES
(1, 'corrector1', 'cor1password', 'cor1@mail.ru'),
(2, 'corrector2', 'cor2password', 'cor2@mail.ru');

-- --------------------------------------------------------

--
-- Структура таблицы `deletion`
--

DROP TABLE IF EXISTS `deletion`;
CREATE TABLE IF NOT EXISTS `deletion` (
  `invention_name` varchar(255) DEFAULT NULL,
  `deletion_date` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `id_a` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `deletion`
--

INSERT INTO `deletion` (`invention_name`, `deletion_date`, `reason`, `id_a`) VALUES
('name', '2023-05-04', 'cringe', 3),
('reeerer', '0000-00-00', 'cringe', 3),
('fgggghhh', '2023-04-03', 'cringe', 3),
('rtju', '2023-04-03', 'cringe', 3),
('qqxecece', '2023-04-03', 'on', 3),
('rrtt', '2023-04-03', 'Недостаточная значимость', 3),
('eeewqw', '2023-04-03', 'Плагиат/Информация передана в ВАК', 3),
('grgrg', '2023-04-03', 'Плагиат/Информация передана в ВАК', 3),
('ewerrtt', '2023-04-03', 'Плагиат', 3),
('seeewwe', '2023-04-03', 'Плагиат/Информация передана в ВАК', 3),
('ghgh', '2023-04-03', 'Недостаточная значимость', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `fills`
--

DROP TABLE IF EXISTS `fills`;
CREATE TABLE IF NOT EXISTS `fills` (
  `id_ver` int NOT NULL,
  `id_plan` int NOT NULL,
  KEY `id_ver` (`id_ver`),
  KEY `id_plan` (`id_plan`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plan_num`
--

DROP TABLE IF EXISTS `plan_num`;
CREATE TABLE IF NOT EXISTS `plan_num` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `number_of_articles` int NOT NULL,
  `fulled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `prev_next_ver`
--

DROP TABLE IF EXISTS `prev_next_ver`;
CREATE TABLE IF NOT EXISTS `prev_next_ver` (
  `id_prev` int DEFAULT NULL,
  `id_next` int DEFAULT NULL,
  KEY `id_prev` (`id_prev`),
  KEY `id_next` (`id_next`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `prev_next_ver`
--

INSERT INTO `prev_next_ver` (`id_prev`, `id_next`) VALUES
(NULL, 1),
(NULL, 4),
(NULL, 5),
(NULL, 6),
(NULL, 7),
(NULL, 8),
(NULL, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `problem_list`
--

DROP TABLE IF EXISTS `problem_list`;
CREATE TABLE IF NOT EXISTS `problem_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `txt` text,
  `id_ver` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ver` (`id_ver`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `problem_list`
--

INSERT INTO `problem_list` (`id`, `txt`, `id_ver`) VALUES
(1, 'loh', 1),
(2, 'Awesome shit', 4),
(3, 'awesome stuff', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `problem_list_corr`
--

DROP TABLE IF EXISTS `problem_list_corr`;
CREATE TABLE IF NOT EXISTS `problem_list_corr` (
  `id` int NOT NULL AUTO_INCREMENT,
  `txt` text,
  `id_ver` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ver` (`id_ver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `redactor`
--

DROP TABLE IF EXISTS `redactor`;
CREATE TABLE IF NOT EXISTS `redactor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `passwd` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `redactor`
--

INSERT INTO `redactor` (`id`, `username`, `passwd`, `email`) VALUES
(1, 'redactor1', 'red1password', 'red1@mail.ru'),
(2, 'redactor2', 'red2password', 'red2@mail.ru');

-- --------------------------------------------------------

--
-- Структура таблицы `send_recive`
--

DROP TABLE IF EXISTS `send_recive`;
CREATE TABLE IF NOT EXISTS `send_recive` (
  `id_a` int NOT NULL,
  `id_ver` int NOT NULL,
  `sends` tinyint(1) DEFAULT NULL,
  KEY `id_a` (`id_a`),
  KEY `id_ver` (`id_ver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `send_recive`
--

INSERT INTO `send_recive` (`id_a`, `id_ver`, `sends`) VALUES
(1, 1, 1),
(1, 5, 1),
(1, 4, 1),
(1, 6, 1),
(4, 7, 1),
(3, 8, 1),
(3, 9, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `version`
--

DROP TABLE IF EXISTS `version`;
CREATE TABLE IF NOT EXISTS `version` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `stat` int NOT NULL,
  `dat` date NOT NULL,
  `version_number` int NOT NULL,
  `approved` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `version`
--

INSERT INTO `version` (`id`, `name`, `stat`, `dat`, `version_number`, `approved`) VALUES
(1, 'Hotor', 4, '2023-02-16', 1, 1),
(4, 'tyuy', 1, '2023-02-17', 1, NULL),
(5, 'Furry porn', 2, '2023-02-17', 1, NULL),
(6, 'sex', 1, '2023-02-17', 0, NULL),
(7, 'My pants', 1, '2023-03-05', 0, NULL),
(8, 'Cow turner', 4, '2023-03-17', 1, 1),
(9, 'trent', 2, '2023-03-19', 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `version_corrector`
--

DROP TABLE IF EXISTS `version_corrector`;
CREATE TABLE IF NOT EXISTS `version_corrector` (
  `id_ver` int NOT NULL,
  `id_cor` int NOT NULL,
  KEY `id_ver` (`id_ver`),
  KEY `id_cor` (`id_cor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `version_corrector`
--

INSERT INTO `version_corrector` (`id_ver`, `id_cor`) VALUES
(1, 1),
(1, 1),
(1, 1),
(8, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `version_redactor`
--

DROP TABLE IF EXISTS `version_redactor`;
CREATE TABLE IF NOT EXISTS `version_redactor` (
  `id_ver` int NOT NULL,
  `id_red` int NOT NULL,
  KEY `id_ver` (`id_ver`),
  KEY `id_red` (`id_red`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `version_redactor`
--

INSERT INTO `version_redactor` (`id_ver`, `id_red`) VALUES
(1, 1),
(7, 1),
(4, 2),
(6, 2),
(5, 1),
(8, 1),
(9, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
