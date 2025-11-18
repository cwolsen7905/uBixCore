<?php

declare(strict_types=1);

namespace Ubix\Tests;

use DateTime;
use DateTimeInterface;
use Exception;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use ReflectionClass;
use ReflectionException;
use SessionHandlerInterface as SessionHandler;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\ErrorRendererInterface as ErrorRenderer;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Throwable;
use Ubix\Collection\CollectionInterface as Collection;
use Ubix\Controller\AbstractController as Controller;
use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\DataTransferObject\GeolocationLookup;
use Ubix\Enum\AgeVerification\AgeVerificationRequirement;
use Ubix\Enum\MachineCodeReview\MachineCodeReviewTool;
use Ubix\Model\AbstractModel as Model;
use Ubix\SimpleCache\AbstractSimpleCache;
use Ubix\Tests\AbstractTestCase as TestCase;

/**
 * Abstract class for a PHPUnit test case for every VSM concrete class and enumeration
 */
abstract class AbstractUbixConcreteClassOrEnumTestCase extends TestCase
{
    private const CLASS_NAME_RESERVED_WORDS_AND_EXEMPTIONS = [ // These reserved words and exemptions are case sensitive
        'Model'    => ['Ubix\\DataTransferObject\\ModelDiff'],
        'Optiuser' => [],
    ];

    private const METHOD_NAMES_RESERVED_SYNONYMS_OF_GET = [ // These reserved synonyms are case insensitive
        'Fetch',
        'Find',
        'Lookup',
        'Retrieve',
    ];

    private const RANDOM_BYTES_SEED = 32;

    private const SYMFONY_COMMAND_MIXED_PROPERTY_EXEMPTIONS = [ // These exemptions are case sensitive
        'defaultDescription',
        'defaultName',
    ];

    private const SYMFONY_COMMAND_STATIC_METHOD_EXEMPTIONS = [ // These exemptions are case sensitive
        'getDefaultDescription',
        'getDefaultName',
    ];

