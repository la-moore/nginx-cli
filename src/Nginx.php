<?php

namespace Nginx\Cli;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use DirectoryIterator;

class Nginx
{
    protected Filesystem $filesystem;

    protected static string $configFile = '';

    static function testMode()
    {
        static::$configFile =  Path::join(getcwd(), '.nginx', 'nginx.conf');
    }

    public function __construct()
    {
        $this->filesystem = new Filesystem();

        if (!Nginx::$configFile) {
            Nginx::$configFile = $this->run(`(awk -F= -v RS=' ' '/conf-path/ {print $2}' <<< $(nginx -V 2>&1))`);

            throw new RuntimeException('Unable to find Nginx config file.');
        }
    }

    public function fromBaseDir(...$paths)
    {
        $configDir = dirname(Nginx::$configFile);
        $path = Path::join($configDir, ...$paths);

        return $path;
    }


    public function getSitesDisabled()
    {
        $path = $this->fromBaseDir('sites-available');
        $files = [];

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) continue;

            $fileName = $file->getFilename();

            if (!$this->isSiteEnabled($fileName)) {
                $files[] = $fileName;
            }
        }

        return $files;
    }

    public function getSitesAvailable()
    {
        $path = $this->fromBaseDir('sites-available');
        $files = [];

        foreach (new DirectoryIterator($path) as $file) {
            if($file->isDot()) continue;

            $files[] = $file->getFilename();
        }

        return $files;
    }

    public function getSitesEnabled()
    {
        $path = $this->fromBaseDir('sites-enabled');
        $files = [];

        foreach (new DirectoryIterator($path) as $file) {
            if($file->isDot()) continue;

            $files[] = $file->getFilename();
        }

        return $files;
    }

    public function isSiteEnabled(string $site)
    {
        $path = $this->fromBaseDir('sites-enabled', $site);

        return $this->filesystem->exists($path);
    }


    public function createSite(string $domain, string$config)
    {
        $path = $this->fromBaseDir('sites-available', $domain);

        $this->filesystem->dumpFile($path, $config);
    }

    public function enableSite(string $domain) {
        $source = $this->fromBaseDir('sites-available', $domain);
        $target = $this->fromBaseDir('sites-enabled', $domain);

        $this->filesystem->symlink($source, $target);
    }

    public function disableSite(string $domain) {
        $target = $this->fromBaseDir('sites-enabled', $domain);

        $this->filesystem->remove($target);
    }


    public function run(...$commandParts)
    {
        $process = Process::fromShellCommandline(join(' ', $commandParts));

        $process->setTimeout(null);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
