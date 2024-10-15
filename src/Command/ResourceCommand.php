<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Command;

use Jgrasp\PrestashopMigrationPlugin\Importer\ImporterInterface;
use Jgrasp\PrestashopMigrationPlugin\Validator\Violation;
use Sylius\Component\Core\Formatter\StringInflector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ResourceCommand extends Command
{
    private string $name;

    private ImporterInterface $importer;

    public function __construct(string $name, ImporterInterface $importer)
    {
        parent::__construct();

        $this->name     = ucfirst(StringInflector::nameToCamelCase($name));
        $this->importer = $importer;

        $this->addOption('criteria', null, InputOption::VALUE_REQUIRED, 'Filter resources.', '[]');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit number of resources.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $inCriteria = $input->getOption('criteria');
        $inCriteria = json_decode($inCriteria, true) ?? [];
        $inVerbose  = $input->getOption('verbose');
        $inLimit    = $input->getOption('limit');

        $importerSize = $this->importer->size($inCriteria, $inLimit);

        $io->title(sprintf('Start migration of "%s" with %d objects', $this->name, $importerSize));

        if ($inVerbose) {
            $progressBar = null;
        } else {
            $progressBar = new ProgressBar($output, $importerSize);
            $progressBar->setFormat('%percent:3s%% [%bar%] %elapsed:6s%/%estimated:-6s%');
        }

        $this->importer->import($inCriteria, function (int $step, array $violations) use ($progressBar, $io) {
            array_walk_recursive(
                $violations,
                function (Violation $violation) use ($io) {
                    $io->warning([
                        sprintf('%s %s not import', $this->name, $violation->getEntityId()),
                        sprintf('Reason : %s', $violation->getMessage()),
                    ]);
                }
            );

            if ($progressBar) {
                $progressBar->advance($step);
            }
        });

        if ($progressBar) {
            $progressBar->finish();
        }

        $io->newLine(2);
        $io->success('Migration successfull');
        $io->writeln('---------------------------------------------------------------------------');

        return Command::SUCCESS;
    }
}
