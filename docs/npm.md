# Npm recipe

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/npm.php';
```

### Configuration options

- **bin/npm** *(optional)*: set npm binary, automatically try to detect it otherwise. 

By default, if no env setting is provided, this recipe will fallback to the global setting.

### Tasks

- `npm:install` Install npm packages

### Suggested Usage


```php
after('deploy:update_code', 'npm:install');
// or
before('deploy:symlink', 'npm:install');
```
