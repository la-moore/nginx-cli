<?php

namespace Nginx\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

class EnableCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('enable')
            ->setDescription('Enable site');
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
            label: 'Which site do you want to enable?',
            options: $ng->getSitesDisabled(),
        );

        if ($request) {
            $ng->enableSite($request);

            $this->output->writeln('Site is enabled!');
        }

        return 0;
    }
}