    /**
     * Test that the class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    protected function testClassFollowingUbixStandards(string $className): void
    {
        //
        //  Run these tests on all classes
        //
        $this->testClassNamespaceFollowingUbixStandards($className);
        $this->testClassNameFollowingUbixStandards($className);
        $this->testClassConstantsFollowingUbixStandards($className);
        $this->testClassPropertiesFollowingUbixStandards($className);
        $this->testClassDependenciesFollowingUbixStandards($className);
        $this->testClassMethodsFollowingUbixStandards($className);

        //
        //  Run specific tests for different types of classes
        //
        // NOT_IMPLEMENTED: should I add an enum option here? should it check for methods and ban them? (* with exemptions?) probably not, probably rethink the structure and don't run classes and enums through the same method
        // NOT_IMPLEMENTED: Payloads
        // NOT_IMPLEMENTED: DataTypes
        if (str_starts_with($className, 'Ubix\\Collection\\')) {
            $this->testCollectionClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\Console\\Command\\')) {
            $this->testConsoleCommandClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\Controller\\')) {
            $this->testControllerClassFollowingUbixStandards($className);
            $this->unitTestControllerClass($className);
        } elseif (str_starts_with($className, 'Ubix\\DataTransferObject\\')) {
            $this->testDtoClassFollowingUbixStandards($className);
            $this->unitTestDtoClass($className);
        } elseif (str_starts_with($className, 'Ubix\\Exception\\')) {
            $this->testExceptionClassFollowingUbixStandards($className);
            $this->unitTestExceptionClass($className);
        } elseif (str_starts_with($className, 'Ubix\\HttpClient\\')) {
            $this->testHttpClientClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\Middleware\\')) {
            $this->testMiddlewareClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\Model\\')) {
            $this->testModelClassFollowingUbixStandards($className);
            $this->unitTestModelClass($className);
        } elseif (str_starts_with($className, 'Ubix\\Renderer\\')) {
            $this->testRendererClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\Repository\\')) {
            $this->testRepositoryClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\Service\\')) {
            $this->testServiceClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\SessionHandler\\')) {
            $this->testSessionHandlerClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\SimpleCache\\')) {
            $this->testSimpleCacheClassFollowingUbixStandards($className);
        } elseif (str_starts_with($className, 'Ubix\\SlimHandler\\')) {
            $this->testSlimHandlerClassFollowingUbixStandards($className);
        }
    }

    /**
     * Test that the console command class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testConsoleCommandClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className); // NOT_IMPLEMENTED: this was stubbed up early on in the process of building our first console commands - there is likely stuff missing, if you are reading this please audit the method and check for missing requirements

        //
        //  Ensure the class name is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'Command'),
            'All console command classes must end with `Command` but `' . $class->getName() . '` does not',
        );
    }

    /**
     * Test that the controller class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testControllerClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the class name is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'Controller'),
            'All controller classes must end with `Controller` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure the class extends the global abstract controller
        //
        if ($class->getName() !== Controller::class) {
            $this->assertTrue(
                $class->isSubclassOf(Controller::class),
                'All controller classes must extend `' . Controller::class . '` but `' . $class->getName() . '` does not',
            );
        }

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            $this->assertTrue(
                $property->isPrivate() || $property->isProtected(),
                'All controller class properties must be private or protected but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );
        }

        //
        //  Loop through each class method
        //
        foreach ($class->getMethods() as $method) {
            if ($class->isAbstract()) { // Validate abstract controllers methods
                if ($method->getName() !== '__construct') {
                    $this->assertTrue(
                        $method->isPrivate() || $method->isProtected(),
                        'All abstract controller class methods must be private or protected but `' . $method->getName() . '` of `' . $class->getName() . '` is not',
                    );
                }
            } else { // Validate concrete controllers methods
                if ($method->isPublic() && $method->getName() !== '__construct') {
                    $validPsrParameters = count($method->getParameters()) >= 2 && (string)$method->getParameters()[0]->getType() === Request::class && (string)$method->getParameters()[1]->getType() === Response::class;
                    $validPsrReturnType = (string)$method->getReturnType() === Response::class;

                    $this->assertTrue(
                        $validPsrParameters && $validPsrReturnType,
                        'All concrete controller class public methods must use `' . Request::class . '` as its first parameter type, `' . Response::class . '` as its second parameter type and `' . Response::class . '` as its return type but `' . $method->getName() . '` of `' . $class->getName() . '` does not',
                    );
                }
            }
        }

        //
        //  Test the parent class if one exists
        //
        if ($class->getParentClass() !== false && str_starts_with($class->getParentClass()->getName(), 'Ubix\\Controller\\')) {
            $this->testControllerClassFollowingUbixStandards($class->getParentClass()->getName());
        }
    }

    /**
     * Unit test a model class
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function unitTestModelClass(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Generate constructor parameters to instantiate an object of the class
        //
        $constructorParameters = [];

        if ($class->hasMethod('__construct')) {
            foreach ($class->getMethod('__construct')->getParameters() as $parameter) {
                $value = null;

                $parameterType = $parameter->getType() !== null ? (string)$parameter->getType() : 'mixed';
                if (str_starts_with($parameterType, '?')) {
                    $parameterType = substr($parameterType, 1) . '|null';
                }

                switch (explode('|', $parameterType)[0]) { // Generate a random value based on the first (and sometimes only) type
                    case 'array':
                        $value = $this->generateRandomArray();
                        break;

                    case 'bool':
                        $value = $this->generateRandomBoolean();
                        break;

                    case 'DateTime':
                    case 'DateTimeInterface':
                        $value = $this->generateRandomDateTime();
                        break;

                    case 'float':
                        $value = $this->generateRandomFloat();
                        break;

                    case 'int':
                        $value = $this->generateRandomInteger();
                        break;

                    case 'string':
                        $value = $this->generateRandomString();
                        break;
                }

                $constructorParameters[$parameter->getName()] = $value;
            }
        }

        //
        //  Instantiate a new object of the class
        //
        $instance = $class->newInstanceArgs($constructorParameters);

        //
        //  Test all properties were correctly set
        //
        if (count($class->getProperties()) > 0) {
            $exemptions = [
                'Ubix\Model\ProspectApplication::$originalObject',
            ];
            foreach ($class->getProperties() as $property) {
                if (in_array($class->getName() . '::$' . $property->getName(), $exemptions, true)) { // If the property is exempted then skip the assertions and continue to the next one
                    continue;
                }

                $propertyGetter = 'get' . strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);
                $propertySetter = 'set' . strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);


                // If the setter or getter is for changedProperties then exempt it from testing as it's a special case
                $isExempt = $propertyGetter === 'getChangedProperties' || $propertySetter === 'setChangedProperties';
                if ($isExempt) {
                    continue;
                }

                //
                //  Test that the getter returns the same value that the object was instantiated with
                //
                $this->assertSame(
                    $instance->{$propertyGetter}(),
                    $constructorParameters[$property->getName()],
                    'The `' . $propertyGetter . '` method of `' . $class->getName() . '` must return the same value that was passed through the constructor but is not',
                );



                //
                //  Test the setter with a new value then the getter a second time
                //
                $newValue = null;

                $propertyType = $property->getType() !== null ? (string)$property->getType() : 'mixed';
                if (str_starts_with($propertyType, '?')) {
                    $propertyType = substr($propertyType, 1) . '|null';
                }

                switch (explode('|', $propertyType)[0]) { // Generate a random value based on the first (and sometimes only) type
                    case 'array':
                        $newValue = $this->generateRandomArray();
                        break;

                    case 'bool':
                        $newValue = $this->generateRandomBoolean();
                        break;

                    case 'DateTime':
                    case 'DateTimeInterface':
                        $newValue = $this->generateRandomDateTime();
                        break;

                    case 'float':
                        $newValue = $this->generateRandomFloat();
                        break;

                    case 'int':
                        $newValue = $this->generateRandomInteger();
                        break;

                    case 'string':
                        $newValue = $this->generateRandomString();
                        break;
                }

                $instance->{$propertySetter}($newValue);
                $this->assertSame(
                    $instance->{$propertyGetter}(),
                    $newValue,
                    'The `' . $propertyGetter . '` method of `' . $class->getName() . '` must return the same value that was passed through the `' . $propertySetter . '` method but is not',
                );
            }
        } else {
            $this->assertEmpty($class->getProperties());
        }
    }

    /**
     * Generate a random array
     *
     * @return mixed[] A random array
     */
    private function generateRandomArray(): array
    {
        // NOT_IMPLEMENTED: once PHP-DI is set up for PHPUnit this can be made more expansive and actually include the correct type of items within the array
        return [];
    }

    /**
     * Generate a random boolean
     *
     * @return bool A random boolean
     */
    private function generateRandomBoolean(): bool
    {
        return mt_rand(0, 1) === 0;
    }

    /**
     * Generate a random DateTime object
     *
     * @return DateTimeInterface A random DateTime object
     */
    private function generateRandomDateTime(): DateTimeInterface
    {
        return new DateTime();
    }

    /**
     * Generate a random integer
     *
     * @return int A random integer
     */
    private function generateRandomInteger(): int
    {
        return mt_rand(PHP_INT_MIN, PHP_INT_MAX);
    }

    /**
     * Generate a random float
     *
     * @return float A random float
     */
    private function generateRandomFloat(): float
    {
        return mt_rand() / mt_getrandmax();
    }

    /**
     * Generate a random string
     *
     * @return string A random string
     */
    private function generateRandomString(): string
    {
        return bin2hex(random_bytes(self::RANDOM_BYTES_SEED));
    }

