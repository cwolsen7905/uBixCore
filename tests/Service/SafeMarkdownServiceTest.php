<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Markdown\MarkdownPolicy;
use Ubix\Service\SafeMarkdownService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\SafeMarkdownService
 *
 * One test per vector rather than one test asserting "it is safe". A single
 * omnibus assertion goes green the day a renderer upgrade starts letting
 * something through; a case per vector names which one.
 *
 * @coversDefaultClass \Ubix\Service\SafeMarkdownService
 * @see                \Ubix\Tests\Tests\Service\SafeMarkdownServiceTestTest PHPUnit test case
 */
final class SafeMarkdownServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(SafeMarkdownService::class);
    }

    /**
     * The prose a host actually wants renders as prose.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testRendersThePermittedSubset(): void
    {
        $html = $this->render("## Heading\n\nSome **bold** and *italic* text.\n\n- one\n- two\n\n> quoted\n");

        $this->assertStringContainsString('<h2>Heading</h2>', $html);
        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<em>italic</em>', $html);
        $this->assertStringContainsString('<li>one</li>', $html);
        $this->assertStringContainsString('<blockquote>', $html);
    }

    /**
     * Empty and whitespace-only input renders nothing, not an empty paragraph.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testEmptyInputRendersNothing(): void
    {
        $this->assertSame('', $this->render(''));
        $this->assertSame('', $this->render("   \n\n  "));
    }

    /**
     * A `<script>` in the source never becomes a script element, and its body
     * does not survive as executable markup.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testScriptIsNeverRendered(): void
    {
        $html = $this->render("Hello\n\n<script>alert(1)</script>\n");

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('</script', $html);
    }

    /**
     * An iframe is refused — the embed vector, and the one a creator is most
     * likely to reach for.
     *
     * The URL itself is *expected* in the output, as escaped text: what someone
     * typed is shown back to them verbatim rather than silently vanishing. What
     * must not survive is the markup. So this asserts inertness, not absence.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testIframeIsNeverRendered(): void
    {
        $html = $this->render('<iframe src="https://example.com/evil"></iframe>');

        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringContainsString('&lt;iframe', $html);
    }

    /**
     * An inline event handler cannot reach the output, whether or not the tag
     * carrying it would have been allowed.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testEventHandlerAttributeIsNeverRendered(): void
    {
        $html = $this->render('<img src="x" onerror="alert(1)"> and <b onclick="alert(2)">text</b>');

        // Inertness, not absence: the handler text is shown back as escaped
        // characters, and there is no element for it to be an attribute of.
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<b ', $html);
        $this->assertStringContainsString('&lt;img', $html);
        $this->assertStringContainsString('&lt;b', $html);
    }

    /**
     * A `javascript:` link loses its href and keeps its text.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testJavascriptLinkLosesItsHref(): void
    {
        $html = $this->render('[click me](javascript:alert(1))');

        $this->assertStringNotContainsStringIgnoringCase('javascript:', $html);
        $this->assertStringContainsString('click me', $html);
    }

    /**
     * A `data:` link loses its href — the other scheme that executes.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testDataLinkLosesItsHref(): void
    {
        $html = $this->render('[x](data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==)');

        $this->assertStringNotContainsStringIgnoringCase('data:text/html', $html);
    }

    /**
     * An ordinary link survives and is qualified with the host's `rel` and
     * `target`, which is the whole reason the policy carries them.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testPermittedLinkIsKeptAndQualified(): void
    {
        $html = $this->render('[site](https://example.com/page)');

        $this->assertStringContainsString('href="https://example.com/page"', $html);
        $this->assertStringContainsString('rel="nofollow ugc noopener noreferrer"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
    }

    /**
     * A relative link is not a scheme and stays — it cannot execute.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testRelativeLinkIsKept(): void
    {
        $html = $this->render('[about](/about)');

        $this->assertStringContainsString('href="/about"', $html);
    }

    /**
     * A Markdown image is not rendered when the policy omits `img`; its alt text
     * survives as words. Images belong to the media pipeline, not to prose.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testImageIsDroppedWhenNotPermitted(): void
    {
        $html = $this->render('![a picture](https://example.com/p.png)');

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('example.com/p.png', $html);
    }

    /**
     * A heading shallower than the floor is pushed down, so a document cannot
     * outrank the page's own `<h1>`.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testHeadingIsPushedToTheFloor(): void
    {
        $html = $this->render("# Top\n\n### Deeper\n");

        $this->assertStringNotContainsString('<h1', $html);
        $this->assertStringContainsString('<h2>Top</h2>', $html);
        $this->assertStringContainsString('<h3>Deeper</h3>', $html);
    }

    /**
     * A tag outside the policy is unwrapped rather than deleted, so the author's
     * words are never silently lost.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testDisallowedTagIsUnwrappedNotDeleted(): void
    {
        $policy = new MarkdownPolicy(allowedTags: ['p']);
        $html   = $this->service()->render('Some **important** words.', $policy);

        $this->assertStringNotContainsString('<strong', $html);
        $this->assertStringContainsString('important', $html);
        $this->assertStringContainsString('words', $html);
    }

    /**
     * Nothing but `href` survives on a link, including attributes a future
     * renderer or extension might start emitting.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testOnlyHrefSurvivesOnALink(): void
    {
        $html = $this->render('[x](https://example.com "a title")');

        $this->assertStringContainsString('href="https://example.com"', $html);
        $this->assertStringNotContainsString('title=', $html);
    }

    /**
     * A deeply nested document is bounded rather than exhausting the stack, and
     * still returns something renderable.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testPathologicalNestingIsSurvivable(): void
    {
        $html = $this->render(str_repeat('> ', 200) . "deep\n");

        $this->assertNotSame('', $html);
        $this->assertStringContainsString('deep', $html);
    }

    /**
     * The output is a fragment, not a document — no document chrome leaks in
     * from the sanitising round-trip.
     *
     * @return void
     *
     * @covers ::render
     */
    public function testOutputIsAFragment(): void
    {
        $html = $this->render("# Title\n\nBody.\n");

        $this->assertStringNotContainsString('<html', $html);
        $this->assertStringNotContainsString('<body', $html);
        $this->assertStringNotContainsString('ubix-markdown-root', $html);
        $this->assertStringNotContainsString('<?xml', $html);
    }

    /**
     * Render with a policy shaped like a creator profile page
     *
     * @param string $markdown The source
     *
     * @return string The rendered fragment
     */
    private function render(string $markdown): string
    {
        $policy = new MarkdownPolicy(
            allowedTags:     ['p', 'br', 'strong', 'em', 'a', 'ul', 'ol', 'li', 'blockquote', 'hr', 'h2', 'h3', 'h4', 'h5', 'h6'],
            linkRel:         'nofollow ugc noopener noreferrer',
            linkTarget:      '_blank',
            minHeadingLevel: 2,
        );

        return $this->service()->render($markdown, $policy);
    }

    /**
     * The service under test
     *
     * @return SafeMarkdownService
     */
    private function service(): SafeMarkdownService
    {
        return new SafeMarkdownService($this->createStub(Logger::class));
    }
}
