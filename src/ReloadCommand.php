<?php

namespace Nginx\Cli;

use Nginx\Cli\Helpers\Nginx;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReloadCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('reload')
            ->setDescription('Nginx reload');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();

        $ng->run('invoke-rc.d nginx reload');
        $output->writeln('Nginx reloaded');

        return 0;
    }
}
