<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Realtime;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a verified real-time subject grant
 *
 * What {@see \Ubix\Service\Realtime\SubjectGrantService::verify()} returns: a
 * short-lived statement, signed by the host, that `holder` may subscribe to
 * `subjects`. A gateway holding browser sockets trusts only this; it never
 * decides access itself.
 *
 * @see \Ubix\Tests\DataTransferObject\Realtime\SubjectGrantTest PHPUnit test case
 */
final readonly class SubjectGrant implements Dto
{
    /**
     * Constructor
     *
     * @param string             $holder    Who the grant was issued to (the host's user id, as a string)
     * @param array<int, string> $subjects  Subject patterns the holder may subscribe to; a final `>` token is a trailing wildcard
     * @param int                $issuedAt  Unix time the grant was signed
     * @param int                $expiresAt Unix time after which the grant is refused
     */
    public function __construct(
        public readonly string $holder = '',
        public readonly array $subjects = [],
        public readonly int $issuedAt = 0,
        public readonly int $expiresAt = 0,
    ) {
    }
}
