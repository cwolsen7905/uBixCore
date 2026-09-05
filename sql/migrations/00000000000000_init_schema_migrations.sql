-- Migration: 00000000000000_init_schema_migrations
-- Database: SYSTEMS
-- Description: Bootstrap the master Schema_Migrations tracker that records
--              every applied migration across every Ubix-consumed
--              database. Special-cased by the runner — applied directly
--              when the table does not yet exist, then self-recorded as
--              the first row.
-- Author: Christopher W. Olsen

CREATE TABLE SYSTEMS.Schema_Migrations (
    id                VARCHAR(96)   NOT NULL,
    target_database   VARCHAR(64)   NOT NULL,
    description       TEXT          NOT NULL,
    checksum          CHAR(64)      NOT NULL,
    applied_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    applied_by        VARCHAR(64)   NOT NULL,
    duration_ms       INT UNSIGNED  NOT NULL,
    date_created      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_last_updated DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_target_database (target_database),
    KEY idx_applied_at (applied_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
