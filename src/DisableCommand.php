<?php

namespace Nginx\Cli;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

class DisableCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('disable')
            ->setDescription('Disable site config')
            ->addArgument('domain', InputArgument::REQUIRED, 'Project domain');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();

        if (!$this->input->getArgument('domain')) {
            $this->input->setArgument('domain', select(
                label: 'Which site do you want to disable?',
                options: $ng->getSitesEnabled(),
            ));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();
        $domain = $this->input->getArgument('domain');

        if (!$ng->isSiteEnabled($domain)) {
            throw new RuntimeException('Site is not enabled');
        }

        $ng->disableSite($domain);
        $this->output->writeln('Site is disabled!');

        return 0;
    }
}
