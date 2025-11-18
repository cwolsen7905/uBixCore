<?php

declare(strict_types=1);

namespace Ubix\Tests\Renderer\ModelSignupApi;

use Ubix\Renderer\ModelSignupApi\JsonErrorRenderer;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Renderer\ModelSignupApi\JsonErrorRenderer
 *
 * @coversDefaultClass \Ubix\Renderer\ModelSignupApi\JsonErrorRenderer
 */
final class JsonErrorRendererTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(JsonErrorRenderer::class);
    }
}
