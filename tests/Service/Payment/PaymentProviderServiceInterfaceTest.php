<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Payment;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Ubix\Service\Payment\PaymentProviderServiceInterface as PaymentProviderService;

/**
 * PHPUnit test case for \Ubix\Service\Payment\PaymentProviderServiceInterface
 *
 * An interface has no behaviour to test, but this one makes two claims in its
 * docblock that are load-bearing rather than stylistic, and both are checkable
 * from its own signature. They are asserted here so that a later slice adding a
 * convenience method cannot quietly retract them.
 *
 * @coversNothing
 */
final class PaymentProviderServiceInterfaceTest extends TestCase
{
    /**
     * Test that no amount anywhere on the seam is a float
     *
     * Money on this interface is minor units plus a currency. A float amount
     * cannot represent 19.99 exactly, so money that round-trips through one
     * eventually fails to reconcile by a cent -- and the cheapest place to stop
     * that is at the type.
     *
     * @return void
     */
    public function testNoMethodAcceptsOrReturnsAFloat(): void
    {
        foreach ($this->methods() as $method) {
            $returnType = $method->getReturnType();

            if ($returnType instanceof ReflectionNamedType) {
                $this->assertNotSame(
                    'float',
                    $returnType->getName(),
                    sprintf('%s() returns a float; money is minor units plus a currency', $method->getName()),
                );
            }

            foreach ($method->getParameters() as $parameter) {
                $type = $parameter->getType();

                if (!$type instanceof ReflectionNamedType) {
                    continue;
                }

                $this->assertNotSame(
                    'float',
                    $type->getName(),
                    sprintf(
                        '%s() takes $%s as a float; money is minor units plus a currency',
                        $method->getName(),
                        $parameter->getName(),
                    ),
                );
            }
        }
    }

    /**
     * Test that the seam offers no way to hand it card data
     *
     * The host built against this interface has no card-collecting form to
     * audit because the interface cannot receive one's output. That property
     * survives only as long as nobody adds a parameter that could carry a PAN,
     * expiry or CVV, so the parameter names are checked directly.
     *
     * @return void
     */
    public function testNoParameterCouldCarryCardData(): void
    {
        $forbidden = ['card', 'pan', 'cvv', 'cvc', 'securitycode', 'expiry', 'expiration', 'cardnumber'];

        foreach ($this->methods() as $method) {
            foreach ($method->getParameters() as $parameter) {
                $name = strtolower($parameter->getName());

                foreach ($forbidden as $needle) {
                    $this->assertStringNotContainsString(
                        $needle,
                        $name,
                        sprintf(
                            '%s() takes $%s; every payment path here is provider-hosted and no card data may reach this seam',
                            $method->getName(),
                            $parameter->getName(),
                        ),
                    );
                }
            }
        }
    }

    /**
     * Test that webhook verification returns a type an unverified payload cannot become
     *
     * The distinct return type is what makes the signature check impossible to
     * forget: a handler taking the verified type cannot be handed raw bytes.
     *
     * @return void
     */
    public function testWebhookVerificationReturnsTheVerifiedType(): void
    {
        $returnType = (new ReflectionMethod(PaymentProviderService::class, 'verifyWebhook'))->getReturnType();

        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame(
            'Ubix\DataTransferObject\Payment\VerifiedWebhookEvent',
            $returnType->getName(),
        );
    }

    /**
     * Every method declared on the seam
     *
     * @return array<int, ReflectionMethod> The interface's methods
     */
    private function methods(): array
    {
        return (new ReflectionClass(PaymentProviderService::class))->getMethods();
    }
}
