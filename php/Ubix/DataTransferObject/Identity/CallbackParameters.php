<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Identity;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for what a provider sent back to the callback URL
 *
 * Built by the host from the query string (Google, Facebook) or the POST body
 * (Apple's `response_mode=form_post`). Everything here is attacker-controllable
 * until {@see \Ubix\Service\Identity\AuthorizationFlowService::takeForCallback()}
 * has matched `state` against the stored flow.
 *
 * @see \Ubix\Tests\DataTransferObject\Identity\CallbackParametersTest PHPUnit test case
 */
final readonly class CallbackParameters implements Dto
{
    /**
     * Constructor
     *
     * @param string $code     The authorization code to exchange, or empty when the provider returned an error
     * @param string $state    The state value the provider echoed back
     * @param string $error    The provider's error code (for example `access_denied` when the user cancelled), or empty
     * @param string $userJson Apple only: the unsigned `user` form field carrying the name on first authorization, or empty
     */
    public function __construct(
        public readonly string $code = '',
        public readonly string $state = '',
        public readonly string $error = '',
        public readonly string $userJson = '',
    ) {
    }
}
