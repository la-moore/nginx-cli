<?php

namespace Nginx\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class NewCommand extends Command
{
    /** @var InputInterface */
    public $input;

    /** @var OutputInterface */
    public $output;

    protected function configure()
    {
        $this->setName('new')
            ->setDescription('NginxCLI Test')
            ->addArgument('domain', InputArgument::REQUIRED, 'Project domain');
    }


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }


    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$this->input->getArgument('domain')) {
            $this->input->setArgument('domain', text(
                label: 'What is the domain of your project?',
                placeholder: 'E.g. example.com',
                required: 'The project domain is required.',
                validate: fn ($value) => preg_match('/[^\pL\pN\-_.]/', $value) !== 0
                    ? 'The name may only contain letters, numbers, dashes, underscores, and periods.'
                    : null,
            ));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nginx = new NginxConfig();
        $ng = new Nginx();

        $nginx->domain = $this->input->getArgument('domain');

        $nginx->port = $this->askForPort($nginx->port);
        $nginx->root = $this->askForRoot($nginx->root .'/'. $nginx->domain);
        $nginx->index = $this->askForIndex($nginx->index);
        $nginx->indexFallback = $this->askForIndexFallback($nginx->indexFallback);

        $nginx->enablePhpFastCgi = $this->askForPhpFpm($nginx->enablePhpFastCgi);

        if ($nginx->enablePhpFastCgi) {
            $nginx->phpSock = $this->askForPhpSock($nginx->phpSock);
        }

        $nginx->ssl = $this->askForSsl();

        if ($nginx->ssl) {
            $nginx->redirectToSsl = $this->askForRedirectToSsl();
        }

        $nginx->enableGzip = $this->askForGzip($nginx->enableGzip);
        $nginx->enableSecurity = $this->askForSecurity($nginx->enableSecurity);

        $ng->createSite($nginx->domain, (string) $nginx);

        $this->output->writeln('<info>Site is added!</info>');

        return 0;
    }



    protected function askForSsl($default = false)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = confirm(
            label: 'Do you want to enable SSL?',
            default: $default,
        );

        return $request;
    }

    protected function askForIndexFallback($default = false)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = confirm(
            label: 'Do you want to enable index fallback?',
            default: $default,
        );

        return $request;
    }

    protected function askForPhpFpm($default = false)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = confirm(
            label: 'Do you want to enable PHP-FPM?',
            default: $default,
        );

        return $request;
    }

    protected function askForRedirectToSsl($default = false)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = confirm(
            label: 'Do you want to redirect to SSL?',
            default: $default,
        );

        return $request;
    }

    protected function askForGzip($default = false)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = confirm(
            label: 'Do you want to enable gzip?',
            default: $default,
        );

        return $request;
    }

    protected function askForSecurity($default = false)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = confirm(
            label: 'Do you want to enable security?',
            default: $default,
        );

        return $request;
    }

    protected function askForPort($default = 0)
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = text(
            label: 'What port do you want to use?',
            placeholder: 'E.g. 80',
            default: $default,
            hint: 'Leave empty to use the default port.'
        );

        return $request;
    }

    protected function askForRoot($default = '')
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = text(
            label: 'What is the root of your project?',
            placeholder: 'E.g. /var/www/html',
            default: $default
        );

        return $request;
    }

    protected function askForIndex($default = '')
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = text(
            label: 'What is the index of your project?',
            placeholder: 'E.g. index.html',
            default: $default
        );

        return $request;
    }

    protected function askForPhpSock($default = '')
    {
        if (!$this->input->isInteractive()) {
            return $default;
        }

        $request = text(
            label: 'What is the PHP path of your project?',
            placeholder: 'E.g. /var/run/php/php8.2-fpm',
            default: $default
        );

        return $request;
    }
}
