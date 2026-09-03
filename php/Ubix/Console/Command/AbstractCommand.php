<?php

declare(strict_types=1);

namespace Ubix\Console\Command;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Symfony\Component\Console\Terminal;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Abstract command to be extended by all other commands and crons
 */
abstract class AbstractCommand extends SymfonyCommand
{
    public const COLOR_GRAY = '#999999';

    private const ASCII_TRIDENT = <<<'ASCII'
                                                    <fg=yellow>,--,.,.+’</>
                                                   <fg=yellow>/+’‛‛‛‛</>
 <fg=yellow>,\ </>                  <fg=yellow>Powered by</>                 <fg=yellow>}/(<____</>
<fg=yellow><(<#>>==================================<<<####<< <+----_>>===--</>
 <fg=yellow>’/</>                 <fg=bright-yellow>Project Neptune</>              <fg=yellow>}\\(^^^‛¯</>
                                                   <fg=yellow>\\+,_____</>
                                                    <fg=yellow>’==’"’"+,</>
ASCII;

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     *
     * @throws Exception If the command's fully qualiied class name is invalid
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        //
        //  Validate the fully qualified class name
        //
        if (!str_starts_with(get_class($this), 'Ubix\\Console\\Command\\') || !str_ends_with(get_class($this), 'Command')) {
            throw new Exception(
                'The fully qualified class name of `' . get_class($this) . '` must begin with `Ubix\\Console\\Command\\` and end with `Command` but does not',
                ExceptionCode::INVALID_COMMAND_FQCN->value,
            );
        }

        //
        //  Translate the class name into a command name (e.g. Ubix\Console\Command\Cron\Affiliates\CronExampleCommand translates to 'cron:affiliates:cronExample') and pass it into the parent's constructor
        //
        $name = get_class($this);
        $name = substr($name, strlen('Ubix\\Console\\Command\\'), -strlen('Command'));
        $name = explode('\\', $name);
        $name = array_map(function ($string) {
            return lcfirst($string);
        }, $name);
        $name = implode(':', $name);

        parent::__construct($name);
    }

    /**
     * Display ASCII art
     *
     * @param Output $output       Symfony output
     * @param string $ascii        ASCII text
     * @param bool   $centerOutput Whether or not to center the output (optional) (default: false)
     *
     * @throws Exception If the terminal width could not be determined
     *
     * @return void
     */
    protected function displayAscii(Output $output, string $ascii, bool $centerOutput = false): void
    {
        $asciiLines = preg_split('/\R/', $ascii);
        if ($asciiLines === false) {
            throw new Exception('Error splitting ASCII art into lines', ExceptionCode::ASCII_ART_SPLIT_FAILED->value);
        } elseif ($asciiLines === []) { // Handle an empty array
            $asciiLines = [''];
        }

        $centerSpacing = '';
        if ($centerOutput) {
            $maxLineLength = max(array_map(function ($line) {
                return strlen(strip_tags($line));
            }, $asciiLines));

            $extraSpace = (new Terminal())->getWidth() - $maxLineLength;
            if ($extraSpace > 0) {
                $centerSpacing = str_repeat(' ', (int)floor($extraSpace / 2));
            }
        }

        $output->write(PHP_EOL);
        foreach ($asciiLines as $line) {
            $output->writeln($centerSpacing . $line);
        }
        $output->write(PHP_EOL);
    }

    /**
     * Display the Project Neptune trident ASCII art
     *
     * @param Output $output Symfony output
     *
     * @return void
     */
    protected function displayAsciiTrident(Output $output): void
    {
        $this->displayAscii($output, self::ASCII_TRIDENT, true);
    }
}
