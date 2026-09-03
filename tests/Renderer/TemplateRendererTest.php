<?php

declare(strict_types=1);

namespace Ubix\Tests\Renderer;

use Ubix\Renderer\TemplateRenderer;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Renderer\TemplateRenderer
 *
 * @coversDefaultClass \Ubix\Renderer\TemplateRenderer
 */
final class TemplateRendererTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(TemplateRenderer::class);
    }
}
