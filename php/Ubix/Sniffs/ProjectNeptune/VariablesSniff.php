<?php

namespace Ubix\Sniffs\UbixCore;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class VariablesSniff implements Sniff
{

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
	 * @param File $phpcs_file The file being scanned.
	 * @param int  $stack_ptr  The position of the current token in the stack.
	 */
	public function process(File $phpcs_file, $stack_ptr)
	{

		$tokens   = $phpcs_file->getTokens();
		$var_name = ltrim($tokens[$stack_ptr]['content'], '$');

		// Skip $_SESSION
		if ($var_name == '_SESSION') { // NOT_IMPLEMENTED: this could be changed to an exceptions array that could be passed in from phpcs.xml then the sniff could be renamed to something like CamelCaseVariableNamesSniff making it more generic and not a UbixCore specific sniff
			return;
		}

		// Check for lower camel case
		$is_lower_camel_case = preg_match('/^[a-z]+(?:[A-Z0-9][a-z0-9]*)*$/', $var_name);

		if( !$is_lower_camel_case ) {

			$phpcs_file->addError(
				'Variable "$%s" must be lowerCamelCase only.',
				$stack_ptr,
				'InvalidVariableName',
				[$var_name]
			);

		}

	}

}