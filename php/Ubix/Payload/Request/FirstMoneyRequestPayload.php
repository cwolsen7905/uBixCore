<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Float\UsdCurrency;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

// phpcs:ignore Ubix.Imports.UseAlias.InvalidAlias

/**
 * Payload object for getting parameters required and option from the API request.
 *
 * @see \Ubix\Tests\Payload\Request\FirstMoneyRequestTest PHPUnit test case
 * @see \Ubix\Tests\Payload\Request\FirstMoneyRequestPayloadTest PHPUnit test case
 */
final class FirstMoneyRequestPayload extends Payload implements RequestPayload
{
    public PlatformUserId $userId;

    public UsdCurrency $amount;

    public MpCode $envMpCode;

    public ?UbixDateTime $transactionDate;

    public ?Varchar $clickId;

    public ?Varchar $voluumClickId;

    /**
     * Constructor
     *
     * @param ?float  $amount           The amount
     * @param ?string $env_mp_code      The environment merchant processor code
     * @param ?string $transaction_date The transaction date
     * @param ?int    $user_id          The user ID
     * @param ?string $click_id         The click ID
     * @param ?string $voluum_click_id  The voluum click ID
     */
    public function __construct(
        ?float $amount,
        ?string $env_mp_code,
        ?string $transaction_date,
        ?int $user_id,
        ?string $click_id,
        ?string $voluum_click_id,
    ) {
        $this->validateAndMapField('amount', 'amount', $amount);
        $this->validateAndMapField('env_mp_code', 'envMpCode', $env_mp_code);
        $this->validateAndMapField('transaction_date', 'transactionDate', $transaction_date);
        $this->validateAndMapField('user_id', 'userId', $user_id);
        $this->validateAndMapField('click_id', 'clickId', $click_id);
        $this->validateAndMapField('voluum_click_id', 'voluumClickId', $voluum_click_id);

        parent::__construct();
    }
}
