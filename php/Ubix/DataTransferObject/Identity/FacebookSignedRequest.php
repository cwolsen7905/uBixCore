<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Identity;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a Meta `signed_request` whose signature has been verified
 *
 * Built only by {@see \Ubix\Service\Identity\FacebookSignedRequestParserService} after
 * the HMAC-SHA256 signature checked out against the app secret, so a host
 * handler taking one cannot be handed a user id straight off a request.
 *
 * Meta posts one to the app's **data-deletion callback** and its **deauthorize
 * callback**. What to delete, disable or record is the host's decision; the
 * user id is Facebook's app-scoped id, the same value as
 * {@see VerifiedIdentity::$subject} for a Facebook sign-in.
 *
 * @see \Ubix\Tests\DataTransferObject\Identity\FacebookSignedRequestTest PHPUnit test case
 */
final readonly class FacebookSignedRequest implements Dto
{
    /**
     * Constructor
     *
     * @param string $userId                Facebook's app-scoped user id the request is about
     * @param int    $issuedAtUnixTimestamp When Meta issued the request (`issued_at`), or 0 when absent
     */
    public function __construct(
        public readonly string $userId = '',
        public readonly int $issuedAtUnixTimestamp = 0,
    ) {
    }
}
