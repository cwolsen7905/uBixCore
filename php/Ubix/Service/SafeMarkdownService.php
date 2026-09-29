<?php

declare(strict_types=1);

namespace Ubix\Service;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Psr\Log\LoggerInterface as Logger;
use Throwable;
use Ubix\DataTransferObject\Markdown\MarkdownPolicy;

/**
 * Render untrusted Markdown to HTML that is safe to put on a page
 *
 * Two stages, and the second is the one that makes the guarantee:
 *
 * 1. **Parse** with `league/commonmark`, configured so raw HTML in the source is
 *    escaped to text rather than passed through (`html_input: escape`), unsafe
 *    link schemes are refused (`allow_unsafe_links: false`), and nesting is
 *    bounded so a pathological document cannot exhaust the stack.
 * 2. **Enforce an allow-list over the produced HTML.** Every element the policy
 *    does not name is unwrapped to its own text, every attribute is dropped
 *    except `href` on a link, and `href` must carry a permitted scheme.
 *
 * Stage 2 exists because stage 1 is configuration, and configuration is the
 * wrong place to put a security boundary: a parser upgrade, a new extension, or
 * a default that changes underneath us would silently widen what gets through.
 * An allow-list applied to the output cannot be widened by accident — only by
 * editing the policy the host passes in.
 *
 * The host owns the policy (`MarkdownPolicy`) entirely. This service has no view
 * on which tags a surface should permit, and must not acquire one.
 *
 * It returns a fragment, not a document: no `<html>`, `<head>` or `<body>`.
 *
 * @see \Ubix\Tests\Service\SafeMarkdownServiceTest PHPUnit test case
 */
final class SafeMarkdownService
{
    /**
     * Elements whose *contents* are dropped rather than unwrapped
     *
     * Unwrapping `<b>x</b>` to `x` is right; unwrapping `<script>alert(1)</script>`
     * to `alert(1)` puts the payload on the page as text, which is merely ugly —
     * but for a `<style>` or a `<title>` it is worse, and for anything that can
     * be re-parsed it is a trap. These go, contents and all.
     */
    private const DROP_WITH_CONTENTS = ['script', 'style', 'iframe', 'object', 'embed', 'template', 'noscript'];

    /**
     * Guards against a pathological document exhausting the stack while parsing.
     */
    private const MAX_NESTING_LEVEL = 25;

    /**
     * Constructor
     *
     * @param Logger $logger PSR-3 logger
     */
    public function __construct(
        private Logger $logger,
    ) {
    }

