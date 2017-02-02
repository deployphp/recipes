# SED recipe

This recipe is a very simple implementation of sed to search and replace variables in your deployed files.

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
    'searches' => ['foo_file1','foo_file2'],
    'replacements' => ['bar_file1','bar_file2']
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
