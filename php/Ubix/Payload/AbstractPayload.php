<?php

declare(strict_types=1);

namespace Ubix\Payload;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use TypeError;
use ValueError;
use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\DataTransferObject\PayloadError;
use Ubix\Exception\DtoException;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;
use Ubix\Payload\ResponsePayloadInterface as ResponsePayload;

/**
 * Abstract payload request for use.
 */
abstract class AbstractPayload implements RequestPayload, ResponsePayload
{
     /**
      * @var array<array{name: string, error: string}> $errors
      */
    private array $errors = [];

    private static ?Serializer $serializer = null;

    /**
     * Construct and die if there are validation errors
     *
     * @throws DtoException If there are validation errors
     */
    public function __construct()
    {
        if (count($this->errors) > 0) {
            throw new DtoException(message: 'There are issues with your input value(s).', dto: new PayloadError(count: count($this->errors), errors: $this->errors));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validateAndMapField(string $name, string $dest, mixed $raw): void
    {
        $property = $this->getProperty($dest);


        try {
            if ($raw === null && $property['allowNull']) {
                $this->{$dest} = null;
            } elseif ($property['isBuiltin']) {
                $this->{$dest} = $raw;
            } elseif ($property['isEnum']) {
                $this->{$dest} = $property['dataType']::from($raw);
            } else {
                $this->{$dest} = new $property['dataType']($raw);
            }
        } catch (TypeError $e) {
            $this->errors[] = ['name' => $name, 'error' => 'Cannot be of type ' . gettype($raw)];
        } catch (ValueError $e) {
            $this->errors[] = ['name' => $name, 'error' => '"' . (gettype($raw) === 'string' ? $raw : 'NA') . '" is not a valid backing value.'];
        } catch (Exception $e) {
            $this->errors[] = ['name' => $name, 'error' => $e->getMessage()];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseData(): array
    {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        /**
         * @var array<string, mixed> $responseData
         */
        $responseData = [];

        foreach ($properties as $property) {
            $responseData[$property->getName()] = $property->getValue($this);
        }


        return $responseData;
    }

    /**
     * {@inheritDoc}
     */
    public static function getResponse(Dto $dto): ResponsePayload
    {
        $vars = get_object_vars($dto);  // phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
        return new static(...$vars);  // @phpstan-ignore-line
    }

    /**
     * {@inheritDoc}
     *
     * @throws DtoException If there are deserialization errors
     * @throws Exception If there are type errors during deserialization
     */
    public static function getRequest(Request $request): RequestPayload
    {
        $serializer = self::getSerializer();

        try {
            $payload = $serializer->deserialize($request->getBody()->getContents(), static::class, 'json');
        } catch (TypeError $e) {
            // Catch TypeErrors from invalid payloads and rethrow as Exceptions
            // Transform messages like:
            // "Ubix\\DataType\\MpCode::__construct(): Argument #1 ($input) must be of type string, null given, called in /path on line 52"
            // into: "MpCode must be of type string not null"
            $original = $e->getMessage();

            $formatted = null;

            // Pattern captures: fully-qualified class, expected type, given type
            $pattern = '/\\\\?([A-Za-z0-9_]+)::\\__construct\(\): Argument #\d+ \(\$[a-zA-Z0-9_]+\) must be of type ([^,]+), ([^ ]+) given/';

            if (preg_match($pattern, $original, $m)) {
				// phpcs:disable
				// $m[1] = short class name, $m[2] = expected type, $m[3] = given type
				// phpcs:enable
                $class    = $m[1];
                $expected = trim($m[2]);
                $given    = trim($m[3]);

				//phpcs:disable
				// Normalize 'null' (or 'NULL') and other values
				// phpcs:enable
                $given = strtolower($given) === 'null' ? 'null' : $given;

                $formatted = sprintf('%s must be of type %s not %s', $class, $expected, $given);
            }

            // Fallback: attempt a simpler replace similar to previous behaviour
            if ($formatted === null) {
                $formatted = str_replace(
                    ['Ubix\\DataType\\', '::__construct(): Argument #1 ($input)', ', called in ', ' on line '],
                    ['', '', ' not ', ''],
                    $original,
                );
            }

            throw new Exception($formatted);
        } catch (NotEncodableValueException $e) {
            throw new DtoException(message: 'Payload deserialization failed: ' . $e->getMessage(), code: 400, dto: [], previous: $e);
        }

        assert($payload instanceof RequestPayload);

        return $payload;
    }

    /**
     * Look up a property and return the data type and if it's nullable
     *
     * @param string $name The name of the property
     *
     * @return array{
     *     dataType: string,
     *     allowNull: bool,
     *     isBuiltin: bool,
     *     isEnum: bool,
     *     enumBacked: bool
     * }
     *
     * @throws Exception If the property does not exist or the enum class does not exist
     */
    private function getProperty(string $name): array
    {
        $scalarNames = ['int', 'float', 'string', 'bool'];

        $rc = new ReflectionClass(get_class($this));

        try {
            $property = $rc->getProperty($name);
        } catch (Exception $e) {
            throw new Exception('Property ' . $name . ' does not exist.');
        }

        assert($property !== null && $property->getType() !== null && $property->getType() instanceof ReflectionNamedType);
        $className = $property->getType()->getName();
        $allowNull = $property->getType()->allowsNull();
        $isBuiltin = in_array($className, $scalarNames, true);

        $isEnum     = false;
        $enumBacked = false;
        if (! $isBuiltin) {
            if (is_string($className) && class_exists($className)) {
                $typeRc = new ReflectionClass($className);
                $isEnum = $typeRc->isEnum();
                if ($isEnum) {
                    // Backed enums implement methods tryFrom/from; check backing type
                    $enumBacked = $typeRc->hasMethod('tryFrom') || $typeRc->hasMethod('from');
                }
            } else {
                $classParts = explode('\\', $className);
                throw new Exception('Class does not exist for ' . $name . '(' . end($classParts) . ').');
            }
        }

        unset($rc);

        return [
            'allowNull'  => $allowNull,
            'dataType'   => $className,
            'enumBacked' => $enumBacked,
            'isBuiltin'  => $isBuiltin,
            'isEnum'     => $isEnum,
        ];
    }

    /**
     * Get serializer singleton
     *
     * @return Serializer
     */
    private static function getSerializer(): Serializer
    {
        if (self::$serializer === null) {
            self::$serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        }
        return self::$serializer;
    }
}
