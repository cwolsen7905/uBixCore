<?php

declare(strict_types=1);

namespace Ubix\Enum;

/**
 * The 53 Ubix-consumed MySQL databases — one case per
 * `sql/<DB>.sql` baseline dump. Used by every repository that
 * needs to reference a database by name in SQL.
 *
 * Two goals:
 *
 * 1. **Eliminate stringly-typed `<DB>.<Table>` literals across the
 *    repository layer.** Hardcoded names like `'ntl_db.optiusers'`
 *    or `'SYSTEMS.Feature_Flag_Rules'` were sprinkled across 174
 *    query sites in 55 repository files at the time this enum was
 *    introduced. Replacing them with `UbixDatabase::NTL_DB->databaseName()`
 *    and `UbixDatabase::SYSTEMS->databaseName()` makes the database
 *    vocabulary explicit, IDE-checkable, and grep-friendly.
 * 2. **Enable test-DB isolation via the `DATABASE_PREFIX` env var.**
 *    `databaseName()` reads the prefix lazily, so a test bootstrap that
 *    stamps `DATABASE_PREFIX=TEST_` redirects every repository
 *    query to the `TEST_<DB>` parallel schemas without changing a
 *    single repository line. The same env var also drives the
 *    migration runner's tracker schema
 *    (`SchemaMigrationSqlRepository::trackerTable()`) and migration
 *    body rewrite at apply time, so the test set is materialised
 *    coherently end-to-end.
 *
 * The case names use the upper-snake-case PHP convention; the
 * string values preserve the exact on-disk database name
 * (case-sensitive on Linux MySQL — `Content_Consoles` and
 * lowercase `ntl_db` / `flirt4free` etc. are intentional).
 *
 * @see \Ubix\Tests\Enum\UbixDatabaseTest PHPUnit test case
 */
enum UbixDatabase: string
{
    case ADSERVER         = 'ADSERVER';
    case ASIA             = 'ASIA';
    case BI               = 'BI';
    case BILLING          = 'BILLING';
    case CHAT_SYSTEM      = 'CHAT_SYSTEM';
    case CHAT_SYSTEM_LOG  = 'CHAT_SYSTEM_LOG';
    case CLUB             = 'CLUB';
    case CONSOLES         = 'CONSOLES';
    case CONSOLES_USAGE   = 'CONSOLES_USAGE';
    case CONTENT_CONSOLES = 'Content_Consoles';
    case CONVERGENCE      = 'CONVERGENCE';
    case DEVELOPERS       = 'DEVELOPERS';
    case DOCUMENTATION    = 'DOCUMENTATION';
    case FAN_CLUBS        = 'FAN_CLUBS';
    case FANCLUB          = 'fanclub';
    case FLIRT4FREE       = 'flirt4free';
    case FLIRT_PHONE      = 'FLIRT_PHONE';
    case FLIRT_REWARDS    = 'FLIRT_REWARDS';
    case FLIRTGO          = 'FLIRTGO';
    case FLIRTSMS         = 'FLIRTSMS';
    case FORUMS           = 'FORUMS';
    case GAY              = 'GAY';
    case GROUPS           = 'GROUPS';
    case HCMM             = 'HCMM';
    case LIVE_BIZ         = 'live_biz';
    case MAILINGS         = 'MAILINGS';
    case MEMBER_SITES     = 'member_sites';
    case MESSAGING        = 'MESSAGING';
    case METRICS          = 'METRICS';
    case MODEL_ACCESS     = 'MODEL_ACCESS';
    case MODEL_CLIPS      = 'MODEL_CLIPS';
    case NTL_DB           = 'ntl_db';
    case PARTNERS_API     = 'PARTNERS_API';
    case PSYCHIC          = 'PSYCHIC';
    case RDBA             = 'rdba';
    case RDBA_RESTORE     = 'rdba_restore';
    case RECOVER          = 'RECOVER';
    case SE_MATRIX        = 'SE_MATRIX';
    case SMS              = 'SMS';
    case STUDIOS          = 'STUDIOS';
    case STUDIOS_STATS    = 'STUDIOS_STATS';
    case SYSTEMS          = 'SYSTEMS';
    case TEMP             = 'TEMP';
    case TEMPORARY        = 'temporary';
    case TGP              = 'TGP';
    case TUBE             = 'TUBE';
    case VIDEOCHAT        = 'VIDEOCHAT';
    case VOD              = 'VOD';
    case VSCASH           = 'VSCASH';
    case VSCASH_STATS     = 'VSCASH_STATS';
    case VSM              = 'VSM';
    case XVC              = 'XVC';
    case XVP              = 'XVP';

    /**
     * Resolve the database name for use in SQL queries, applying
     * the `DATABASE_PREFIX` env var if it's set. Read lazily so a
     * CLI `--prefix=<P>` (stamped via
     * `MigrationConnectionTargetService::apply()`) or a test
     * bootstrap that does `putenv('DATABASE_PREFIX=TEST_')` takes
     * effect for queries built AFTER the stamp.
     *
     * Method is `databaseName()` rather than `name()` because PHP
     * enums auto-expose a `name` readonly property carrying the
     * case name (`UbixDatabase::NTL_DB->name === 'NTL_DB'`),
     * which would conflict with a same-named method.
     *
     * Examples (without prefix):
     *
     *     UbixDatabase::NTL_DB->databaseName();   // 'ntl_db'
     *     UbixDatabase::SYSTEMS->databaseName();  // 'SYSTEMS'
     *
     * Examples (with `DATABASE_PREFIX=TEST_`):
     *
     *     UbixDatabase::NTL_DB->databaseName();   // 'TEST_ntl_db'
     *     UbixDatabase::SYSTEMS->databaseName();  // 'TEST_SYSTEMS'
     *
     * @return string Database name with the active prefix applied
     */
    public function databaseName(): string
    {
        $prefix = (string) getenv('DATABASE_PREFIX');
        return $prefix . $this->value;
    }
}
