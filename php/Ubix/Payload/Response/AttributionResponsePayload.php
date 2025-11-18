<?php

declare(strict_types=1);

namespace Ubix\Payload\Response;

use Ubix\DataType\String\MpCode;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\ResponsePayloadInterface as ResponsePayload;

// phpcs:ignore Ubix.Imports.UseAlias.InvalidAlias

/**
 * Data transfer object for a Attribution API response
 *
 * @see \Ubix\Tests\Payload\Response\AttributionResponsePayloadTest PHPUnit test case
 */
final class AttributionResponsePayload extends Payload implements ResponsePayload
{
    public string $message;

    public int $code;

    public string $mp_code;

    /**
     * Constructor
     *
     * @param string  $message The response message
     * @param int     $code    The response code
     * @param ?MpCode $mpCode  The mp_code involved in the attribution
     */
    public function __construct(
        string $message,
        int $code,
        ?MpCode $mpCode = null,
    ) {
        $this->message = $message;
        $this->code    = $code;
        $this->mp_code = $mpCode->value ?? '';
    }
}
