<?php

declare(strict_types=1);

namespace Ubix\Enum;

// phpcs:ignore Ubix.Imports.UseAlias.InvalidAlias
use Fig\Http\Message\StatusCodeInterface as HttpStatusCode;

/**
 * Enumeration of HTTP status codes
 *
 * @see \Ubix\Tests\Enum\StatusCode PHPUnit test case
 * @see \Ubix\Tests\Enum\StatusCodeTest PHPUnit test case
 */
enum StatusCode: int
{
    // Informational 1xx
    case CONTINUE            = HttpStatusCode::STATUS_CONTINUE;
    case SWITCHING_PROTOCOLS = HttpStatusCode::STATUS_SWITCHING_PROTOCOLS;
    case PROCESSING          = HttpStatusCode::STATUS_PROCESSING;
    case EARLY_HINTS         = HttpStatusCode::STATUS_EARLY_HINTS;

    // Successful 2xx
    case OK                            = HttpStatusCode::STATUS_OK;
    case CREATED                       = HttpStatusCode::STATUS_CREATED;
    case ACCEPTED                      = HttpStatusCode::STATUS_ACCEPTED;
    case NON_AUTHORITATIVE_INFORMATION = HttpStatusCode::STATUS_NON_AUTHORITATIVE_INFORMATION;
    case NO_CONTENT                    = HttpStatusCode::STATUS_NO_CONTENT;
    case RESET_CONTENT                 = HttpStatusCode::STATUS_RESET_CONTENT;
    case PARTIAL_CONTENT               = HttpStatusCode::STATUS_PARTIAL_CONTENT;
    case MULTI_STATUS                  = HttpStatusCode::STATUS_MULTI_STATUS;
    case ALREADY_REPORTED              = HttpStatusCode::STATUS_ALREADY_REPORTED;
    case IM_USED                       = HttpStatusCode::STATUS_IM_USED;

    // Redirection 3xx
    case MULTIPLE_CHOICES   = HttpStatusCode::STATUS_MULTIPLE_CHOICES;
    case MOVED_PERMANENTLY  = HttpStatusCode::STATUS_MOVED_PERMANENTLY;
    case FOUND              = HttpStatusCode::STATUS_FOUND;
    case SEE_OTHER          = HttpStatusCode::STATUS_SEE_OTHER;
    case NOT_MODIFIED       = HttpStatusCode::STATUS_NOT_MODIFIED;
    case USE_PROXY          = HttpStatusCode::STATUS_USE_PROXY;
    case RESERVED           = HttpStatusCode::STATUS_RESERVED;
    case TEMPORARY_REDIRECT = HttpStatusCode::STATUS_TEMPORARY_REDIRECT;
    case PERMANENT_REDIRECT = HttpStatusCode::STATUS_PERMANENT_REDIRECT;

    // Client Error 4xx
    case BAD_REQUEST                     = HttpStatusCode::STATUS_BAD_REQUEST;
    case UNAUTHORIZED                    = HttpStatusCode::STATUS_UNAUTHORIZED;
    case PAYMENT_REQUIRED                = HttpStatusCode::STATUS_PAYMENT_REQUIRED;
    case FORBIDDEN                       = HttpStatusCode::STATUS_FORBIDDEN;
    case NOT_FOUND                       = HttpStatusCode::STATUS_NOT_FOUND;
    case METHOD_NOT_ALLOWED              = HttpStatusCode::STATUS_METHOD_NOT_ALLOWED;
    case NOT_ACCEPTABLE                  = HttpStatusCode::STATUS_NOT_ACCEPTABLE;
    case PROXY_AUTHENTICATION_REQUIRED   = HttpStatusCode::STATUS_PROXY_AUTHENTICATION_REQUIRED;
    case REQUEST_TIMEOUT                 = HttpStatusCode::STATUS_REQUEST_TIMEOUT;
    case CONFLICT                        = HttpStatusCode::STATUS_CONFLICT;
    case GONE                            = HttpStatusCode::STATUS_GONE;
    case LENGTH_REQUIRED                 = HttpStatusCode::STATUS_LENGTH_REQUIRED;
    case PRECONDITION_FAILED             = HttpStatusCode::STATUS_PRECONDITION_FAILED;
    case PAYLOAD_TOO_LARGE               = HttpStatusCode::STATUS_PAYLOAD_TOO_LARGE;
    case URI_TOO_LONG                    = HttpStatusCode::STATUS_URI_TOO_LONG;
    case UNSUPPORTED_MEDIA_TYPE          = HttpStatusCode::STATUS_UNSUPPORTED_MEDIA_TYPE;
    case RANGE_NOT_SATISFIABLE           = HttpStatusCode::STATUS_RANGE_NOT_SATISFIABLE;
    case EXPECTATION_FAILED              = HttpStatusCode::STATUS_EXPECTATION_FAILED;
    case IM_A_TEAPOT                     = HttpStatusCode::STATUS_IM_A_TEAPOT;
    case MISDIRECTED_REQUEST             = HttpStatusCode::STATUS_MISDIRECTED_REQUEST;
    case UNPROCESSABLE_ENTITY            = HttpStatusCode::STATUS_UNPROCESSABLE_ENTITY;
    case LOCKED                          = HttpStatusCode::STATUS_LOCKED;
    case FAILED_DEPENDENCY               = HttpStatusCode::STATUS_FAILED_DEPENDENCY;
    case TOO_EARLY                       = HttpStatusCode::STATUS_TOO_EARLY;
    case UPGRADE_REQUIRED                = HttpStatusCode::STATUS_UPGRADE_REQUIRED;
    case PRECONDITION_REQUIRED           = HttpStatusCode::STATUS_PRECONDITION_REQUIRED;
    case TOO_MANY_REQUESTS               = HttpStatusCode::STATUS_TOO_MANY_REQUESTS;
    case REQUEST_HEADER_FIELDS_TOO_LARGE = HttpStatusCode::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE;
    case UNAVAILABLE_FOR_LEGAL_REASONS   = HttpStatusCode::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS;

    // Server Error 5xx
    case INTERNAL_SERVER_ERROR           = HttpStatusCode::STATUS_INTERNAL_SERVER_ERROR;
    case NOT_IMPLEMENTED                 = HttpStatusCode::STATUS_NOT_IMPLEMENTED;
    case BAD_GATEWAY                     = HttpStatusCode::STATUS_BAD_GATEWAY;
    case SERVICE_UNAVAILABLE             = HttpStatusCode::STATUS_SERVICE_UNAVAILABLE;
    case GATEWAY_TIMEOUT                 = HttpStatusCode::STATUS_GATEWAY_TIMEOUT;
    case VERSION_NOT_SUPPORTED           = HttpStatusCode::STATUS_VERSION_NOT_SUPPORTED;
    case VARIANT_ALSO_NEGOTIATES         = HttpStatusCode::STATUS_VARIANT_ALSO_NEGOTIATES;
    case INSUFFICIENT_STORAGE            = HttpStatusCode::STATUS_INSUFFICIENT_STORAGE;
    case LOOP_DETECTED                   = HttpStatusCode::STATUS_LOOP_DETECTED;
    case NOT_EXTENDED                    = HttpStatusCode::STATUS_NOT_EXTENDED;
    case NETWORK_AUTHENTICATION_REQUIRED = HttpStatusCode::STATUS_NETWORK_AUTHENTICATION_REQUIRED;
}
