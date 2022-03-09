<?php
declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Command;

use Jgrasp\PrestashopMigrationPlugin\Configurator\ConfiguratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfigurationCommand extends Command
{
    /** @var ConfiguratorInterface[] */
    private array $configurators;

    public function __construct(iterable $configurators)
    {
        $this->configurators = $configurators instanceof \Traversable ? iterator_to_array($configurators) : $configurators;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->configurators as $configurator) {

            $io->title(sprintf('Start configuration of "%s"', $configurator->getName()));

            $configurator->execute();

            $io->newLine(2);
            $io->success('Configuration successfull');
            $io->writeln('---------------------------------------------------------------------------');
        }
        return Command::SUCCESS;
    }
}
