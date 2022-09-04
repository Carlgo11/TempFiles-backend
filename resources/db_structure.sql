-- Create USER

--
-- Create Database
--
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
CREATE DATABASE IF NOT EXISTS `tempfiles` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `tempfiles`;

--
-- Create Table: Main
--
CREATE TABLE IF NOT EXISTS `main`
(
    `id`      varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `expiry`  varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `views`   tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
    `delpass` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

--
-- Create Table: Content
--
CREATE TABLE IF NOT EXISTS `content`
(
    `id`   varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `part` tinyint                                                      NOT NULL,
    `data` mediumblob                                                   NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

--
-- Create Table: MetaData
--
CREATE TABLE IF NOT EXISTS `metadata`
(
    `id`   varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `part` tinyint                                                      NOT NULL,
    `data` mediumtext                                                   NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;
