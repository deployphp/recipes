# Yarn recipe

### Installing

```php
// deploy.php

require 'recipe/yarn.php';
```

### Configuration options

- **bin/yarn** *(optional)*: set Yarn binary, automatically detected otherwise.

By default, if no env setting is provided, this recipe will fallback to the global setting.

### Tasks

- `yarn:install` Install Yarn packages

### Suggested Usage

```php
after('deploy:update_code', 'yarn:install');
```
