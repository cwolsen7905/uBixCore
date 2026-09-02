-- Migration: 20260902030000_create_tiers_and_subscriptions
-- Database: sowingme
-- Description: tiers, tier_benefits and subscriptions tables (M1-04;
--              subscription-tiers TDS §2). Money is minor units + ISO currency
--              (FR-104). position 0 is the implicit free tier and never stored.
--              subscriptions.active_key is a generated column that is NULL for
--              terminal statuses, giving MariaDB a partial-unique equivalent:
--              at most one non-terminal row per (user_id, creator_id) (FR-302).
-- Author: Christopher W. Olsen

CREATE TABLE sowingme.tiers (
    id               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    creator_id       INT(10) UNSIGNED NOT NULL,
    name             VARCHAR(80) NOT NULL,
    description      TEXT NULL,
    price_amount     INT NOT NULL,
    price_currency   CHAR(3) NOT NULL DEFAULT 'USD',
    billing_interval ENUM('month','year') NOT NULL DEFAULT 'month',
    position         SMALLINT UNSIGNED NOT NULL,
    status           ENUM('active','archived') NOT NULL DEFAULT 'active',
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_tiers_creator_position (creator_id, position),
    KEY idx_tiers_creator_status (creator_id, status),
    CONSTRAINT fk_tiers_creator_id FOREIGN KEY (creator_id) REFERENCES sowingme.creators (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sowingme.tier_benefits (
    id          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    tier_id     INT(10) UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    position    SMALLINT UNSIGNED NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_tier_benefits_tier_position (tier_id, position),
    CONSTRAINT fk_tier_benefits_tier_id FOREIGN KEY (tier_id) REFERENCES sowingme.tiers (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sowingme.subscriptions (
    id                       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id                  INT(10) UNSIGNED NOT NULL,
    creator_id               INT(10) UNSIGNED NOT NULL,
    tier_id                  INT(10) UNSIGNED NOT NULL,
    status                   ENUM('active','past_due','canceled','expired') NOT NULL DEFAULT 'active',
    provider_subscription_id VARCHAR(255) NULL,
    provider_customer_id     VARCHAR(255) NULL,
    current_period_end       DATETIME NULL,
    canceled_at              DATETIME NULL,
    active_key               VARCHAR(64) GENERATED ALWAYS AS (
        CASE WHEN status IN ('active','past_due') THEN CONCAT(user_id, ':', creator_id) ELSE NULL END
    ) STORED,
    created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_subscriptions_active_key (active_key),
    KEY idx_subscriptions_user_id (user_id),
    KEY idx_subscriptions_creator_status (creator_id, status),
    CONSTRAINT fk_subscriptions_user_id FOREIGN KEY (user_id) REFERENCES sowingme.users (id),
    CONSTRAINT fk_subscriptions_creator_id FOREIGN KEY (creator_id) REFERENCES sowingme.creators (id),
    CONSTRAINT fk_subscriptions_tier_id FOREIGN KEY (tier_id) REFERENCES sowingme.tiers (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
