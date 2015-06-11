# Rsync recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/rsync.php';
```

### Configuration options

- **rsync**: Accepts an array with following rsync options (all are optional and defaults are ok):

- *exclude*: accepts an *array* with patterns to be excluded from sending to server
- *exclude-file*: accepts a *string* containing absolute path to file, which contains exclude patterns
- *include*: accepts an *array* with patterns to be included in sending to server
- *include-file*: accepts a *string* containing absolute path to file, which contains include patterns
- *filter*: accepts an *array* of rsync filter rules
- *filter-file*: accepts a *string* containing merge-file filename.
- *filter-perdir*: accepts a *string* containing merge-file filename to be scanned and merger per each directory in rsync list offiles to send
- *flags*: accepts a *string* of flags to set when calling rsync command. Please **avoid** flags that accept params, and use *options* instead.
- *options*: accepts an *array* of options to set when calling rsync command. **DO NOT** prefix options with `--` as it's automaticly added.
- *local_release_dir*: accepts a *string* with dirname where temporary repository cloning should take place before being sent to server

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

