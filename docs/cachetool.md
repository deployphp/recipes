# Cachetool recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/cachetool.php';
```

## Configuration

- **cachetool** *(optional)*: accepts a *string* with the unix socket or ip address to php5-fpm. If `cachetool` is not given, then the application will look for a `cachetool.yml` file and read the configuration from there.

    ```php
    set('cachetool', '/var/run/php5-fpm.sock');
    // or
    set('cachetool', '127.0.0.1:9000');
    ```

You can also specify different cachetool settings for each host:
```php
host('staging')
    ->set('cachetool', '127.0.0.1:9000');
    
host('production')
    ->set('cachetool', '/var/run/php5-fpm.sock');
```

By default, if no `cachetool` parameter is provided, this recipe will fallback to the global setting.

## Tasks

- `cachetool:clear:apc` – clears APC *system* cache
- `cachetool:clear:apcu` – clears APCu cache
- `cachetool:clear:opcache` – resets the contents of the opcode cache

## Usage

Since APC/APCu and OPcache deal with compiling and caching files, they should be executed right after the symlink is created for the new release:

```php
after('deploy:symlink', 'cachetool:clear:opcache');
// or
after('deploy:symlink', 'cachetool:clear:apc');
// or
after('deploy:symlink', 'cachetool:clear:apcu');
```

## Read more

Read more information about cachetool on the website:
http://gordalina.github.io/cachetool/
