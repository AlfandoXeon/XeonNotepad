-- ============================================================
-- Xeon Notepad — Database Schema v1.0.0
-- Import: mysql -u root -p < database/xeon_notepad.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Database
CREATE DATABASE IF NOT EXISTS `xeon_notepad`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `xeon_notepad`;

-- ---------------------------------------------------
-- Table: users
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)     NOT NULL,
  `email`      VARCHAR(150)    NOT NULL,
  `password`   VARCHAR(255)    NOT NULL COMMENT 'bcrypt cost-12 hash',
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------
-- Table: notes
-- All user content is AES-256-GCM encrypted.
-- Even the database owner cannot read note contents.
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS `notes` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `title_enc`   TEXT            NOT NULL COMMENT 'AES-256-GCM encrypted title (base64)',
  `content_enc` LONGTEXT        NOT NULL COMMENT 'AES-256-GCM encrypted HTML content (base64)',
  `title_iv`    VARCHAR(64)     NOT NULL COMMENT 'Base64 IV for title decryption',
  `content_iv`  VARCHAR(64)     NOT NULL COMMENT 'Base64 IV for content decryption',
  `is_deleted`  TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Soft delete: 1 = deleted',
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id`    (`user_id`),
  KEY `idx_is_deleted` (`is_deleted`),
  CONSTRAINT `fk_notes_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------
-- Table: note_histories
-- Encrypted change log per note.
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS `note_histories` (
  `id`            BIGINT UNSIGNED                       NOT NULL AUTO_INCREMENT,
  `note_id`       BIGINT UNSIGNED                       NOT NULL,
  `user_id`       BIGINT UNSIGNED                       NOT NULL,
  `action`        ENUM('created','edited','deleted')    NOT NULL,
  `diff_desc_enc` TEXT                                  NOT NULL COMMENT 'Encrypted change description (base64)',
  `diff_desc_iv`  VARCHAR(64)                           NOT NULL COMMENT 'Base64 IV for description decryption',
  `created_at`    TIMESTAMP                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_note_id` (`note_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_history_note`
    FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
