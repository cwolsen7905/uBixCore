-- Migration: 20260901120000_create_password_reset_tokens
-- Database: sowingme
-- Description: password_reset_tokens table (M1-01; authentication TDS §2.2)
--              Mirrors email_confirmation_tokens but stores a SHA-256
--              token_hash instead of the raw token — a reset token changes a
--              credential, so plaintext only ever appears in the emailed URL.
-- Author: Christopher W. Olsen

CREATE TABLE sowingme.password_reset_tokens (
    id         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT(10) UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at    DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_password_reset_tokens_token_hash (token_hash),
    KEY idx_password_reset_tokens_user_id (user_id),
    CONSTRAINT fk_password_reset_tokens_user_id FOREIGN KEY (user_id) REFERENCES sowingme.users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
