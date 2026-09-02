<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Model\Creator;

/**
 * The outcome of resolving a public creator slug (creator-profile TDS §3)
 *
 * Exactly one shape applies: a live creator (found), a retired slug pointing at
 * the current one (redirect), or neither (not found).
 *
 * @see \Ubix\Tests\DataTransferObject\CreatorProfileResolutionTest PHPUnit test case
 */
final readonly class CreatorProfileResolution implements Dto
{
    /**
     * Constructor
     *
     * @param ?Creator $creator        The live creator, when the slug resolves directly (default: null)
     * @param ?string  $redirectToSlug The current slug, when the requested one is retired (default: null)
     */
    public function __construct(
        public readonly ?Creator $creator = null,
        public readonly ?string $redirectToSlug = null,
    ) {
    }
}
