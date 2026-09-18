<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Media;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a short-lived, single-object read URL
 *
 * Returned by {@see \Ubix\Service\Media\MediaStorageServiceInterface::createSignedReadUrl()}.
 * Deliberately carries only a URL and its expiry — never the object key or bucket it
 * was minted for — so passing this DTO on to a template or an API response cannot
 * leak more than the one thing the recipient is meant to have: a URL that stops
 * working on its own.
 *
 * @see \Ubix\Tests\DataTransferObject\Media\SignedUrlTest PHPUnit test case
 */
final readonly class SignedUrl implements Dto
{
    /**
     * Constructor
     *
     * @param string $url                    The signed, single-object URL
     * @param int    $expiresAtUnixTimestamp Unix timestamp after which the URL stops resolving; a caller must re-derive a fresh one rather than cache this past that point (platform TDS §9: signed read URLs are re-derived on every read, never persisted)
     */
    public function __construct(
        public readonly string $url = '',
        public readonly int $expiresAtUnixTimestamp = 0,
    ) {
    }
}
