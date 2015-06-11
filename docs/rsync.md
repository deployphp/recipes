# Rsync recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/rsync.php';
```

### Configuration options

- **rsync**: Accepts an array with following rsync options:

- *excludes* *(optional)*: accepts a *array* with files/dirs to be excluded from sending to server
- *local_release_dir* *(optional)*: accepts a *string* with dirname where temporary repository cloning should take place before being sent to server

```php
// deploy.php

set('rsync',[
  'excludes'=> [
    '.git',
    'deployer_release',
    'releases',
    'deploy.php',
    ],
  'local_release_dir' => '/tmp'
]);
```

### Tasks

- `deploy:local_release` Creates local release directory
- `deploy:update_code` overrides standard `deploy:update_code` to update code locally instead of remotelly
- `deploy:rsync` perorms rsync from local release to remote

### Suggested Usage

This recipe performs all repository-related tasks locally, so the best way to use it, would be to use this instead of `common.php` recipe.

For using composer or other tools, You'd need to override `deploy:vendors` task and plug it in `deploy` chain.

