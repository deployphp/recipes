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

### Environimental Variables

- **rsync_src**: per-environment rsync source. This can be server, stage or whatever-dependent. By default it's set to current directory
- **rsync_src**: per-environment rsync deestination. This can be server, stage or whatever-dependent. by default it's equivalent to release deploy destination.


```php
// deploy.php

set('rsync',[
  'exclude'=> [
    '.git',
    '*_deployer',
    'releases',
    'deploy.php',
    ],
  'exclude-file' => false,
  'include'=> [],
  'include-file' => false,
  'filter'=> [],
  'filter-file' => false,
  'filter-perdir' => false,
  'flags' => 'rz',
  'options' => ['delete'],
]);

env('rsync_src', __DIR__);
env('rsync_dest','{{release_path}}');
```

### Tasks

- `rsync` perorms rsync from local `rsync_src` dir to remote `rsync_dest` dir
- `deploy:rsync_warmup` performs a warmup rsync on remote. Useful only when using `rsync` task instead of `deploy:update_code`

### Suggested Usage

#### `rsync` task

Set `rsync_src` to locally cloned repository and rsync to `rsync_dest`. Then set this task instead of `deploy:update_code` in Your `deploy` task if Your hosting provider does not allow git.

#### `deploy:rsync_warmup` task

If Your deploy task looks like:

```php
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'rsync',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');
```

And Your `rsync_dest` is set to `{{release_path}}` then You could add this task to run before `rsync` task or after `deploy:release`, whatever's more convinient.