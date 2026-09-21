-- Migration: 20260921000000_create_pii_access_audits
-- Database: SYSTEMS
-- Description: SYSTEMS.Pii_Access_Audits, the personal-data access trail
--              docs/standards/sensitive-data-access.md requires, written only
--              through Ubix\Service\Audit\PiiAccessAuditService.
--
--              Reference copy: a host applies this from its own
--              sql/migrations/, as it does the Schema_Migrations init.
--
--              entity_type is VARCHAR(32), NOT the standard's
--              ENUM('customer','affiliate','broadcaster','model'). Those are
--              one product's subject domains; a framework table naming them
--              could not serve any other product. Each host defines its own
--              values. Every other column follows the standard as written.
--
--              Append-only: rows are inserted, never updated or deleted,
--              except by the scheduled retention purge. No date_last_updated,
--              the documented omission for an event table.
-- Author: Christopher W. Olsen

CREATE TABLE SYSTEMS.Pii_Access_Audits (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id     INT UNSIGNED    NOT NULL,
    entity_type  VARCHAR(32)     NOT NULL,
    subject_id   INT UNSIGNED    NOT NULL,
    search_term  VARCHAR(255)    NULL,
    reason       VARCHAR(100)    NOT NULL,
    date_created DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- "What did this operator look at, and when."
    KEY idx_admin_id (admin_id, date_created),
    -- "Who has looked at this person." One row per subject makes this a plain
    -- indexed lookup, the query a CSV-blob design cannot answer.
    KEY idx_entity_type_subject_id (entity_type, subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
