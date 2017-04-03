# Yarn recipe

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/yarn.php';
```

### Configuration options

- **bin/yarn** *(optional)*: set Yarn binary, automatically detected otherwise.
- **local/bin/yarn** *(optional)*: set local Yarn binary, automatically detected otherwise. 

By default, if no env setting is provided, this recipe will fallback to the global setting.

### Tasks

- `yarn:install` Install Yarn packages
- `yarn:local:install` Install Yarn packages into a locally prepared release. This should be used with the [local recipe](docs/local.md).

### Suggested Usage


```php
after('deploy:update_code', 'yarn:install');
// or
before('deploy:symlink', 'yarn:install');
// or local
after('local:update_code', 'yarn:local:install');
```
