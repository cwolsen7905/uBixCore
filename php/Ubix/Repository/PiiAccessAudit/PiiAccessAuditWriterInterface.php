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

    /**
     * Delete access records written before a moment
     *
     * The trail is append-only in normal use; this is the one exception, and it
     * exists because keeping a record of who looked at someone's data forever is
     * itself a privacy problem. **How long is the host's policy, not the
     * framework's**: a host schedules this with its own retention window, which
     * is the window its privacy notice promises.
     *
     * @param string $cutoff Delete rows created before this, `Y-m-d H:i:s`
     * @param int    $limit  Most rows in one call, so a first run on a long
     *                       history does not hold one enormous transaction.
     *                       A caller with a backlog calls this repeatedly.
     *
     * @return void
     */
    public function purgeAccessesBefore(string $cutoff, int $limit = 10000): void;
}
