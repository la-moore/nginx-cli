<?php

namespace Nginx\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

class DisableCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('disable')
            ->setDescription('Disable site');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();

        $request = select(
            label: 'What site do you want to disable?',
            options: $ng->getSitesEnabled(),
        );

        if ($request) {
            $ng->disableSite($request);

            $this->output->writeln('Site is disabled!');
        }

        return 0;
    }
}
