
# NginxCLI

🌴 Create and manage your **Nginx** configs from the command line.

## Installing the NginxCLI

```bash
composer global require la-moore/nginx-cli
```

Make sure to place Composer's system-wide vendor bin directory in your `$PATH` so the `nginx-cli` executable can be located by your system.
If you don't know where your `$PATH` is, run this command to find out:

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

Once installed, you should be able to run `nginx-cli {command}` from within any directory.

## Updating the NginxCLI

```bash
composer global update la-moore/nginx-cli
```

Run this command to update the CLI tool to the most recent published version. If there's been a major version release, you may need to run `require` instead of update.

## Using the NginxCLI

### Create new domain config

Create a new domain config with the `new` command:

```bash
nginx-cli new <example.com>
```

### Enable domain config

Enable a domain config with the `enable` command:

```bash
nginx-cli enable <example.com>
```

### Disable domain config

Disable a domain config with the `disable` command:

```bash
nginx-cli disable <example.com>
```

### Nginx reload

Reload Nginx with the `reload` command:

```bash
nginx-cli reload
```

### Nginx Start

Start Nginx with the `start` command:

```bash
nginx-cli start
```

### Nginx stop

Stop Nginx with the `stop` command:

```bash
nginx-cli stop
```

### Nginx test config

Stop Nginx with the `test` command:

```bash
nginx-cli test
```

## Run tests

Run tests with:

```bash
./vendor/bin/phpunit tests/NewCommandIntegrationTest.php
```
