<?php
/**
 * Verifies that a @throws tag exists for each exception type a function throws.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Ubix\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class FunctionCommentThrowTagSniff implements Sniff
{


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return [T_FUNCTION];

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token
	 *                                               in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		if (isset($tokens[$stackPtr]['scope_closer']) === false) {
			// Abstract or incomplete.
			return;
		}

		$find   = Tokens::$methodPrefixes;
		$find[] = T_WHITESPACE;

		$commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
		if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
			// Function doesn't have a doc comment or is using the wrong type of comment.
			return;
		}

		$stackPtrEnd = $tokens[$stackPtr]['scope_closer'];

		// Find all the exception type tokens within the current scope.
		$thrownExceptions = [];
		$currPos          = $stackPtr;
		$foundThrows      = false;
		$unknownCount     = 0;
		do {
			$currPos = $phpcsFile->findNext([T_THROW, T_ANON_CLASS, T_CLOSURE], ($currPos + 1), $stackPtrEnd);
			if ($currPos === false) {
				break;
			}

			if ($tokens[$currPos]['code'] !== T_THROW) {
				$currPos = $tokens[$currPos]['scope_closer'];
				continue;
			}

			$foundThrows = true;

			/*
				If we can't find a NEW, we are probably throwing
				a variable or calling a method.

				If we're throwing a variable, and it's the same variable as the
				exception container from the nearest 'catch' block, we take that exception
				as it is likely to be a re-throw.

				If we can't find a matching catch block, or the variable name
				is different, it's probably a different variable, so we ignore it,
				but they still need to provide at least one @throws tag, even through we
				don't know the exception class.
			*/

			$nextToken = $phpcsFile->findNext(T_WHITESPACE, ($currPos + 1), null, true);
			if ($tokens[$nextToken]['code'] === T_NEW
				|| $tokens[$nextToken]['code'] === T_NS_SEPARATOR
				|| $tokens[$nextToken]['code'] === T_STRING
			) {
				if ($tokens[$nextToken]['code'] === T_NEW) {
					$currException = $phpcsFile->findNext(
						[
							T_NS_SEPARATOR,
							T_STRING,
						],
						$currPos,
						$stackPtrEnd,
						false,
						null,
						true
					);
				} else {
					$currException = $nextToken;
				}

				if ($currException !== false) {
					$endException = $phpcsFile->findNext(
						[
							T_NS_SEPARATOR,
							T_STRING,
						],
						($currException + 1),
						$stackPtrEnd,
						true,
						null,
						true
					);

					if ($endException === false) {
						$thrownExceptions[] = $tokens[$currException]['content'];
					} else {
						$thrownExceptions[] = $phpcsFile->getTokensAsString($currException, ($endException - $currException));
					}
				}//end if
			} else if ($tokens[$nextToken]['code'] === T_VARIABLE) {
				// Find the nearest catch block in this scope and, if the caught var
				// matches our re-thrown var, use the exception types being caught as
				// exception types that are being thrown as well.
				$catch = $phpcsFile->findPrevious(
					T_CATCH,
					$currPos,
					$tokens[$stackPtr]['scope_opener'],
					false,
					null,
					false
				);

				if ($catch !== false) {
					$thrownVar = $phpcsFile->findPrevious(
						T_VARIABLE,
						($tokens[$catch]['parenthesis_closer'] - 1),
						$tokens[$catch]['parenthesis_opener']
					);

					if ($tokens[$thrownVar]['content'] === $tokens[$nextToken]['content']) {
						$exceptions = explode('|', $phpcsFile->getTokensAsString(($tokens[$catch]['parenthesis_opener'] + 1), ($thrownVar - $tokens[$catch]['parenthesis_opener'] - 1)));
						foreach ($exceptions as $exception) {
							$thrownExceptions[] = trim($exception);
						}
					}
				}
			} else {
				++$unknownCount;
			}//end if
		} while ($currPos < $stackPtrEnd && $currPos !== false);

		if ($foundThrows === false) {
			// No throw statements found in this method.
			// Flag any @throws tags in the docblock as useless.
			$commentStart = $tokens[$commentEnd]['comment_opener'];
			foreach ($tokens[$commentStart]['comment_tags'] as $tagPos) {
				if ($tokens[$tagPos]['content'] !== '@throws') {
					continue;
				}

				$error = 'Useless @throws tag; method does not throw any exceptions';
				// Report on the @throws tag itself
				$phpcsFile->addError($error, $tagPos, 'ThrowsNotFoundButDocumented');
			}
			return;
		}

		// Only need one @throws tag for each type of exception thrown.
		$thrownExceptions = array_unique($thrownExceptions);

		$throwTags    = [];
		$commentStart = $tokens[$commentEnd]['comment_opener'];
		foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
			if ($tokens[$tag]['content'] !== '@throws') {
				continue;
			}

			if ($tokens[($tag + 2)]['code'] === T_DOC_COMMENT_STRING) {
				$exception = $tokens[($tag + 2)]['content'];
				$space     = strpos($exception, ' ');
				if ($space !== false) {
					$exception = substr($exception, 0, $space);
				}

				$throwTags[$exception] = true;
			}
		}

		if (empty($throwTags) === true) {
			$error = 'Missing @throws tag in function comment';
			$phpcsFile->addError($error, $commentEnd, 'Missing');
			return;
		}

		// Make sure @throws tag count matches thrown count.
		$thrownCount = (count($thrownExceptions) + $unknownCount);
		$tagCount    = count($throwTags);
		if ($thrownCount !== $tagCount) {
			$error = 'Expected %s @throws tag(s) in function comment; %s found';
			$data  = [
				$thrownCount,
				$tagCount,
			];
			$phpcsFile->addError($error, $commentEnd, 'WrongNumber', $data);
		}

		foreach ($thrownExceptions as $throw) {
			if (isset($throwTags[$throw]) === true) {
				continue;
			}

			foreach ($throwTags as $tag => $ignore) {
				if (strrpos($tag, $throw) === (strlen($tag) - strlen($throw))) {
					continue 2;
				}
			}

			$error = 'Missing @throws tag for "%s" exception';
			$data  = [$throw];
			$phpcsFile->addError($error, $commentEnd, 'Missing', $data);
		}

		//--------------------------------------------------------------------------
		// New: Flag @throws tags not actually thrown in this method
		//--------------------------------------------------------------------------
		foreach ($throwTags as $tagException => $_) {
			if (!in_array($tagException, $thrownExceptions, true)) {
				$error = 'Useless @throws tag for "%s" exception; not thrown in function';
				$phpcsFile->addError($error, $commentEnd, 'ThrowsNotThrown', [$tagException]);
			}
		}

		//--------------------------------------------------------------------------
		// New: Ensure @throws tags are in alphabetical order
		//--------------------------------------------------------------------------
		$original = array_keys($throwTags);
		$sorted   = $original;
		sort($sorted, SORT_STRING);

		if ($original !== $sorted) {
			$fix = $phpcsFile->addFixableError(
				'@throws tags must be in alphabetical order',
				$commentEnd,
				'ThrowsNotSorted'
			);

			if ($fix) {
				$docStart = $commentStart;
				$docEnd   = $commentEnd;

				// Grab the entire doc-block
				$block = $phpcsFile->getTokensAsString(
					$docStart,
					($docEnd - $docStart + 1)
				);

				// Split out each line
				$lines = preg_split('/\r\n|\r|\n/', $block);

				// Find which lines are @throws
				$indices = [];
				foreach ($lines as $i => $line) {
					if (strpos($line, '@throws') !== false) {
						$indices[] = $i;
					}
				}

				if (!empty($indices)) {
					$first     = reset($indices);
					$last      = end($indices);
					// Detect indentation from the first @throws line
					$indentPos = strpos($lines[$first], '@throws');
					$indent    = ($indentPos !== false)
							? substr($lines[$first], 0, $indentPos)
							: '';

					/*
					// Build the new, sorted @throws lines
					$newTags = [];
					foreach ($sorted as $ex) {
						$newTags[] = $indent . '@throws ' . $ex;
					}
					*/

					// Build the new, sorted @throws lines, preserving descriptions
					$newTags = [];
					// Grab the original @throws lines
					$originalLines = array_slice($lines, $first, $last - $first + 1);
					$lineMap = [];
					foreach ($originalLines as $line) {
						// Capture the exception name (first token after @throws)
						if (preg_match('/@throws\s+(\S+)/', $line, $m)) {
							$lineMap[$m[1]] = $line;
						}
					}
					// Reassemble in sorted order, using original lines when available
					foreach ($sorted as $ex) {
						if (isset($lineMap[$ex])) {
							$newTags[] = $lineMap[$ex];
						} else {
							// Fallback if somehow missing
							$newTags[] = $indent . '@throws ' . $ex;
						}
					}

					// Reassemble the doc-block with the sorted lines
					$newLines = array_merge(
						array_slice($lines, 0, $first),
						$newTags,
						array_slice($lines, $last + 1)
					);

					$newBlock = implode($phpcsFile->eolChar, $newLines);

					// And apply it in one fixer changeset
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($docStart, $newBlock);
					for ($pos = $docStart + 1; $pos <= $docEnd; $pos++) {
						$phpcsFile->fixer->replaceToken($pos, '');
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}

	}//end process()


}//end class
