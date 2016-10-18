<?php

namespace Devim\Provider\StorageServiceProvider\Command;

use Isolate\ConsoleServiceProvider\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class StorageClearCommand.
 */
class StorageClearCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('storage:clear')
            ->setDescription('Remove all files from storage');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $finder = new Finder();
        $finder->files()->in($container['storage.directory']);

        foreach ($finder as $file) {
            $realPath = $file->getRealpath();
            $result = unlink($realPath) ? 'OK' : 'ERROR';
            $output->writeln(sprintf('File "%s" remove: <info>%s</info>', $realPath, $result));
        }

        $output->writeln('Storage clear <info>success</info>');
    }
}
