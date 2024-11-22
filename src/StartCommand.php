<?php

namespace Nginx\Cli;

use Nginx\Cli\Helpers\Nginx;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('start')
            ->setDescription('Nginx start');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();

        $ng->run('invoke-rc.d nginx start');
        $output->writeln('Nginx started');

        return 0;
    }
}
