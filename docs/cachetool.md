# Cachetool recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/cachetool.php';
```

### Configuration options

- **cachetool** *(optional)*: accepts a *string* with the unix socket or ip address to php5-fpm. If `cachetool` is not given, then the application will look for a `cachetool.yml` file and read the configuration from there.

```php
// deploy.php

set('cachetool', '/var/run/php5-fpm.sock');
// or
set('cachetool', '127.0.0.1:9000');
```

### Tasks

- `cachetool:clear:apc` Clears APC *system* cache
- `cachetool:clear:opcache` Resets the contents of the opcode cache

### Suggested Usage

Since APC and OPcache deal with compiling and caching files, they should be executed right after the symlink is created for the new release:

```php
// deploy.php

after('deploy:symlink', 'cachetool:clear:opcache');
// or
after('deploy:symlink', 'cachetool:clear:apc');
```

### Read more

Read more information about cachetool on the website:
http://gordalina.github.io/cachetool/
