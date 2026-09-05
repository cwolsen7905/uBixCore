<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a Slack API response
 *
 * @see \Ubix\Tests\DataTransferObject\SlackResponseTest PHPUnit test case
 */
final readonly class SlackResponse implements Dto
{
    /**
     * Constructor
     *
     * @param string $response The response from the Slack API
     */
    public function __construct(
        public readonly string $response,
    ) {
    }
}
