<?php

namespace Nginx\Cli;

use Nginx\Cli\Helpers\Nginx;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\select;

class EnableCommand extends Command
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('enable')
            ->setDescription('Enable site config')
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
                label: 'Which site do you want to enable?',
                options: $ng->getSitesDisabled(),
            ));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ng = new Nginx();
        $domain = $this->input->getArgument('domain');

        if (!$ng->isSiteAvailable($domain)) {
            throw new RuntimeException('Site is not available');
        }

        $ng->enableSite($domain);
        $this->output->writeln('Site is enabled!');

        return 0;
    }
}
