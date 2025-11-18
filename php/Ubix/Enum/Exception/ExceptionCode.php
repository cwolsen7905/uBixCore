<?php

declare(strict_types=1);

namespace Ubix\Enum\Exception;

/**
 * Enumeration of global codes within Project Neptune for use in throwing and catching exceptions
 *
 * Throw example:
 * ```
 * throw new \Exception(
 *     'Message that is safe to be displayed to customers.',
 *     \Ubix\Enum\Exception\ExceptionCode::SPECIFIC_EXCEPTION->value, // This code is for VSM developers only
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
    //  Allen Sun's exception codes should use integers from 10000 to 10999
    //
    case ALLEN_PLACE_HOLDER = 10000;

    //
    //  Andrew Johnson's exception codes should use integers from 11000 to 11999
    //
    case MISSING_USERNAME_FOR_PLATFORM_USER_LOGIN                    = 11000;
    case MISSING_PASSWORD_FOR_PLATFORM_USER_LOGIN                    = 11001;
    case NO_MATCHES_FOUND_FOR_PLATFORM_USER_LOGIN                    = 11002;
    case MISSING_USERNAME_FOR_PERFORMER_LOGIN                        = 11003;
    case MISSING_PASSWORD_FOR_PERFORMER_LOGIN                        = 11004;
    case NO_MATCHES_FOUND_FOR_PERFORMER_LOGIN                        = 11005;
    case INVALID_PLATFORM_USER_STATUS                                = 11006;
    case NO_COOKIE_DOMAIN_DETERMINED                                 = 11007;
    case MISSING_SLUG                                                = 11008;
    case SLUG_NOT_FOUND                                              = 11009;
    case MISSING_SCREEN_NAME                                         = 11010;
    case GIFTED_DAYS_OUT_OF_RANGE                                    = 11011;
    case SCREEN_NAME_NOT_FOUND_FOR_GIFT                              = 11012;
    case MISSING_STRING_FOR_FILTER                                   = 11013;
    case FAN_CLUB_POST_ALREADY_UNLOCKED                              = 11014;
    case NOT_ENOUGH_CREDITS                                          = 11015;
    case FAN_CLUB_POST_ALREADY_LIKED                                 = 11016;
    case FAN_CLUB_POST_ALREADY_UNLIKED                               = 11017;
    case MEMBERSHIP_ALREADY_CANCELED                                 = 11018;
    case MEMBERSHIP_INELIGIBLE_FOR_REACTIVATION                      = 11019;
    case MEMBERSHIP_FORBIDDEN                                        = 11020;
    case NO_MATCHES_FOUND_FOR_MEMBERSHIP                             = 11021;
    case FAN_CLUB_POST_NOT_UNLOCKABLE                                = 11022;
    case FAN_CLUB_IS_CLOSED                                          = 11023;
    case NO_MATCHES_FOUND_FOR_FAN_CLUB_BY_PERFORMER_ID               = 11024;
    case MONTHLY_PRICE_OUT_OF_RANGE                                  = 11025;
    case NO_STATISTICS_MATCHES_FOUND_FOR_PERFORMER_ID                = 11026;
    case ERROR_CONNECTING_TO_PDO                                     = 11027;
    case CURL_HTTP_CLIENT_ERROR                                      = 11028;
    case TRANSACTION_COST_IS_NULL                                    = 11029;
    case MESSAGE_IS_MISSING_RECIPIENT_ID                             = 11030;
    case TRANSACTION_ALREADY_PROCESSED                               = 11031;
    case LOGGED_IN_ACCOUNT_IN_REQUEST_WRONG_TYPE                     = 11032;
    case NO_MATCHES_FOUND_FOR_FAN_CLUB_POST_BY_POST_ID               = 11033;
    case FAIL_TO_DELETE_INVALID_FAN_CLUB_LIKE                        = 11034;
    case JSON_DECODE_FAILED                                          = 11035;
    case JSON_ENCODE_FAILED                                          = 11036;
    case SIMPLE_CACHE_KEY_IS_EMPTY                                   = 11037;
    case SIMPLE_CACHE_KEY_USES_RESERVED_CHARACTERS                   = 11038;
    case SIMPLE_CACHE_KEY_IS_TOO_LONG                                = 11039;
    case SIMPLE_CACHE_KEY_WRONG_TYPE                                 = 11040;
    case SESSION_ALREADY_STARTED                                     = 11041;
    case FILESTACK_INVALID_EXPIRY                                    = 11042;
    case TYPESAFE_MISMATCH                                           = 11043;
    case BANNED_MAGIC_METHOD_INVOKED_IN_CLASS_OF_CONSTANTS           = 11044;
    case MATCH_ERROR_FOR_PLATFORM_USER                               = 11045;
    case CIDR_RANGE_IS_INVALID                                       = 11046;
    case TWO_FACTOR_AUTHENTICATION_MISSING_FOR_PLATFORM_USER         = 11047;
    case TWO_FACTOR_AUTHENTICATION_INVALID_FOR_PLATFORM_USER         = 11048;
    case TWO_FACTOR_AUTHENTICATION_MISSING_FOR_XVT                   = 11049;
    case TWO_FACTOR_AUTHENTICATION_INVALID_FOR_XVT                   = 11050;
    case XVT_API_TIMED_OUT                                           = 11051;
    case CIDR_RANGE_OR_IP_ADDRESS_IS_INVALID                         = 11052;
    case LAST_USABLE_IP_ADDRESS_REACHED                              = 11053;
    case VELOCITY_CHECK_FAILED_DUE_TO_BLOCKED_IP_ADDRESS             = 11054;
    case VELOCITY_CHECK_FAILED_DUE_TO_PRIVATE_IP_ADDRESS             = 11055;
    case VELOCITY_CHECK_FAILED_DUE_TO_IP_ADDRESS_LIMIT               = 11056;
    case VELOCITY_CHECK_FAILED_DUE_TO_USERNAME_LIMIT                 = 11057;
    case GDPR_FORGOTTEN_PLATFORM_USER_STATUS                         = 11058;
    case INACTIVE_DOMAIN                                             = 11059;
    case ONECLICK_DOMAIN_MISMATCH                                    = 11060;
    case PLATFORM_USER_IS_PENDING                                    = 11061;
    case INVALID_IP_ADDRESS_FOR_GEOLOCATION_LOOKUP                   = 11062;
    case MISSING_GEOLOCATION_LOOKUP_DATA                             = 11063;
    case BEGIN_TRANSACTION_FAILED_IN_PDO                             = 11064;
    case COMMIT_TRANSACTION_FAILED_IN_PDO                            = 11065;
    case ROLL_BACK_TRANSACTION_FAILED_IN_PDO                         = 11066;
    case NO_MATCHES_FOUND_FOR_PROSPECT_APPLICATION_ID                = 11067;
    case BASE64_DECODE_FAILED                                        = 11068;
    case NO_TOOLS_ENABLED_FOR_MACHINE_CODE_REVIEW                    = 11069;
    case MACHINE_CODE_REVIEW_FILE_MD5_HASH_MISMATCH                  = 11070;
    case TEMPORARY_FILE_CREATION_ERROR                               = 11071;
    case NO_MATCHES_FOUND_FOR_PROSPECT                               = 11072;
    case DOCUMENT_2257_VSM_API_BAD_RESPONSE                          = 11073;
    case PROSPECT_APPLICATION_ID_MISMATCH                            = 11074;
    case INVALID_PAGE_FOR_PERFORMER_NAME_SUGGESTIONS                 = 11075;
    case INVALID_PAGE_SIZE_FOR_PERFORMER_NAME_SUGGESTIONS            = 11076;
    case OUT_OF_RANGE_PAGE_FOR_PERFORMER_NAME_SUGGESTIONS            = 11077;
    case APP_NAME_MISSING                                            = 11078;
    case MISSING_APPLICATION_ID                                      = 11079;
    case INVALID_FAKE_DOB_FOR_PROSPECT_CREATE_PROFILE                = 11080;
    case MISSING_STAGE_NAME_FOR_PROSPECT_CREATE_PROFILE              = 11081;
    case PROSPECT_APPLICATION_STATUS_NOT_PENDING                     = 11082;
    case PROSPECT_APPLICATION_STAGE_NAME_ALREADY_APPROVED            = 11083;
    case NO_MATCHES_FOUND_FOR_PERFORMER_NAME_SUGGESTION              = 11084;
    case NO_MATCHES_FOUND_FOR_PROSPECT_APPLICATION_APPLICATION_ID    = 11085;
    case FAKE_DATE_OF_BIRTH_IS_NOT_VALID                             = 11086;
    case FAKE_DATE_OF_BIRTH_IS_UNDERAGE                              = 11087;
    case MISSING_EMAIL_ADDRESS_FOR_PROSPECT_CONFIRM                  = 11088;
    case MISSING_CAPTCHA_FOR_RECAPTCHA                               = 11089;
    case INVALID_RECAPTCHA                                           = 11090;
    case INVALID_TYPE_FOR_PROSPECT_APPLICATION_SUBMIT                = 11091;
    case MISSING_FIRST_NAME_FOR_PROSPECT_APPLICATION                 = 11092;
    case MISSING_LAST_NAME_FOR_PROSPECT_APPLICATION                  = 11093;
    case MISSING_EMAIL_ADDRESS_FOR_PROSPECT_APPLICATION              = 11094;
    case MISSING_PHONE_NUMBER_FOR_PROSPECT_APPLICATION               = 11095;
    case INVALID_PREFERRED_CONTACT_METHOD_FOR_PROSPECT_APPLICATION   = 11096;
    case INVALID_COUNTRY_CODE_FOR_PROSPECT_APPLICATION               = 11097;
    case INVALID_STATE_CODE_FOR_PROSPECT_APPLICATION                 = 11098;
    case MISSING_PASSWORD_FOR_PROSPECT_APPLICATION                   = 11099;
    case INVALID_DATE_OF_BIRTH_FOR_PROSPECT_APPLICATION              = 11100;
    case INVALID_GENDER_FOR_PROSPECT_APPLICATION                     = 11101;
    case MISSING_STUDIO_NAME_FOR_PROSPECT_APPLICATION                = 11102;
    case BLOCKED_IP_ADDRESS                                          = 11103;
    case BLOCKED_TRACKER                                             = 11104;
    case NO_MATCHES_FOUND_FOR_BROADCASTER_ID                         = 11105;
    case NO_MATCHES_FOUND_FOR_PLATFORM_USER_SCREEN_NAME              = 11106;
    case DATE_OF_BIRTH_IS_UNDERAGE                                   = 11107;
    case DATE_OF_BIRTH_IS_NOT_VALID                                  = 11108;
    case INVALID_PASSWORD_FOR_PROSPECT_APPLICATION                   = 11109;
    case PROSPECT_REAPPLYING                                         = 11110;
    case INVALID_FROM_EMAIL_ADDRESS                                  = 11111;
    case INVALID_TO_EMAIL_ADDRESS                                    = 11112;
    case INVALID_EMAIL_SUBJECT                                       = 11113;
    case INVALID_EMAIL_BODY                                          = 11114;
    case MISSING_SLACK_CHANNEL                                       = 11115;
    case MISSING_SLACK_MESSAGE                                       = 11116;
    case SLACK_CHANNEL_NOT_WHITELISTED                               = 11117;
    case SLACK_API_ERROR                                             = 11118;
    case MISSING_COMMAND_CLASS                                       = 11119;
    case INVALID_COMMAND_CLASS                                       = 11120;
    case NO_MATCHES_FOUND_FOR_ADMIN_USER_ID                          = 11121;
    case MULTIPLE_VALID_EMAIL_BODIES                                 = 11122;
    case INVALID_REPLY_TO_EMAIL_ADDRESS                              = 11123;
    case EXISTING_EXACT_DATE_OF_BIRTH_MATCH_FOR_PROSPECT_APPLICATION = 11124;
    case EXISTING_ADMIN_USER_MATCH_FOR_PROSPECT_APPLICATION          = 11125;
    case INVALID_COMMAND_FQCN                                        = 11126;
    case S3_CLIENT_FAILED_TO_INITIALIZE                              = 11127;
    case S3_FAILED_TO_RETRIEVE_BLOB                                  = 11128;
    case PROSPECT_APPLICATION_INVALID_SIGNATURE                      = 11129;
    case S3_KEY_MISSING_SLASH                                        = 11130;
    case PROSPECT_APPLICATION_EMAIL_ALREADY_CONFIRMED                = 11131;
    case INVALID_EMAIL_CONFIRMATION_CODE                             = 11132;
    case PROSPECT_APPLICATION_FILE_TYPE_ALREADY_EXISTS               = 11133;
    case PROSPECT_APPLICATION_FILE_TYPE_NOT_FOUND                    = 11134;
    case PROSPECT_APPLICATION_IMAGE_TYPE_EXISTS                      = 11135;
    case INVALID_TYPE_OF_IDENTIFICATION_FOR_PROSPECT_CREATE_PROFILE  = 11136;
    case MISSING_ISSUING_AUTHORITY_FOR_PROSPECT_CREATE_PROFILE       = 11137;
    case MISSING_ID_NUMBER_FOR_PROSPECT_CREATE_PROFILE               = 11138;
    case PROCESS_OPEN_FAILED                                         = 11139;
    case PROSPECT_APPLICATION_MISSING_PROSPECT                       = 11140;
    case NO_MATCHES_FOUND_FOR_COUNTRY_NAME                           = 11141;
    case NO_MATCHES_FOUND_FOR_COUNTRY_ISO31661_ALPHA2                = 11142;
    case NO_MATCHES_FOUND_FOR_COUNTRY_ISO31661_ALPHA3                = 11143;
    case NO_MATCHES_FOUND_FOR_STATE_NAME                             = 11144;
    case NO_MATCHES_FOUND_FOR_STATE_ISO31662                         = 11145;
    case PROSPECT_APPLICATION_SECTION_NOT_FOUND                      = 11146;
    case PROSPECT_APPLICATION_IMAGE_INVALID                          = 11147;
    case PROSPECT_APPLICATION_PERSONAL_DETAILS_NOT_APPROVED          = 11148;
    case PROSPECT_APPLICATION_PHOTO_ID_NOT_APPROVED                  = 11149;
    case PROSPECT_APPLICATION_PHOTO_ID_FACE_NOT_APPROVED             = 11150;
    case PROSPECT_APPLICATION_PHOTO_ID_FRONT_NOT_APPROVED            = 11151;
    case PROSPECT_APPLICATION_FILE_TYPE_MISSING                      = 11152;
    case STAGE_NAME_ALREADY_IN_USE                                   = 11153;
    case INVALID_PERFORMER_STAGE_NAME                                = 11154;
    case ASCII_ART_SPLIT_FAILED                                      = 11155;
    case INVALID_PHONE_NUMBER_FOR_PROSPECT_APPLICATION               = 11156;

    //
    //  Aric Yu's exception codes should use integers from 12000 to 12999
    //
    case    ARIC_PLACEHOLDER = 12000;

    //
    //  Christopher Olsen's exception codes should use integers from 13000 to 13999
    //
    case NO_MATCHES_FOUND_FOR_PERFORMER_ID       = 13000;
    case ERROR_EXECUTING_PDO_QUERY               = 13001;
    case MISSING_TITLE_FOR_NOTIFICATION          = 13002;
    case MISSING_MESSAGE_FOR_NOTIFICATION        = 13003;
    case INVALID_RECIPIENT_TYPE_FOR_NOTIFICATION = 13004;
    case INVALID_RECIPIENT_ID_FOR_NOTIFICATION   = 13005;
    case INVALID_SENDER_IP_FOR_NOTIFICATION      = 13006;
    case INVALID_SENDER_TYPE_FOR_NOTIFICATION    = 13007;
    case INVALID_SENDER_ID_FOR_NOTIFICATION      = 13008;
    case NO_MATCHES_FOR_RECIPIENT_PERFORMER      = 13009;
    case NO_MATCHES_FOUND_FOR_PLATFORM_USER_ID   = 13010;
    case NO_MATCHES_FOR_RECIPIENT_PLATFORM_USER  = 13011;
    case GITLAB_API_ERROR                        = 13012;
    case DATE_CREATED_NULL                       = 13013;
    case RATE_TYPE_NOT_FOUND                     = 13014;
    case NO_EARLIER_ACCOUNT                      = 13015;
    case AFFILIATE_NOT_FOUND                     = 13016;
    case TRANSACTION_NOT_FOUND                   = 13017;
    case UNSUPPORTED_AFFILIATE_PROPERTY_TYPE     = 13018;

    //
    //  Dennis Morrison's exception codes should use integers from 14000 to 14999
    //
    case DENNIS_PLACEHOLDER = 14000;

    //
    //  James Grossman's exception codes should use integers from 15000 to 15999
    //
    case JAMES_PLACEHOLDER = 15000;

    //
    //  Jim Kingsepp's exception codes should use integers from 16000 to 16999
    //
    case JIM_PLACEHOLDER = 16000;

    //
    //  Joe Santiago's exception codes should use integers from 17000 to 17999
    //
    case JOE_PLACEHOLDER = 17000;

    //
    //  Kean Khauv's exception codes should use integers from 18000 to 18999
    //
    case KEAN_PLACEHOLDER = 18000;

    //
    //  Martin Krupauer's exception codes should use integers from 19000 to 19999
    //
    case MARTIN_PLACEHOLDER = 19000;

    //
    //  Matt Gioia's exception codes should use integers from 20000 to 20999
    //
    case MATT_PLACEHOLDER = 20000;

    //
    //  Mike Hogan's exception codes should use integers from 21000 to 21999
    //
    case MIKE_PLACEHOLDER = 21000;

    //
    //  Pavandeep Kaur's exception codes should use integers from 22000 to 22999
    //
    case PAVANDEEP_PLACEHOLDER = 22000;

    //
    //  Pete Evstratov's exception codes should use integers from 23000 to 23999
    //
    case PETE_EVSTRATOV_PLACEHOLDER = 23000;

    //
    //  Petr Stehlik's exception codes should use integers from 24000 to 24999
    //
    case PETR_STEHLIK_PLACEHOLDER = 24000;

    //
    //  Quash's exception codes should use integers from 25000 to 25999
    //
    case QUASH_PLACEHOLDER = 25000;

    //
    //  Ryan Bayer's exception codes should use integers from 26000 to 26999
    //
    case RYAN_BAYER_PLACEHOLDER = 26000;

    //
    //  Soe Lynn Htike's exception codes should use integers from 27000 to 27999
    //
    case SOE_LYNN_HTIKE_PLACEHOLDER = 27000;

    //
    //  Tomasz Mieczkowski's exception codes should use integers from 28000 to 28999
    //
    case TOMASZ_MIECZKOWSKI_PLACEHOLDER = 28000;

    //
    //  Zack Ford's exception codes should use integers from 29000 to 29999
    //
    case ZACK_FORD_PLACEHOLDER = 29000;

    //
    //  If you don't see your name in this list then speak to your manager and they will assign you a range of exception codes to use
    //
}
