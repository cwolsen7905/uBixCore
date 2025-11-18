<?php
namespace Ubix\Console\Cron;

use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Ubix\Service\AnsiColorService;
use Ubix\Service\GitService;
//use Ubix\Service\PhpCsService;
//use Ubix\Service\PhpstanService;

class CronBuildMpCodeCache extends Command
{
    protected static $defaultName = 'cron:buil-mp-code-cache';

    public function __construct(
        private Logger $logger,
        private AnsiColorService $ansiColorService,
        private GitService $gitService,
        //private PhpCsService $phpCsService,
	//private PhpstanService $phpStanService
    ) {

        // Call the parent constructor with the command name
        // This is necessary to ensure the command is registered correctly
        parent::__construct(self::$defaultName);
    
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {


		// Get list of files to validate from arguments or from git
		$FILES = $input->getArgument('file') ?: array_map( function( $fileData ) {
				return $fileData['file'];
			}, $this->gitService->getFiles( 'unstaged' ) );

   		if( empty( $FILES ) ) {

			$message = "No files/changes found to validate. Are you sure you know what you are doing?";
	   		fwrite( STDERR, $message );
	    	return Command::FAILURE;
        }

        //
        //	List all the files being validated
        //
        foreach( $FILES as $id => $file ) {

            $message = sprintf("<ansi fg='FG_YELLOW' bold='false'>%s:</ansi> <ansi fg='FG_BLUE' bold='false'>%s</ansi>\n", $id, $file );

            fwrite( STDOUT, $this->ansiColorService->parse( $message ) );

        }

        //	Run the files through phpcs
        //$this->phpCsService->phpcs($FILES);

		// Run the files through phpstan
		//$this->phpStanService->phpstan( $FILES );


        return Command::SUCCESS;

    }

    protected function configure() {
        $this
            ->setDescription('Build all mp-code data into cache')
            ->setHelp(
                <<<HELP
This command validates source files through PHP CodeSniffer, PHPStan and PHPUnit.
By default, it checks all unstaged files in the current branch. If you want to validate specific files, you can pass them as arguments.

Usage:
  neptune code:valide [<file>...]
HELP)
			->addArgument('file', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Files to validate');

    }

}
