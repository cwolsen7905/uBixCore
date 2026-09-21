<?php

declare(strict_types=1);

namespace Ubix\Repository\PiiAccessAudit;

use Ubix\DataTransferObject\Audit\PiiAccess;

/**
 * Writes the personal-data access trail
 *
 * Append-only. There is no update and no delete here: an audit trail that can
 * be edited by the code it audits is not an audit trail. Retention purges are
 * a scheduled job outside this interface.
 */
interface PiiAccessAuditWriterInterface
{
    /**
     * Record one access, writing one row per subject
     *
     * One row per subject, never a list in one row, so "who accessed this
     * person" is a plain indexed query.
     *
     * @param PiiAccess $access The access to record
     *
     * @return void
     */
    public function recordAccess(PiiAccess $access): void;
}