    /**
     * Render Markdown to an HTML fragment the policy permits
     *
     * Never throws on bad input: unparseable or pathological Markdown yields the
     * escaped source as a single paragraph, because a profile page rendering
     * someone's prose as plain text is a far better outcome than a 500.
     *
     * @param string         $markdown The untrusted Markdown source
     * @param MarkdownPolicy $policy   What this host permits
     *
     * @return string An HTML fragment
     */
    public function render(string $markdown, MarkdownPolicy $policy): string
    {
        if (trim($markdown) === '') {
            return '';
        }

        try {
            $environment = new Environment([
                'allow_unsafe_links' => false,
                // Raw HTML in the source becomes text. The allow-list below is
                // the real boundary, but there is no reason to parse markup we
                // have already decided not to honour.
                'html_input'         => 'escape',
                'max_nesting_level'  => self::MAX_NESTING_LEVEL,
            ]);
            $environment->addExtension(new CommonMarkCoreExtension());

            $html = (new MarkdownConverter($environment))->convert($markdown)->getContent();
        } catch (Throwable $e) {
            $this->logger->warning('Markdown could not be parsed; falling back to escaped text', [
                'error'  => $e->getMessage(),
                'length' => strlen($markdown),
            ]);

            return sprintf('<p>%s</p>', htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8'));
        }

        return $this->enforce($html, $policy);
    }

    /**
     * Apply the policy to rendered HTML
     *
     * @param string         $html   The HTML produced by the parser
     * @param MarkdownPolicy $policy What this host permits
     *
     * @return string The HTML the policy permits
     */
    private function enforce(string $html, MarkdownPolicy $policy): string
    {
        if (trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        // A parse failure must not surface as a PHP warning, and unknown tags
        // are expected here — that is the point.
        $previous = libxml_use_internal_errors(true);
        $wrapped  = sprintf('<div id="ubix-markdown-root">%s</div>', $html);
        $loaded   = $document->loadHTML(
            '<?xml encoding="UTF-8">' . $wrapped,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($loaded === false) {
            $this->logger->warning('Rendered Markdown could not be re-parsed for sanitising; returning text only');

            return sprintf('<p>%s</p>', htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8'));
        }

        $root = $document->getElementById('ubix-markdown-root');
        if (! $root instanceof DOMElement) {
            return '';
        }

        $this->walk($root, $policy, $document);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= (string) $document->saveHTML($child);
        }

        return trim($out);
    }

    /**
     * Walk a subtree, applying the policy to each element
     *
     * Iterates over a snapshot of the children because the walk rewrites the
     * tree as it goes, and a live `DOMNodeList` shifts underneath the loop.
     *
     * @param DOMElement     $parent   The element whose children to process
     * @param MarkdownPolicy $policy   What this host permits
     * @param DOMDocument    $document The owning document
     *
     * @return void
     */
    private function walk(DOMElement $parent, MarkdownPolicy $policy, DOMDocument $document): void
    {
        $children = [];
        foreach ($parent->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if (! $child instanceof DOMElement) {
                // Comments, processing instructions and anything else that is
                // neither text nor an element carry no content worth keeping.
                $parent->removeChild($child);
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (in_array($tag, self::DROP_WITH_CONTENTS, true)) {
                $parent->removeChild($child);
                continue;
            }

            $this->walk($child, $policy, $document);

            // The floor is applied *before* the allow-list, not after: a policy
            // that correctly omits `h1` would otherwise unwrap it and lose the
            // heading altogether, instead of demoting it to a level it allows.
            $child = $this->applyHeadingFloor($child, $tag, $policy, $document);
            $tag   = strtolower($child->nodeName);

            if (! in_array($tag, $policy->allowedTags, true)) {
                $this->unwrap($child, $parent);
                continue;
            }

            $this->applyAttributes($child, $tag, $policy);
        }
    }

    /**
     * Replace an element with its own children, keeping the text
     *
     * @param DOMElement $element The element to remove
     * @param DOMElement $parent  Its parent
     *
     * @return void
     */
    private function unwrap(DOMElement $element, DOMElement $parent): void
    {
        while ($element->firstChild instanceof DOMNode) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    /**
     * Strip every attribute the policy does not permit
     *
     * Only `href` on a link survives, and only with an acceptable scheme. No
     * `style`, no `class`, no `id`, and no `on*` handler — none of which the
     * parser emits, but all of which an allow-list must refuse on principle
     * rather than on the strength of what today's parser happens to produce.
     *
     * @param DOMElement     $element The element
     * @param string         $tag     Its lower-case tag name
     * @param MarkdownPolicy $policy  What this host permits
     *
     * @return void
     */
    private function applyAttributes(DOMElement $element, string $tag, MarkdownPolicy $policy): void
    {
        $names = [];
        foreach ($element->attributes as $attribute) {
            $names[] = $attribute->nodeName;
        }

        foreach ($names as $name) {
            $keep = $tag === 'a' && strtolower($name) === 'href';
            if (! $keep) {
                $element->removeAttribute($name);
            }
        }

        if ($tag !== 'a') {
            return;
        }

        if (! $this->isPermittedUri($element->getAttribute('href'), $policy)) {
            $element->removeAttribute('href');
        }

        if ($policy->linkRel !== '') {
            $element->setAttribute('rel', $policy->linkRel);
        }

        if ($policy->linkTarget !== '') {
            $element->setAttribute('target', $policy->linkTarget);
        }
    }

    /**
     * Whether an `href` may stay
     *
     * A relative or scheme-relative URL is fine — it cannot execute. Anything
     * carrying a scheme must carry one the host named.
     *
     * @param string         $href   The attribute value
     * @param MarkdownPolicy $policy What this host permits
     *
     * @return bool True when the link may keep its `href`
     */
    private function isPermittedUri(string $href, MarkdownPolicy $policy): bool
    {
        $trimmed = trim($href);
        if ($trimmed === '') {
            return false;
        }

        // Control characters are how a blocked scheme gets smuggled past a
        // naive prefix check (`java\tscript:`), so any presence is refusal.
        if (preg_match('/[\x00-\x1F\x7F]/', $trimmed) === 1) {
            return false;
        }

        $matches = [];
        if (preg_match('/^([A-Za-z][A-Za-z0-9+.-]*):/', $trimmed, $matches) !== 1) {
            return true;
        }

        return in_array(strtolower((string) $matches[1]), $policy->allowedUriSchemes, true);
    }

    /**
     * Push a heading down to the policy's floor
     *
     * A document rendered inside a page must not outrank that page's own `<h1>`;
     * the levels below the floor are collapsed onto it rather than shifted, so
     * the outline stays contiguous.
     *
     * @param DOMElement     $element  The element
     * @param string         $tag      Its lower-case tag name
     * @param MarkdownPolicy $policy   What this host permits
     * @param DOMDocument    $document The owning document
     *
     * @return DOMElement The element to carry on with — the replacement when one was made
     */
    private function applyHeadingFloor(DOMElement $element, string $tag, MarkdownPolicy $policy, DOMDocument $document): DOMElement
    {
        $matches = [];
        if (preg_match('/^h([1-6])$/', $tag, $matches) !== 1) {
            return $element;
        }

        $level = (int) $matches[1];
        if ($level >= $policy->minHeadingLevel) {
            return $element;
        }

        $parent = $element->parentNode;
        if (! $parent instanceof DOMNode) {
            return $element;
        }

        $replacement = $document->createElement(sprintf('h%d', min(6, $policy->minHeadingLevel)));
        while ($element->firstChild instanceof DOMNode) {
            $replacement->appendChild($element->firstChild);
        }

        $parent->replaceChild($replacement, $element);

        return $replacement;
    }
}
