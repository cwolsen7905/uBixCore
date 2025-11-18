<?php

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * PHP Code Sniffer custom sniff for to ban certain variable names
 */
class BannedVariableNamesSniff implements Sniff
{
    private const BANNED_VARIABLE_NAMES_WITH_ERROR_MESSAGES = [
        '$_COOKIE'  => 'Use of the $_COOKIE superglobal is forbidden; use PSR request objects and \Psr\Http\Message\ServerRequestInterface->getCookieParams() instead.',
        '$_ENV'     => 'Use of the $_ENV superglobal is forbidden; use the getenv() function instead.',
        '$_FILES'   => 'Use of the $_FILES superglobal is forbidden; use PSR request objects and \Psr\Http\Message\ServerRequestInterface->getUploadedFiles() instead.',
        '$_GET'     => 'Use of the $_GET superglobal is forbidden; use PSR request objects and \Psr\Http\Message\ServerRequestInterface->getQueryParams() instead.',
        '$GLOBALS'  => 'Use of the $GLOBALS superglobal is forbidden.',
        '$_POST'    => 'Use of the $_POST superglobal is forbidden; use PSR request objects and \Psr\Http\Message\ServerRequestInterface->getParsedBody() instead.',
        '$_REQUEST' => 'Use of the $_REQUEST superglobal is forbidden; use $_GET, $_POST or $_COOKIE instead.',
        '$_SERVER'  => 'Use of the $_SERVER superglobal is forbidden; use PSR request objects and \Psr\Http\Message\ServerRequestInterface->getServerParams() instead.',
    ];

    /**
     * Register this sniff for the T_VARIABLE token.
     *
     * @return array
     */
    public function register()
    {
        return [T_VARIABLE];
    }

    /**
     * Process the variables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $variableName = $phpcsFile->getTokens()[$stackPtr]['content'] ?? '';

        foreach (self::BANNED_VARIABLE_NAMES_WITH_ERROR_MESSAGES as $bannedVariableName => $errorMessage) {
            if (strcasecmp($variableName, $bannedVariableName) === 0) {
                $phpcsFile->addError($errorMessage, $stackPtr, 'Found');
            }
        }
    }
}
