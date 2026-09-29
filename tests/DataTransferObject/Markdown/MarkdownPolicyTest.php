<?php

declare(strict_types=1);

namespace Ubix\Tests\DataTransferObject\Markdown;

use Ubix\DataTransferObject\Markdown\MarkdownPolicy;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\DataTransferObject\Markdown\MarkdownPolicy
 *
 * @coversDefaultClass \Ubix\DataTransferObject\Markdown\MarkdownPolicy
 * @see                \Ubix\Tests\Tests\DataTransferObject\Markdown\MarkdownPolicyTestTest PHPUnit test case
 */
final class MarkdownPolicyTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MarkdownPolicy::class);
    }

    /**
     * The defaults are the cautious ones: no `rel`, no `target`, headings
     * untouched, and only schemes that cannot execute.
     *
     * @return void
     */
    public function testDefaultsAreConservative(): void
    {
        $policy = new MarkdownPolicy(allowedTags: ['p']);

        $this->assertSame(['p'], $policy->allowedTags);
        $this->assertSame('', $policy->linkRel);
        $this->assertSame('', $policy->linkTarget);
        $this->assertSame(1, $policy->minHeadingLevel);
        $this->assertSame(['http', 'https', 'mailto'], $policy->allowedUriSchemes);
    }
}
