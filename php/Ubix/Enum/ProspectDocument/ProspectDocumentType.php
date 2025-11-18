<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectDocument;

/**
 * Enumeration of prospect document type
 *
 * @see \Ubix\Tests\Enum\ProspectDocument\ProspectDocumentTypeTest PHPUnit test case
 */
enum ProspectDocumentType: string
{
    case BROADCASTER_AGREEMENT = 'bc_agreement';
    case DOCUMENT_2257         = 'document_2257';
}
