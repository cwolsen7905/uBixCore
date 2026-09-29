<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Markdown;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * What a host permits when it renders someone's Markdown
 *
 * Consumed by `SafeMarkdownService::render()`. The service owns the *mechanism*
 * — parse, then enforce an allow-list over the result — and this owns the
 * *policy*, because what is acceptable in a comment, a profile page and a
 * README are three different answers and the framework has no view on which.
 *
 * `allowedTags` is an allow-list, deliberately: the set of dangerous tags is
 * unbounded and grows, the set a given surface needs is small and known.
 *
 * @see \Ubix\Tests\DataTransferObject\Markdown\MarkdownPolicyTest PHPUnit test case
 */
final readonly class MarkdownPolicy implements Dto
{
    /**
     * Constructor
     *
     * @param array<int, string> $allowedTags       Lower-case HTML tag names to keep. Anything else is unwrapped to its text, or dropped outright when it can carry script. An empty list keeps nothing but text.
     * @param string             $linkRel           The `rel` to force on every `<a>`; `''` sets none. User-generated links usually want `nofollow ugc noopener noreferrer`.
     * @param string             $linkTarget        The `target` to force on every `<a>`; `''` sets none.
     * @param int                $minHeadingLevel   Headings shallower than this are pushed down to it, so a document cannot outrank the page's own `<h1>`. `1` leaves them alone.
     * @param array<int, string> $allowedUriSchemes Schemes permitted in `href`, lower-case and without `:`. A scheme-relative or relative URL is always allowed; anything else with a scheme outside this list loses its `href`.
     */
    public function __construct(
        public array $allowedTags,
        public string $linkRel = '',
        public string $linkTarget = '',
        public int $minHeadingLevel = 1,
        public array $allowedUriSchemes = ['http', 'https', 'mailto'],
    ) {
    }
}
