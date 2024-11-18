
# NginxCLI tool

🌴 Create and manage your **Nginx** configs from the command line.

## Installing the NginxCLI tool

```
composer global require la-moore/nginx-cli
```

Make sure to place Composer's system-wide vendor bin directory in your `$PATH` so the `nginx-cli` executable can be located by your system.
If you don't know where your `$PATH` is, run this command to find out:

```bash
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

Once installed, you should be able to run `nginx-cli {command}` from within any directory.

## Updating the NginxCLI tool

```
composer global update la-moore/nginx-cli
```

Run this command to update the CLI tool to the most recent published version. If there's been a major version release, you may need to run `require` instead of update.

## Using the NginxCLI tool

### Create new domain config

Create a new domain config with the `new` command:

```
nginx-cli new <example.com>
```

### Enable domain config

Enable a domain config with the `enable` command:

```
nginx-cli enable <example.com>
```

### Disable domain config

Disable a domain config with the `disable` command:

```
nginx-cli disable <example.com>
```

### Run tests

Run tests with:

```
./vendor/bin/phpunit tests/NewCommandIntegrationTest.php
```
