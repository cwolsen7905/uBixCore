<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the result of a string being filtered
 *
 * @see \Ubix\Service\FilterService This DTO is used by the filter service
 * @see \Ubix\Tests\DataTransferObject\FilterStringResultTest PHPUnit test case
 */
final readonly class FilterStringResult implements Dto
{
    /**
     * Constructor
     *
     * @param string    $original          The original string
     * @param string    $originalFormatted The original string formatted for the filter API
     * @param ?string   $filtered          The filtered string or null if not filtered (optional) (default: null)
     * @param ?bool     $isSpam            Whether the string is considered spam or null if not determined (optional) (default: null)
     * @param ?bool     $wasFiltered       Whether the string was filtered or null if not determined (optional) (default: null)
     * @param ?string[] $filteredWords     An array of words that were filtered or null if not determined (optional) (default: null)
     */
    public function __construct(
        public readonly string $original,
        public readonly string $originalFormatted,
        public readonly ?string $filtered = null,
        public readonly ?bool $isSpam = null,
        public readonly ?bool $wasFiltered = null,
        public readonly ?array $filteredWords = null,
    ) {
    }
}