    /**
     * Unit test a controller class
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function unitTestControllerClass(string $className): void
    {
        $class = $this->getReflectionClass($className);

        // NOT_IMPLEMENTED: this is a placeholder assertion, this whole method needs to be coded
        $this->assertTrue($class->isSubclassOf(Controller::class)); // DELETE THIS LINE WHEN YOU CODE THIS METHOD
    }

    /**
     * Unit test a DTO class
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function unitTestDtoClass(string $className): void
    {
        $class = $this->getReflectionClass($className);

        // NOT_IMPLEMENTED: We can probably standardize the "generate an object with randomized constructor parameters" process rather than it being a copy/paste in DTOs and models (DRY principle violation)
        //
        //  Generate constructor parameters to instantiate an object of the class
        //
        $constructorParameters = [];

        if ($class->hasMethod('__construct')) {
            foreach ($class->getMethod('__construct')->getParameters() as $parameter) {
                $value = null;

                $parameterType = $parameter->getType() !== null ? (string)$parameter->getType() : 'mixed';
                if (str_starts_with($parameterType, '?')) {
                    $parameterType = substr($parameterType, 1) . '|null';
                }

                switch (explode('|', $parameterType)[0]) { // Generate a random value based on the first (and sometimes only) type
                    case 'array':
                        $value = $this->generateRandomArray();
                        break;

                    case 'bool':
                        $value = $this->generateRandomBoolean();
                        break;

                    case 'DateTime':
                    case 'DateTimeInterface':
                        $value = $this->generateRandomDateTime();
                        break;

                    case 'float':
                        $value = $this->generateRandomFloat();
                        break;

                    case 'int':
                        $value = $this->generateRandomInteger();
                        break;

                    case 'string':
                        $value = $this->generateRandomString();
                        break;

                    case GeolocationLookup::class: // TEMPORARY: this is hacky, we need to do something about PHP-DI for PHPUnit
                        $value = new GeolocationLookup();
                        break;

                    case AgeVerificationRequirement::class: // TEMPORARY: this is hacky, we need to do something about PHP-DI for PHPUnit
                        $value = AgeVerificationRequirement::BLOCKED;
                        break;

                    case MachineCodeReviewTool::class: // TEMPORARY: this is hacky, we need to do something about PHP-DI for PHPUnit
                        $value = MachineCodeReviewTool::PHPUNIT;
                        break;
                }

                $constructorParameters[$parameter->getName()] = $value;
            }
        }

        //
        //  Instantiate a new object of the class
        //
        $instance = $class->newInstanceArgs($constructorParameters);

        //
        //  Test all properties were correctly set
        //
        if (count($class->getProperties()) > 0) {
            foreach ($class->getProperties() as $property) {
                $this->assertSame(
                    $instance->{$property->getName()},
                    $constructorParameters[$property->getName()],
                    'The `' . $property->getName() . '` property of `' . $class->getName() . '` must be set to the same value that was passed through the constructor but is not',
                );
            }
        } else {
            $this->assertEmpty($class->getProperties());
        }
    }

    /**
     * Unit test an exception class
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function unitTestExceptionClass(string $className): void
    {
        $class = $this->getReflectionClass($className);

        if ($class->isSubclassOf(Exception::class)) {
            $defaultConstructorParameters = [
                'code'     => 0,
                'message'  => '',
                'previous' => null,
            ];

            //
            //  Test an instance with no constructor parameters
            //
            $constructorParameters = [];

            $instance = $class->newInstanceArgs($constructorParameters);

            $this->assertSame(
                $defaultConstructorParameters['code'],
                $instance->getCode(),
                'The `' . $class->getName() . '` class must return the default value of the $code parameter (`' . $defaultConstructorParameters['code'] . '`) when the getCode() method is called but is not',
            );

            $this->assertSame(
                $defaultConstructorParameters['message'],
                $instance->getMessage(),
                'The `' . $class->getName() . '` class must return the default value of the $message parameter (`' . $defaultConstructorParameters['message'] . '`) when the getMessage() method is called but is not',
            );

            $this->assertSame(
                $defaultConstructorParameters['previous'],
                $instance->getPrevious(),
                'The `' . $class->getName() . '` class must return the default value of the $previous parameter (`' . $defaultConstructorParameters['code'] . '`) when the getPrevious() method is called but is not',
            );

            //
            //  Test an instance with a code constructor parameter
            //
            $constructorParameters['code'] = $this->generateRandomInteger();

            $instance = $class->newInstanceArgs($constructorParameters);

            $this->assertSame(
                $constructorParameters['code'],
                $instance->getCode(),
                'The `' . $class->getName() . '` class must return `' . $constructorParameters['code'] . '` when the getCode() method is called but is not',
            );

            $this->assertSame(
                $defaultConstructorParameters['message'],
                $instance->getMessage(),
                'The `' . $class->getName() . '` class must return the default value of the $message parameter (`' . $defaultConstructorParameters['message'] . '`) when the getMessage() method is called but is not',
            );

            $this->assertSame(
                $defaultConstructorParameters['previous'],
                $instance->getPrevious(),
                'The `' . $class->getName() . '` class must return the default value of the $previous parameter (`' . $defaultConstructorParameters['code'] . '`) when the getPrevious() method is called but is not',
            );

            //
            //  Test an instance with code and message constructor parameters
            //
            $constructorParameters['message'] = $this->generateRandomString();

            $instance = $class->newInstanceArgs($constructorParameters);

            $this->assertSame(
                $constructorParameters['code'],
                $instance->getCode(),
                'The `' . $class->getName() . '` class must return `' . $constructorParameters['code'] . '` when the getCode() method is called but is not',
            );

            $this->assertSame(
                $constructorParameters['message'],
                $instance->getMessage(),
                'The `' . $class->getName() . '` class must return `' . $constructorParameters['message'] . '` when the getMessage() method is called but is not',
            );

            $this->assertSame(
                $defaultConstructorParameters['previous'],
                $instance->getPrevious(),
                'The `' . $class->getName() . '` class must return the default value of the $previous parameter (`' . $defaultConstructorParameters['code'] . '`) when the getPrevious() method is called but is not',
            );

            //
            //  Test an instance with code, message and previous constructor parameters
            //
            $constructorParameters['previous'] = $this->createMock(Throwable::class);

            $instance = $class->newInstanceArgs($constructorParameters);

            $this->assertSame(
                $constructorParameters['code'],
                $instance->getCode(),
                'The `' . $class->getName() . '` class must return `' . $constructorParameters['code'] . '` when the getCode() method is called but is not',
            );

            $this->assertSame(
                $constructorParameters['message'],
                $instance->getMessage(),
                'The `' . $class->getName() . '` class must return `' . $constructorParameters['message'] . '` when the getMessage() method is called but is not',
            );

            $this->assertSame(
                $constructorParameters['previous'],
                $instance->getPrevious(),
                'The `' . $class->getName() . '` class must return `' . $constructorParameters['previous'] . '` when the getPrevious() method is called but is not',
            );
        }
    }

    /**
     * Test that the Collection class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testCollectionClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the Collection marker interface is implemented
        //
        $this->assertTrue(
            $class->implementsInterface(Collection::class),
            'All Collection classes must implement the `' . Collection::class . '` marker interface but `' . $class->getName() . '` does not',
        );


        //
        //  Ensure the one and only method is the constructor
        //
        $this->assertTrue(
            $class->hasMethod('__construct') && $class->getMethod('__construct')->isPublic(),
            'All Collection classes must have a public constructor but `' . $class->getName() . '` does not',
        );

        foreach ($class->getMethods() as $method) {
            foreach ($method->getParameters() as $parameter) {
                if ($parameter->allowsNull()) {
                    $this->assertTrue(
                        $parameter->isDefaultValueAvailable() && $parameter->getDefaultValue() === null,
                        'All Collection properties which allow null must have a constructor parameter with a default value of null but `' . $parameter->getName() . '` of `' . $class->getName() . '` does not',
                    );
                }
            }
        }

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            $this->assertTrue(
                $property->isPrivate(),
                'All Collection properties must be private but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );

            $this->assertTrue(
                $property->getType() !== null,
                'All Collection properties must have a type set but `' . $property->getName() . '` of `' . $class->getName() . '` does not',
            );
        }
    }

    /**
     * Test that the DTO class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testDtoClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the Dto marker interface is implemented
        //
        $this->assertTrue(
            $class->implementsInterface(Dto::class),
            'All DTO classes must implement the `' . Dto::class . '` marker interface but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure the class is readonly
        //
        $this->assertTrue(
            $class->isReadOnly(),
            'All DTO classes must be readonly but `' . $class->getName() . '` is not',
        );

        //
        //  Ensure the one and only method is the constructor
        //
        $this->assertTrue(
            $class->hasMethod('__construct') && $class->getMethod('__construct')->isPublic(),
            'All DTO classes must have a public constructor but `' . $class->getName() . '` does not',
        );

        foreach ($class->getMethods() as $method) {
            $this->assertTrue(
                $method->getName() === '__construct',
                'All DTO classes must avoid methods (except for a constructor) but `' . $method->getName() . '` exists in `' . $class->getName() . '`',
            );

            foreach ($method->getParameters() as $parameter) {
                try {
                    $property = $class->getProperty($parameter->getName());

                    $this->assertTrue(
                        $parameter->getType() !== null && (string)$parameter->getType() === (string)$property->getType(),
                        'All DTO properties must have a constructor parameter with matching type but `' . $parameter->getName() . '` of `' . $class->getName() . '` does not (`' . (string)$parameter->getType() . '` instead of `' . (string)$property->getType() . '`)',
                    );

                    $this->assertTrue(
                        $property->isReadOnly(),
                        'All DTO properties must be readonly but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
                    );
                } catch (ReflectionException $e) {
                    $this->fail('All DTO properties must have a matching constructor parameter but `' . $parameter->getName() . '` of `' . $class->getName() . '` does not');
                }

                if ($parameter->allowsNull()) {
                    $this->assertTrue(
                        $parameter->isDefaultValueAvailable() && $parameter->getDefaultValue() === null,
                        'All DTO properties which allow null must have a constructor parameter with a default value of null but `' . $parameter->getName() . '` of `' . $class->getName() . '` does not',
                    );
                }
            }
        }

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            $this->assertTrue(
                $property->isPublic(),
                'All DTO properties must be public but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );

            $this->assertTrue(
                $property->getType() !== null,
                'All DTO properties must have a type set but `' . $property->getName() . '` of `' . $class->getName() . '` does not',
            );
        }
    }

    /**
     * Test that the HTTP client class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testHttpClientClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure class suffix is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'HttpClient'),
            'All HTTP client classes must end with `HttpClient` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure class extends PSR's HTTP client interface
        //
        $this->assertTrue(
            $class->implementsInterface(HttpClient::class),
            'All HTTP client classes must implement the `' . HttpClient::class . '` interface but `' . $class->getName() . '` does not',
        );
    }

    /**
     * Test that the exception class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testExceptionClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure class suffix is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'Exception'),
            'All exception classes must end with `Exception` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure class extends PHP's base Exception class
        //
        $this->assertTrue(
            $class->getParentClass() !== false && $class->getParentClass()->getName() === Exception::class,
            'All exception classes must extend the `' . Exception::class . '` class but `' . $class->getName() . '` does not',
        );
    }

    /**
     * Test that the middleware class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testMiddlewareClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the class name is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'Middleware'),
            'All middleware classes must end with `Middleware` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure class implements PSR's Middleware interace
        //
        $this->assertTrue(
            $class->implementsInterface(Middleware::class),
            'All middleware classes must implement the `' . Middleware::class . '` interface but `' . $class->getName() . '` does not',
        );

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            $this->assertTrue(
                $property->isPrivate() || $property->isProtected(),
                'All middleware class properties must be private or protected but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );
        }

        //
        //  Loop through each class method
        //
        foreach ($class->getMethods() as $method) {
            if ($method->isPublic() && $method->getName() !== '__construct') {
                $validPsrName       = $method->getName() === 'process';
                $validPsrParameters = count($method->getParameters()) >= 2 && (string)$method->getParameters()[0]->getType() === Request::class && (string)$method->getParameters()[1]->getType() === Handler::class;
                $validPsrReturnType = (string)$method->getReturnType() === Response::class;

                $this->assertTrue(
                    $validPsrName && $validPsrParameters && $validPsrReturnType,
                    'All middleware class public methods must be called `process`, use  `' . Request::class . '` as its first parameter type, use `' . Handler::class . '` as its second parameter type and `' . Response::class . '` as its return type but `' . $method->getName() . '` of `' . $class->getName() . '` does not',
                );
            }
        }
    }

    /**
     * Test that the model class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testModelClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the AbstractModel abstract class is extended
        //
        $this->assertTrue(
            $class->isSubclassOf(Model::class),
            'All model classes must extend the `' . Model::class . '` abstract class but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure classes have a public constructor
        //
        $this->assertTrue(
            $class->hasMethod('__construct') && $class->getMethod('__construct')->isPublic(),
            'All model classes must have a public constructor but `' . $class->getName() . '` does not',
        );

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            //
            //  Ensure properties are either private or protected
            //
            $this->assertTrue(
                $property->isPrivate() || $property->isProtected(),
                'All model properties must be protected or private but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );

            //
            //  Ensure properties have a type defined
            //
            $this->assertTrue(
                $property->getType() !== null,
                'All model properties must have a type set but `' . $property->getName() . '` of `' . $class->getName() . '` does not',
            );

            //
            //  Ensure the property has a corresponding parameter in the constructor with a default value of null
            //
            $constructor = $class->getMethod('__construct');

            $constructorParameterFound = false;
            foreach ($constructor->getParameters() as $parameter) {
                if (!$constructorParameterFound && $parameter->getName() === $property->getName()) {
                    $constructorParameterFound = true;

                    $this->assertTrue(
                        $parameter->getType() !== null && (string)$parameter->getType() === (string)$property->getType(),
                        'All model properties must have a constructor parameter with matching type but `' . $parameter->getName() . '` of `' . $class->getName() . '` does not (`' . (string)$parameter->getType() . '` instead of `' . (string)$property->getType() . '`)',
                    );

                    $this->assertTrue(
                        $parameter->isDefaultValueAvailable() && $parameter->getDefaultValue() === null,
                        'All model properties must have a constructor parameter with a default value of null but `' . $parameter->getName() . '` of `' . $class->getName() . '` does not',
                    );
                }
            }

            //
            //  Ensure properties have getter and setter functions
            //
            if ($constructorParameterFound) {
                $propertyGetter = 'get' . strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);
                try {
                    $getter = $class->getMethod($propertyGetter);

                    $this->assertTrue(
                        $getter->isPublic(),
                        'All model getter methods must be public but `' . $propertyGetter . '` of `' . $class->getName() . '` is not',
                    );

                    $this->assertTrue(
                        $getter->getReturnType() !== null && (string)$getter->getReturnType() === (string)$property->getType(),
                        'All model getter methods must have a return type that matches the property\'s but `' . $propertyGetter . '` of `' . $class->getName() . '` does not (`' . (string)$getter->getReturnType() . '` instead of `' . (string)$property->getType() . '`)',
                    );
                } catch (ReflectionException $e) {
                    $this->fail('All model properties must have a getter method but `' . $property->getName() . '` of `' . $class->getName() . '` is missing a `' . $propertyGetter . '` method');
                }

                $propertySetter = 'set' . strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);
                try {
                    $setter = $class->getMethod($propertySetter);

                    $this->assertTrue(
                        $setter->isPublic(),
                        'All model setter methods must be public but `' . $propertySetter . '` of `' . $class->getName() . '` is not',
                    );

                    $this->assertGreaterThan(
                        0,
                        count($setter->getParameters()),
                        'All model setter methods must have at least one parameter but `' . $propertySetter . '` of `' . $class->getName() . '` has none',
                    );

                    $this->assertTrue(
                        $setter->getParameters()[0]->getType() !== null && (string)$setter->getParameters()[0]->getType() === (string)$property->getType(),
                        'All model setter methods must have a first parameter whose type matches the property\'s but `' . $propertySetter . '` of `' . $class->getName() . '` does not (`' . (string)$setter->getParameters()[0]->getType() . '` instead of `' . (string)$property->getType() . '`)',
                    );
                } catch (ReflectionException $e) {
                    $this->fail('All model properties must have a setter method but `' . $property->getName() . '` of `' . $class->getName() . '` is missing a `' . $propertySetter . '` method');
                }
            }
        }
    }

    /**
     * Test that the renderer class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testRendererClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the class name is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'Renderer'),
            'All renderer classes must end with `Renderer` but `' . $class->getName() . '` does not',
        );

        if ($class->implementsInterface(ErrorRenderer::class)) {
            $this->assertTrue(
                str_ends_with($class->getName(), 'ErrorRenderer'),
                'All error renderer classes must end with `ErrorRenderer` but `' . $class->getName() . '` does not',
            );
        }

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            $this->assertTrue(
                $property->isPrivate() || $property->isProtected(),
                'All renderer class properties must be private or protected but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );
        }

        //
        //  Loop through each class method
        //
        foreach ($class->getMethods() as $method) {
            if ($class->implementsInterface(ErrorRenderer::class) && $method->isPublic() && $method->getName() !== '__construct') {
                $validPsrName       = $method->getName() === '__invoke';
                $validPsrParameters = count($method->getParameters()) >= 2 && (string)$method->getParameters()[0]->getType() === Throwable::class && (string)$method->getParameters()[1]->getType() === 'bool';
                $validPsrReturnType = (string)$method->getReturnType() === 'string';

                $this->assertTrue(
                    $validPsrName && $validPsrParameters && $validPsrReturnType,
                    'All error renderer class public methods must be called `__invoke`, use  `' . Throwable::class . '` as its first parameter type, use `bool` as its second parameter type and `string` as its return type but `' . $method->getName() . '` of `' . $class->getName() . '` does not',
                );
            }
        }
    }

    /**
     * Test that the repository class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testRepositoryClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure the type of repository is included as the third subnamespace
        //
        $subnamespaces    = explode('\\', $class->getNamespaceName());
        $typeSubnamespace = count($subnamespaces) >= 3 ? $subnamespaces[2] : null;

        $this->assertGreaterThanOrEqual(
            3,
            count($subnamespaces),
            'All repository classes/interfaces must have at least three subnamespaces but `' . $class->getName() . '` has ' . count($subnamespaces),
        );

        $this->assertTrue(
            str_starts_with($class->getShortName(), $typeSubnamespace ?? ''),
            'All repository class/interface short names must be prefixed with their type but `' . $class->getShortName() . '` of `' . $class->getName() . '` doesn\'t begin with ' . $typeSubnamespace,
        );

        if ($class->isInterface()) { // Validate repository interface
            //
            //  Ensure interface suffix is correct
            //
            $this->assertTrue(
                str_ends_with($class->getName(), 'ReaderInterface') || str_ends_with($class->getName(), 'WriterInterface'),
                'All repository interfaces must end with `ReaderInterface` or `WriterInterface` but `' . $class->getName() . '` does not',
            );

            //
            //  Ensure all writer interface methods return void and use exclusively models as parameter types
            //
            if (str_ends_with($class->getName(), 'WriterInterface')) {
                foreach ($class->getMethods() as $method) {
                    $this->assertTrue(
                        (string)$method->getReturnType() === 'void',
                        'All repository writer interface methods must have a void return type but `' . $method->getName() . '` of `' . $class->getName() . '` does not',
                    );

                    if ($method->getName() !== 'save' && $method->getName() !== 'update') { // The save and update methods cannot accept non-model parameters
                        continue;
                    }

                    foreach ($method->getParameters() as $parameter) {
                        $this->assertTrue(
                            $parameter->getType() !== null && str_starts_with((string)$parameter->getType(), 'Ubix\\Model\\'),
                            'All repository writer interface method parameters must be of a model type but `' . $parameter->getName() . '` of `' . $method->getName() . '` of `' . $class->getName() . '` is not',
                        );
                    }
                }
            }
        } else { // Validate repository concrete class
            //
            //  Ensure class suffix is correct
            //
            $this->assertTrue(
                str_ends_with($class->getName(), 'Repository'),
                'All repository concrete classes must end with `Repository` but `' . $class->getName() . '` does not',
            );

            //
            //  Ensure the repository is implementing one or more interfaces
            //
            $this->assertTrue(
                count($class->getInterfaceNames()) > 0,
                'All repository concrete classes must implement one or more interfaces but `' . $class->getName() . '` does not',
            );

            //
            //  Ensure class prefix is correct and consistent with implemented interfaces
            //
            $implementsReaderInterface = false;

            foreach ($class->getInterfaceNames() as $interfaceName) {
                $this->assertTrue(
                    str_starts_with($interfaceName, 'Ubix\\Repository\\'),
                    'All repository concrete classes must only implement VSM repository interfaces but `' . $class->getName() . '` implements `' . $interfaceName . '`',
                );

                $interfacePrefix = substr($interfaceName, strrpos($interfaceName, '\\') + 1, -15);
                $this->assertTrue(
                    $typeSubnamespace === $interfacePrefix,
                    'All repository concrete class names must match the interfaces they implement but `' . $class->getName() . '` has a `' . $typeSubnamespace . '` prefix instead of `' . $interfacePrefix . '`',
                );

                if (str_ends_with($interfaceName, 'ReaderInterface')) {
                    $implementsReaderInterface = true;
                }
            }

            if ($implementsReaderInterface && str_ends_with($class->getShortName(), 'SqlRepository')) {
                $this->assertTrue(
                    $class->hasMethod('query') && $class->getMethod('query')->isPrivate() && (string) $class->getMethod('query')->getParameters()[0]->getType() === 'Ubix\\DataTransferObject\\SqlRepository\\' . $typeSubnamespace . 'Options',
                    'All SQL repository classes that implement a reader interface must have a private method called `query` with a first parameter of type `Ubix\\DataTransferObject\\SqlRepository\\' . $typeSubnamespace . 'Options` but `' . $class->getName() . '` does not',
                );
            }

            //
            //  Test any VSM interfaces implemented
            //
            foreach ($class->getInterfaceNames() as $interface) {
                if (str_starts_with($interface, 'Ubix\\Repository\\')) {
                    $this->testRepositoryClassFollowingUbixStandards($interface);
                }
            }
        }
    }

    /**
     * Test that the service class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testServiceClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure class name is correct
        //
        if ($class->isInterface()) {
            $this->assertTrue(
                str_ends_with($class->getName(), 'ServiceInterface'),
                'All service interfaces must end with `ServiceInterface` but `' . $class->getName() . '` does not',
            );
        } else {
            $this->assertTrue(
                str_ends_with($class->getName(), 'Service'),
                'All service classes must end with `Service` but `' . $class->getName() . '` does not',
            );

            foreach ($class->getInterfaceNames() as $interface) {
                $interfacePrefix = substr($interface, strrpos($interface, '\\') + 1, -9);

                $this->assertTrue(
                    substr($class->getName(), 0 - strlen($interfacePrefix)) === $interfacePrefix || substr($class->getName(), 0 - strlen($interfacePrefix . 'Service')) === $interfacePrefix . 'Service', // We use "Service" in our interfaces but PSR and other third parties may not
                    'All service classes must end with their implemented interface (`' . $interfacePrefix . '`) but `' . $class->getName() . '` does not',
                );
            }

            if ($class->getParentClass() !== false) {
                $parentClassSuffix = substr($class->getParentClass()->getShortName(), strrpos($class->getShortName(), '\\') + 9);

                $this->assertTrue(
                    substr($class->getName(), 0 - strlen($parentClassSuffix)) === $parentClassSuffix,
                    'All service classes must end with their parent class (`' . $parentClassSuffix . '`) but `' . $class->getName() . '` does not',
                );
            }
        }

        //
        //  Loop through each class property
        //
        foreach ($class->getProperties() as $property) {
            $this->assertTrue(
                $property->isPrivate() || $property->isProtected(),
                'All service class properties must be private or protected but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
            );
        }

        //
        //  Test the parent class if one exists
        //
        if ($class->getParentClass() !== false && str_starts_with($class->getParentClass()->getName(), 'Ubix\\Service\\')) {
            $this->testServiceClassFollowingUbixStandards($class->getParentClass()->getName());
        }

        //
        //  Test the interfaces implemented if any exist
        //
        foreach ($class->getInterfaceNames() as $interface) {
            if (str_starts_with($interface, 'Ubix\\Service\\')) {
                $this->testServiceClassFollowingUbixStandards($interface);
            }
        }
    }

    /**
     * Test that the session handler class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testSessionHandlerClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure class suffix is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'SessionHandler'),
            'All session handler classes must end with `SessionHandler` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure the correct interface is implemented
        //
        $this->assertTrue(
            $class->implementsInterface(SessionHandler::class),
            'All session handler classes must implement the `' . SessionHandler::class . '` interface but `' . $class->getName() . '` does not',
        );
    }

    /**
     * Test that the simple cache class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testSimpleCacheClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure class suffix is correct
        //
        $this->assertTrue(
            str_ends_with($class->getName(), 'SimpleCache'),
            'All simple cache classes must end with `SimpleCache` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure the correct interface is implemented
        //
        $classNeedsInterface      = $class->getName() !== AbstractSimpleCache::class;
        $classImplementsInterface = $class->implementsInterface(SimpleCache::class);

        $this->assertTrue(
            !$classNeedsInterface || $classImplementsInterface,
            'All simple class classes must implement the `' . SimpleCache::class . '` interface but `' . $class->getName() . '` does not',
        );

        //
        //  Test the parent class if one exists
        //
        if ($class->getParentClass() !== false && str_starts_with($class->getParentClass()->getName(), 'Ubix\\SimpleCache\\')) {
            $this->testSimpleCacheClassFollowingUbixStandards($class->getParentClass()->getName());
        }
    }

    /**
     * Test that the Slim handler class follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testSlimHandlerClassFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure class name is correct
        //
        $this->assertTrue(
            str_starts_with($class->getShortName(), 'Slim'),
            'All Slim handler classes must end with `Slim` but `' . $class->getName() . '` does not',
        );

        $this->assertTrue(
            str_ends_with($class->getName(), 'Handler'),
            'All Slim handler classes must end with `Handler` but `' . $class->getName() . '` does not',
        );

        //
        //  Ensure a correct parent class is used
        //
        $this->assertTrue(
            $class->getParentClass() !== false && ($class->getParentClass()->getName() === ErrorHandler::class || str_starts_with($class->getParentClass()->getName(), 'Ubix\\SlimHandler\\')),
            'All Slim handler classes must extend the `' . ErrorHandler::class . '` class or another VSM Slim handler class but `' . $class->getName() . '` does not',
        );

        //
        //  Test the parent class if one exists
        //
        if (str_starts_with($class->getParentClass()->getName(), 'Ubix\\SlimHandler\\')) {
            $this->testSlimHandlerClassFollowingUbixStandards($class->getParentClass()->getName());
        }
    }

    /**
     * Test that the class namespace follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testClassNamespaceFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        $this->assertFalse(
            str_starts_with($class->getNamespaceName(), '\\Ubix\\'),
            'Class namespaces must start with `\\Ubix\\` but `' . $class->getName() . '` does',
        );

        $this->assertFalse(
            strpos($class->getNamespaceName(), '_'),
            'Class namespaces must not include underscores but `' . $class->getName() . '` does',
        );

        foreach (explode('\\', $class->getNamespaceName()) as $subnamespace) {
            $this->assertSame(
                substr($subnamespace, 0, 1),
                strtoupper(substr($subnamespace, 0, 1)),
                'Class subnamespaces must all begin with a capital letter but `' . $subnamespace . '` of ' . $class->getName() . '` does not',
            );
        }
    }

    /**
     * Test that the class name follows VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testClassNameFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        $this->assertSame(
            substr($class->getShortName(), 0, 1),
            strtoupper(substr($class->getShortName(), 0, 1)),
            'Class names must begin with a capital letter but `' . $class->getName() . '` does not',
        );

        $this->assertFalse(
            strpos($class->getShortName(), '_'),
            'Class names must not include underscores but `' . $class->getName() . '` does',
        );

        if ($class->isAbstract()) {
            $this->assertTrue(
                str_starts_with($class->getShortName(), 'Abstract'),
                'All abstract classes must start with `Abstract` but `' . $class->getName() . '` does not',
            );
        }

        if ($class->isInterface()) {
            $this->assertTrue(
                str_ends_with($class->getShortName(), 'Interface'),
                'All interfaces must end with `Interface` but `' . $class->getName() . '` does not',
            );
        }

        //
        //  Loop through each reserved word
        //
        if (count(self::CLASS_NAME_RESERVED_WORDS_AND_EXEMPTIONS) > 0) {
            foreach (self::CLASS_NAME_RESERVED_WORDS_AND_EXEMPTIONS as $reservedWord => $exemptions) {
                if (stripos($class->getShortName(), $reservedWord) !== false) { // To make phpstan happy we are only running the assertion if the class name contains a reserved word and that assertion is confirming that the class name is not among the reserved word's exemptions
                    $this->assertTrue(
                        count($exemptions) > 0 && in_array($class->getName(), $exemptions, true),
                        'Class names can not include the reserved word `' . $reservedWord . '` but `' . $class->getName() . '` does',
                    );
                }
            }
        } else {
            $this->assertEmpty(self::CLASS_NAME_RESERVED_WORDS_AND_EXEMPTIONS);
        }
    }

    /**
     * Test that the class properties follow VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testClassPropertiesFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        if (count($class->getProperties()) > 0) {
            foreach ($class->getProperties() as $property) {
                //
                //  Ban the mixed type
                //
                $isExempt = str_starts_with($class->getName(), 'Ubix\\Collection\\')
                || str_starts_with($class->getName(), 'Ubix\\DataTransferObject\\')
                || str_starts_with($class->getName(), 'Ubix\\Exception\\')
                || str_starts_with($class->getName(), 'Ubix\\SlimHandler\\')
                || ($class->isSubclassOf(SymfonyCommand::class) && in_array($property->getName(), self::SYMFONY_COMMAND_MIXED_PROPERTY_EXEMPTIONS, true));

                $this->assertTrue(
                    $isExempt || ($property->getType() !== null && (string)$property->getType() !== 'mixed'),
                    'All class properties must not use the `mixed` type but `' . $property->getName() . '` of `' . $class->getName() . '` does',
                );

                //
                //  Ban static properties
                //
                $isExempt = $property->getName() === 'singleton'
                || $property->getName() === 'singletons'
                || str_ends_with($property->getName(), 'Singleton')
                || str_ends_with($property->getName(), 'Singletons')
                || ($class->isSubclassOf(SymfonyCommand::class) && in_array($property->getName(), self::SYMFONY_COMMAND_MIXED_PROPERTY_EXEMPTIONS, true));

                $this->assertTrue(
                    $isExempt || !$property->isStatic(),
                    'All class properties must not be static but `' . $property->getName() . '` of `' . $class->getName() . '` is',
                );

                //
                //  Ensure that all properties declared in the class (as opposed to inherited) that have a corresponding constructor parameter are promoted
                //
                $isExempt = str_starts_with($class->getName(), 'Ubix\\Payload\\');
                if ($property->getDeclaringClass()->getName() === $class->getName() && $class->hasMethod('__construct')) {
                    $constructor = $class->getMethod('__construct');
                    foreach ($constructor->getParameters() as $parameter) {
                        if ($parameter->getName() === $property->getName()) {
                            $this->assertTrue(
                                $isExempt || $parameter->isPromoted() && $property->isPromoted(),
                                'All declared class properties which are present in the constructor parameter must be promoted but `' . $property->getName() . '` of `' . $class->getName() . '` is not',
                            );
                        }
                    }
                }
            }
        } else {
            $this->assertEmpty($class->getProperties());
        }
    }

    /**
     * Test that the class constants follow VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testClassConstantsFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        if (count($class->getConstants()) > 0) {
            foreach ($class->getConstants() as $name => $value) {
                if ($class->isEnum()) { // Validate enums
                    $this->assertTrue(
                        preg_match('/^[A-Z]([a-zA-Z0-9]*)*$/', $name) !== false,
                        'All enum cases must be in UpperCamelCase/PascalCase but `' . $name . '` of `' . $class->getName() . '` is not',
                    );
                } else { // Validate classes
                    $this->assertSame(
                        $name,
                        strtoupper($name),
                        'All class constant names must be in uppercase but `' . $name . '` of `' . $class->getName() . '` is not',
                    );

                    $this->assertTrue(
                        is_scalar($value) || is_array($value),
                        'All class constants must use scalar types or arrays but `' . $name . '` of `' . $class->getName() . '` uses `' . gettype($value) . '`',
                    );
                }
            }
        } else {
            $this->assertEmpty($class->getConstants());
        }
    }

    /**
     * Test that the class methods follow VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testClassMethodsFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        if (count($class->getMethods()) > 0) {
            foreach ($class->getMethods() as $method) {
                $isExempt = str_starts_with($class->getName(), 'Ubix\\Payload\\');

                //
                //  Ban static methods
                //
                $isExempt = $isExempt || $class->isEnum()
                || ($class->isSubclassOf(SymfonyCommand::class) && in_array($method->getName(), self::SYMFONY_COMMAND_STATIC_METHOD_EXEMPTIONS, true));

                $this->assertTrue(
                    $isExempt || !$method->isStatic(),
                    'All class methods must not be static but `' . $method->getName() . '` of `' . $class->getName() . '` is',
                );

                //
                //  Ban reserved synonyms of "get"
                //
                foreach (self::METHOD_NAMES_RESERVED_SYNONYMS_OF_GET as $reservedSynonym) {
                    $this->assertFalse(
                        stripos($method->getName(), $reservedSynonym) === 0,
                        'All class methods must not start with `' . substr($method->getName(), 0, strlen($reservedSynonym)) . '` (use `get` instead) but `' . $method->getName() . '` of `' . $class->getName() . '` does',
                    );
                }
            }
        } else {
            $this->assertEmpty($class->getMethods());
        }
    }

    /**
     * Test that the class dependencies follow VSM standards
     *
     * @param string $className The class to test
     *
     * @return void
     */
    private function testClassDependenciesFollowingUbixStandards(string $className): void
    {
        $class = $this->getReflectionClass($className);

        //
        //  Ensure a logger dependency is present when applicable
        //
        $requiresLogger = !$class->isEnum() // NOT_IMPLEMENTED: phpcs should do one extra indent
        && !$class->isInterface()
        && !str_starts_with($class->getName(), 'Ubix\\Collection\\')
        && !str_starts_with($class->getName(), 'Ubix\\DataTransferObject\\')
        && !str_starts_with($class->getName(), 'Ubix\\DataType\\')
        && !str_starts_with($class->getName(), 'Ubix\\Exception\\')
        && !str_starts_with($class->getName(), 'Ubix\\Model\\')
        && !str_starts_with($class->getName(), 'Ubix\\Payload\\');

        if ($requiresLogger) {
            $hasLoggerDependency = $class->hasMethod('__construct')
            && count($class->getMethod('__construct')->getParameters()) >= 1
            && (string)$class->getMethod('__construct')->getParameters()[0]->getType() === Logger::class
            && $class->getMethod('__construct')->getParameters()[0]->getName() === 'logger';

            $this->assertTrue(
                $hasLoggerDependency,
                'The `' . $class->getName() . '` class must have a logger dependency of type `' . Logger::class . '` called `$logger` as its first constructor parameter but does not',
            );
        }

        //
        //  Ensure dependencies conform to standards
        //
        if ($class->hasMethod('__construct')) {
            foreach ($class->getMethod('__construct')->getParameters() as $parameter) {
                //
                //  Ensure repositories are only dependencies of supported classes
                //
                $isRepositoryDependency    = str_starts_with((string)$parameter->getType(), 'Ubix\\Repository\\');
                $classSupportsRepositories = str_starts_with($class->getName(), 'Ubix\\Service\\');

                $this->assertTrue(
                    !$isRepositoryDependency || $classSupportsRepositories,
                    'Repositories must only be dependencies of service classes but `' . $class->getName() . '` has constructor parameter `' . $parameter->getName() . '` of type `' . (string)$parameter->getType() . '`',
                );

                //
                //  Ensure SQL services are only dependencies of supported classes
                //
                $isSqlServiceDependency   = str_starts_with((string)$parameter->getType(), 'Ubix\\Service\\Sql\\');
                $classSupportsSqlServices = str_starts_with($class->getName(), 'Ubix\\Repository\\') && str_ends_with($class->getShortName(), 'SqlRepository');

                $this->assertTrue(
                    !$isSqlServiceDependency || $classSupportsSqlServices,
                    'SQL services must only be dependencies of SQL repository classes but `' . $class->getName() . '` has constructor parameter `' . $parameter->getName() . '` of type `' . (string)$parameter->getType() . '`',
                );
            }
        }
    }

    /**
     * Get a reflection class for the given class name
     *
     * @param string $className The class name
     *
     * @return ReflectionClass<object> The reflection class
     */
    private function getReflectionClass(string $className): ReflectionClass
    {
        if (!class_exists($className) && !interface_exists($className)) {
            $this->fail('`' . $className . '` does not exist');
        }

        return new ReflectionClass($className);
    }
}
