-- Migration: 20260901000000_create_creators_and_slug_history
-- Database: sowingme
-- Description: creators + creator_slug_history tables (M0-06; creator-profile TDS §2)
--              creators is 1:1 with users (FR-101); organization_id and
--              payout_account_id are reserved nullable columns with no FK yet
--              (ADR-007 / payouts M2). Slug redirects resolve via
--              creator_slug_history (FR-203).
-- Author: Christopher W. Olsen

CREATE TABLE sowingme.creators (
    id                INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id           INT(10) UNSIGNED NOT NULL,
    slug              VARCHAR(64) NOT NULL,
    display_name      VARCHAR(120) NOT NULL,
    bio               TEXT NULL,
    avatar_url        VARCHAR(500) NULL,
    banner_url        VARCHAR(500) NULL,
    category          ENUM('pastor','worship','teacher','podcaster','author','artist','other') NOT NULL DEFAULT 'other',
    faith_topic       VARCHAR(120) NULL,
    external_links    JSON NULL,
    organization_id   BIGINT UNSIGNED NULL,
    payout_account_id BIGINT UNSIGNED NULL,
    status            ENUM('draft','active','suspended') NOT NULL DEFAULT 'draft',
    published_at      DATETIME NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_creators_user_id (user_id),
    UNIQUE KEY uniq_creators_slug (slug),
    KEY idx_creators_status (status),
    CONSTRAINT fk_creators_user_id FOREIGN KEY (user_id) REFERENCES sowingme.users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sowingme.creator_slug_history (
    id         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    creator_id INT(10) UNSIGNED NOT NULL,
    old_slug   VARCHAR(64) NOT NULL,
    retired_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_creator_slug_history_old_slug (old_slug),
    KEY idx_creator_slug_history_creator_id (creator_id),
    CONSTRAINT fk_creator_slug_history_creator_id FOREIGN KEY (creator_id) REFERENCES sowingme.creators (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
