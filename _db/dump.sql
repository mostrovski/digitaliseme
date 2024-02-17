-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Jan 22, 2024 at 05:47 PM
-- Server version: 10.4.32-MariaDB-1:10.4.32+maria~ubu2004-log
-- PHP Version: 8.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
    `id` int(10) UNSIGNED NOT NULL,
    `title` varchar(150) NOT NULL,
    `type` varchar(150) NOT NULL,
    `issue_date` date NOT NULL,
    `issuer_id` int(10) UNSIGNED DEFAULT NULL,
    `storage_id` int(10) UNSIGNED DEFAULT NULL,
    `user_id` int(10) UNSIGNED DEFAULT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_keywords`
--

CREATE TABLE `document_keywords` (
    `document_id` int(10) UNSIGNED NOT NULL,
    `keyword_id` int(10) UNSIGNED NOT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
    `id` int(10) UNSIGNED NOT NULL,
    `filename` varchar(100) NOT NULL,
    `path` varchar(100) NOT NULL,
    `document_id` int(10) UNSIGNED DEFAULT NULL,
    `user_id` int(10) UNSIGNED DEFAULT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issuers`
--

CREATE TABLE `issuers` (
    `id` int(10) UNSIGNED NOT NULL,
    `name` varchar(32) NOT NULL,
    `email` varchar(32) NOT NULL,
    `phone` varchar(32) NOT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE `keywords` (
    `id` int(10) UNSIGNED NOT NULL,
    `word` varchar(32) NOT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_places`
--

CREATE TABLE `storage_places` (
    `id` int(10) UNSIGNED NOT NULL,
    `place` varchar(50) NOT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
    `id` int(10) UNSIGNED NOT NULL,
    `first_name` varchar(32) NOT NULL,
    `last_name` varchar(32) NOT NULL,
    `email` varchar(32) NOT NULL,
    `username` varchar(32) NOT NULL,
    `password` varchar(255) NOT NULL,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
    ADD PRIMARY KEY (`id`),
    ADD KEY `foreign_document_issuer` (`issuer_id`),
    ADD KEY `foreign_document_storage` (`storage_id`),
    ADD KEY `foreign_document_user` (`user_id`);

--
-- Indexes for table `document_keywords`
--
ALTER TABLE `document_keywords`
    ADD PRIMARY KEY (`document_id`,`keyword_id`),
    ADD KEY `foreign_document_pivot` (`document_id`),
    ADD KEY `foreign_keyword_pivot` (`keyword_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `unique_filepath` (`path`),
    ADD KEY `foreign_file_document` (`document_id`),
    ADD KEY `foreign_file_user` (`user_id`);

--
-- Indexes for table `issuers`
--
ALTER TABLE `issuers`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `unique_issuer_name` (`name`);

--
-- Indexes for table `keywords`
--
ALTER TABLE `keywords`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `unique_keyword` (`word`);

--
-- Indexes for table `storage_places`
--
ALTER TABLE `storage_places`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `unique_storage_place` (`place`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `unique_user_name` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issuers`
--
ALTER TABLE `issuers`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keywords`
--
ALTER TABLE `keywords`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storage_places`
--
ALTER TABLE `storage_places`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
    ADD CONSTRAINT `foreign_document_issuer` FOREIGN KEY (`issuer_id`) REFERENCES `issuers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `foreign_document_storage` FOREIGN KEY (`storage_id`) REFERENCES `storage_places` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `foreign_document_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `document_keywords`
--
ALTER TABLE `document_keywords`
    ADD CONSTRAINT `foreign_document_pivot` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `foreign_keyword_pivot` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
    ADD CONSTRAINT `foreign_file_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `foreign_file_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
