# SED recipe

This recipe is a very simple implementation of sed to search and replace variables in your configuration files. The idea was to replace critical configurations with placeholder and reaplce them during the deployment with Deployer.

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/sed.php';
```

### Configuration options

```php
// deploy.php

set('sed', [
    'paths' => ['/path/to/file1','/path/to/file2'],
    'searches' => ['foo1','foo2'],
    'replacements' => ['bar1','bar2']
]);
```

### Tasks

- `sed:replace` Search and replace with sed

### Suggested Usage

Integrate the sed recipe after your update_code task or at the end of the deployment.

```php
// deploy.php

after('deploy', 'sed:replace');
```
