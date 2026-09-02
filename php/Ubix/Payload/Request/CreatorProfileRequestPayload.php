<?php

declare(strict_types=1);

namespace Ubix\Payload\Request;

use Ubix\DataType\String\CreatorSlug;
use Ubix\DataType\String\DisplayName;
use Ubix\Enum\Creator\CreatorCategory;
use Ubix\Payload\AbstractPayload as Payload;
use Ubix\Payload\RequestPayloadInterface as RequestPayload;

/**
 * Request payload for creating a creator profile (creator-profile TDS §4.2, wizard profile step)
 *
 * @see \Ubix\Tests\Payload\Request\CreatorProfileRequestPayloadTest PHPUnit test case
 */
final class CreatorProfileRequestPayload extends Payload implements RequestPayload
{
    public DisplayName $displayName;

    public CreatorSlug $slug;

    public ?string $bio;

    public ?CreatorCategory $category;

    public ?string $faithTopic;

    /**
     * Constructor
     *
     * @param ?string $displayName The public display name
     * @param ?string $slug        The requested slug
     * @param ?string $bio         The long-form bio
     * @param ?string $category    The creator category value
     * @param ?string $faithTopic  The faith topic / denomination
     */
    public function __construct(
        ?string $displayName,
        ?string $slug,
        ?string $bio = null,
        ?string $category = null,
        ?string $faithTopic = null,
    ) {
        $this->validateAndMapField('displayName', 'displayName', $displayName);
        $this->validateAndMapField('slug', 'slug', $slug);
        $this->validateAndMapField('bio', 'bio', $bio);
        $this->validateAndMapField('category', 'category', $category);
        $this->validateAndMapField('faithTopic', 'faithTopic', $faithTopic);
        parent::__construct();
    }
}
