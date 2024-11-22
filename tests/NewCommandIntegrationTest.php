<?php

namespace Nginx\Cli\Tests;

use Nginx\Cli\DisableCommand;
use Nginx\Cli\EnableCommand;
use Nginx\Cli\Helpers\Nginx;
use Nginx\Cli\ListCommand;
use Nginx\Cli\NewCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class NewCommandIntegrationTest extends TestCase
{
    protected Filesystem $filesystem;
    protected $baseDirectory;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->onStart();
    }

    public function onStart(): void
    {
        Nginx::testMode();

        $this->filesystem = new Filesystem();
        $this->baseDirectory = (new Nginx())->fromBaseDir('');

        $this->clearBaseDirectory();
    }

    /** @test */
    public function test_new()
    {
        $app = $this->newApp();
        $tester = new CommandTester($app->find('new'));

//        $args = array_merge(['name' => $this->scaffoldName], $args);

        $statusCode = $tester->execute([]);
        $this->assertSame(0, $statusCode);
    }

    /** @test */
    public function test_enable()
    {
        $app = $this->newApp();
        $tester = new CommandTester($app->find('enable'));

        $statusCode = $tester->execute([]);

        $this->assertSame(0, $statusCode);
    }

    /** @test */
    public function test_disable()
    {
        $app = $this->newApp();
        $tester = new CommandTester($app->find('disable'));

        $statusCode = $tester->execute([]);

        $this->assertSame(0, $statusCode);
    }

    protected function clearBaseDirectory()
    {
        $this->filesystem->remove($this->baseDirectory);
        $this->filesystem->mkdir([
            $this->baseDirectory.'/sites-available',
            $this->baseDirectory.'/sites-enabled'
        ]);
    }

    protected function newApp()
    {
        $app = new Application('NginxCli');

        $app->add(new NewCommand);
        $app->add(new ListCommand);
        $app->add(new EnableCommand);
        $app->add(new DisableCommand);

        return $app;
    }
}
