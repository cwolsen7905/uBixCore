<?php

declare(strict_types=1);

namespace Ubix\Exception;

use Exception;
use Throwable;
use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Exception that includes an optional DTO object
 *
 * Throw example:
 * ```
 * throw new \Ubix\Exception\DtoException(
 *     'Message that is safe to be displayed to customers.',
 *     \Ubix\Enum\ExceptionCode::EXCEPTION_CODE->value, // This code is for VSM developers only
 *     new \Ubix\DataTransferObject\Example(
 *         one:   'first',
 *         two:   'second',
 *         three: 'third',
 *     ),
 * );
 * ```
 *
 * Catch example:
 * ```
 * try {
 *     doSomething();
 * } catch (\Exception $e) {
 *     if ($e instanceof \Ubix\Exception\DtoException) {
 *         var_dump($e->getDto());
 *     }
 * }
 * ```
 *
 * @see \Ubix\Tests\Exception\DtoExceptionTest PHPUnit test case
 */
final class DtoException extends Exception
{
    /**
     * Construtor
     *
     * @param string         $message  The Exception message to throw (optional) (default: '')
     * @param int            $code     The Exception code (optional) (default: 0)
     * @param Dto[]|Dto|null $dto      A DTO object or an array of DTO objects (optional) (default: null)
     * @param ?Throwable     $previous The previous throwable used for the exception chaining (optional) (default: null)
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        private readonly array|Dto|null $dto = null,
        ?Throwable $previous = null,
    ) {
        //
        //  Call the Exception constructor
        //
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the DTO object (or array of DTO objects)
     *
     * @return Dto[]|Dto|null A DTO object, an array of DTO objects or null
     */
    public function getDto(): array|Dto|null
    {
        return $this->dto;
    }
}
