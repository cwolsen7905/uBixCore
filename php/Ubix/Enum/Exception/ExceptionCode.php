<?php

declare(strict_types=1);

namespace Ubix\Enum\Exception;

/**
 * Enumeration of global codes within uBixCore for use in throwing and catching exceptions
 *
 * Throw example:
 * ```
 * throw new \Exception(
 *     'Message that is safe to be displayed to customers.',
 *     \Ubix\Enum\Exception\ExceptionCode::SPECIFIC_EXCEPTION->value, // This code is for uBixCore developers only
 * );
 * ```
 *
 * Catch example:
 * ```
 * try {
 *     doSomething();
 * } catch (\Exception $e) {
 *     if ($e->getCode() === \Ubix\Enum\Exception\ExceptionCode::SPECIFIC_EXCEPTION->value) {
 *         specificHandling();
 *     } else {
 *         genericHandling();
 *     }
 * }
 * ```
 *
 * @see \Ubix\Tests\Enum\Exception\ExceptionCodeTest PHPUnit test case
 */
enum ExceptionCode: int
{
    //
    //  Framework codes. Values are stable once published -- never renumber or reuse a
    //  removed value, because hosts and API clients may already match on it.
    //
    case NO_COOKIE_DOMAIN_DETERMINED                  = 11007;
    case MISSING_STRING_FOR_FILTER                    = 11013;
    case ERROR_CONNECTING_TO_PDO                      = 11027;
    case CURL_HTTP_CLIENT_ERROR                       = 11028;
    case JSON_DECODE_FAILED                           = 11035;
    case JSON_ENCODE_FAILED                           = 11036;
    case SIMPLE_CACHE_KEY_IS_EMPTY                    = 11037;
    case SIMPLE_CACHE_KEY_USES_RESERVED_CHARACTERS    = 11038;
    case SIMPLE_CACHE_KEY_IS_TOO_LONG                 = 11039;
    case SIMPLE_CACHE_KEY_WRONG_TYPE                  = 11040;
    case SESSION_ALREADY_STARTED                      = 11041;
    case FILESTACK_INVALID_EXPIRY                     = 11042;
    case CIDR_RANGE_IS_INVALID                        = 11046;
    case CIDR_RANGE_OR_IP_ADDRESS_IS_INVALID          = 11052;
    case LAST_USABLE_IP_ADDRESS_REACHED               = 11053;
    case BEGIN_TRANSACTION_FAILED_IN_PDO              = 11064;
    case COMMIT_TRANSACTION_FAILED_IN_PDO             = 11065;
    case ROLL_BACK_TRANSACTION_FAILED_IN_PDO          = 11066;
    case BASE64_DECODE_FAILED                         = 11068;
    case NO_TOOLS_ENABLED_FOR_MACHINE_CODE_REVIEW     = 11069;
    case MACHINE_CODE_REVIEW_FILE_MD5_HASH_MISMATCH   = 11070;
    case TEMPORARY_FILE_CREATION_ERROR                = 11071;
    case APP_NAME_MISSING                             = 11078;
    case MISSING_CAPTCHA_FOR_RECAPTCHA                = 11089;
    case INVALID_FROM_EMAIL_ADDRESS                   = 11111;
    case INVALID_TO_EMAIL_ADDRESS                     = 11112;
    case INVALID_EMAIL_SUBJECT                        = 11113;
    case INVALID_EMAIL_BODY                           = 11114;
    case MISSING_SLACK_CHANNEL                        = 11115;
    case MISSING_SLACK_MESSAGE                        = 11116;
    case SLACK_CHANNEL_NOT_WHITELISTED                = 11117;
    case SLACK_API_ERROR                              = 11118;
    case MISSING_COMMAND_CLASS                        = 11119;
    case INVALID_COMMAND_CLASS                        = 11120;
    case MULTIPLE_VALID_EMAIL_BODIES                  = 11122;
    case INVALID_REPLY_TO_EMAIL_ADDRESS               = 11123;
    case INVALID_COMMAND_FQCN                         = 11126;
    case S3_CLIENT_FAILED_TO_INITIALIZE               = 11127;
    case S3_FAILED_TO_RETRIEVE_BLOB                   = 11128;
    case S3_KEY_MISSING_SLASH                         = 11130;
    case PROCESS_OPEN_FAILED                          = 11139;
    case NO_MATCHES_FOUND_FOR_COUNTRY_NAME            = 11141;
    case NO_MATCHES_FOUND_FOR_COUNTRY_ISO31661_ALPHA2 = 11142;
    case NO_MATCHES_FOUND_FOR_COUNTRY_ISO31661_ALPHA3 = 11143;
    case NO_MATCHES_FOUND_FOR_STATE_NAME              = 11144;
    case NO_MATCHES_FOUND_FOR_STATE_ISO31662          = 11145;
    case IMAGE_INVALID                                = 11147;
    case ASCII_ART_SPLIT_FAILED                       = 11155;
    case ERROR_EXECUTING_PDO_QUERY                    = 13001;
    case USER_NOT_FOUND                               = 13019;
    case VALIDATION_FAILED                            = 29003;

    //
    //  Host-application codes still defined here for compatibility. Product-specific codes
    //  belong in the host's own enum; these move out in a coordinated change.
    //
    case CREATOR_ALREADY_EXISTS = 29001;
    case CREATOR_SLUG_TAKEN     = 29002;
}
