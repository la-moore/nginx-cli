<?php

namespace Nginx\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('list')
            ->setDescription('List available sites');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();

        foreach ($ng->getSitesAvailable() as $site) {
            if ($ng->isSiteEnabled($site)) {
                $output->writeln('<info>'.$site.'</info>');
            } else {
                $output->writeln('<comment>'.$site.'</comment>');
            }
        }

        return 0;
    }
}
