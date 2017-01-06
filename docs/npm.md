# NPM recipe

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/npm.php';
```

### Configuration options

- **bin/npm** *(optional)*: set NPM binary, automatically detected otherwise.
- **local/bin/npm** *(optional)*: set local NPM binary, automatically detected otherwise. 

By default, if no env setting is provided, this recipe will fallback to the global setting.

### Tasks

- `npm:install` Install NPM packages
- `npm:local:install` Install NPM packages into a locally prepared release. This should be used with the [local recipe](docs/local.md).

### Suggested Usage


```php
after('deploy:update_code', 'npm:install');
// or
before('deploy:symlink', 'npm:install');
// or local
after('local:update_code', 'npm:local:install');
```
