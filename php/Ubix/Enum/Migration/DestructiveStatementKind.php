<?php

declare(strict_types=1);

namespace Ubix\Enum\Migration;

/**
 * Categories of destructive SQL statements detected in migration
 * file bodies. Matches the list in `docs/standards/migrations.md`
 * §11.1.
 *
 * The string value is rendered into the parser's failure message
 * when a destructive migration omits its `Destructive:` header,
 * e.g. *"Migration `<id>` contains DROP TABLE on line 12 but
 * lacks a `Destructive:` header."*
 *
 * @see \Ubix\Tests\Enum\Migration\DestructiveStatementKindTest PHPUnit test case
 */
enum DestructiveStatementKind: string
{
    case ALTER_TABLE_DROP_COLUMN = 'ALTER TABLE … DROP COLUMN';
    case ALTER_TABLE_MODIFY      = 'ALTER TABLE … MODIFY';
    case DELETE_FROM_NO_WHERE    = 'DELETE FROM (no WHERE clause)';
    case DROP_DATABASE           = 'DROP DATABASE';
    case DROP_INDEX              = 'DROP INDEX';
    case DROP_TABLE              = 'DROP TABLE';
    case DROP_VIEW               = 'DROP VIEW';
    case RENAME_TABLE            = 'RENAME TABLE';
    case TRUNCATE_TABLE          = 'TRUNCATE TABLE';
}
