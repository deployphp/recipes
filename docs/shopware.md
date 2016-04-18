# Cachetool recipe

### Installing

You will need to require "deployer/deployer": "~3.2" in your composer.json

```php
// deploy.php

require_once __DIR__ . '/vendor/deployer/deployer/recipe/common.php';
require 'vendor/deployphp/recipes/recipes/shopware.php';
```

### Configuration options

- `writable_dirs` Dirs that will be created via mkdir -p and after it set as writable

### Tasks

- `shopware:install` Install a complete new shopware instance
- `shopware:deploy` Deploys a given shopware instance
- `shopware:deploy:test` Deploys a given shopware instance, without releasing it!

### Suggested Usage

Before using one of the tasks above, use deploy:prepare from the common recipe.
After this you want to create a .htaccess and a default.ini in the shared folder

Place your access data in your default.ini like this:

[database]

host = 127.0.0.1
user = 
name = 
password = 
port = 3306

[client]

user = 
password = 
database = 

Also your config.php.dist should look at least like this:

```php
<?php
$configuration = parse_ini_file(__DIR__ . '/default.ini', true);

return array(
    'db' => array(
        'username' => $configuration['database']['user'],
        'password' => $configuration['database']['password'],
        'dbname' => $configuration['database']['name'],
        'host' => $configuration['database']['host'],
        'port' => $configuration['database']['port']
    )
);
```

Also you will need a shopware fix currently, in order to symlink the community plugins over releases.
Necessary fix: https://github.com/shopware/shopware/pull/304
Someone may provide a better solution in the future.

The local default.ini is optional and just a suggestion.

```php
// deploy.php

require_once __DIR__ . '/vendor/deployer/deployer/recipe/common.php';
require 'vendor/deployphp/recipes/recipes/shopware.php';

// Set configurations
set('repository', '...');

$configuration = parse_ini_file(__DIR__ . '/default.ini', true);

task('shopware:clear:cache', function() {
    run("cd {{release_path}}/var/cache && ./clear_cache.sh");
});

before('deploy:symlink', 'shopware:clear:cache');

// Configure servers
server('development', '...')
    ->user($configuration['deployment']['user'])
    ->identityFile($configuration['deployment']['public'], $configuration['deployment']['private'], $configuration['deployment']['passphrase'])
    ->env('deploy_path', '...')
    ->env('branch', 'master');
```
