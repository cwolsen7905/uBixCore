<?php

declare(strict_types=1);

namespace Ubix\Enum\ProspectApplication;

/**
 * Enumeration of prospect application statuses
 *
 * @see \Ubix\Tests\Enum\ProspectApplication\ProspectApplicationStatusTest PHPUnit test case
 */
enum ProspectApplicationStatus: string
{
    case MISSING_PERSONAL_DATA        = 'missing_personal_data';
    case PERSONAL_DATA_PENDING_REVIEW = 'personal_data_pending_review';
    case MISSING_DOCUMENTS            = 'missing_documents';
    case DOCUMENTS_PENDING_REVIEW     = 'documents_pending_review';
    case DOCUMENTS_APPROVED           = 'documents_approved';
    case APPROVED                     = 'approved';
    case REJECTED                     = 'rejected';
}
