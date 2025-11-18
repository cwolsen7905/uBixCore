<?php

declare(strict_types=1);

namespace Ubix\Enum\Exception;

/**
 * Enumeration of global exception codes that are safe to share with customers
 *
 * @see \Ubix\Tests\Enum\Exception\CustomerFacingExceptionCodeTest PHPUnit test case
 */
enum CustomerFacingExceptionCode: int
{
    //
    //  ModelSignupApi app
    //
    case APPLICATION_APPROVED_FOR_MODEL_SIGNUP_API                     = 1000004;
    case APPLICATION_DOCUMENTS_APPROVED_FOR_MODEL_SIGNUP_API           = 1000006;
    case APPLICATION_DOCUMENTS_PENDING_REVIEW_FOR_MODEL_SIGNUP_API     = 1000003;
    case APPLICATION_MISSING_DOCUMENTS_FOR_MODEL_SIGNUP_API            = 1000002;
    case APPLICATION_MISSING_PERSONAL_DATA_FOR_MODEL_SIGNUP_API        = 1000000;
    case APPLICATION_PERSONAL_DATA_PENDING_REVIEW_FOR_MODEL_SIGNUP_API = 1000001;
    case APPLICATION_REJECTED_FOR_MODEL_SIGNUP_API                     = 1000005;

    //
    // AffiliateApi app
    //
    case ATTRIBUTION_FAILED_TEMPORARY_CODE = 400; // TEMPORARY: This needs to be reassigned
}
