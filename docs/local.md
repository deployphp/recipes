# Local recipe

This recipe is a re-implementation of `common.php` recipe, for running some of the tasks locally instead of remotely. This recipe is especially useful if the hosting provider you're deploying to has some limitations of what can/can't be run. Best example is a *shared hosting* environment.

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/local.php';
```

### Configuration options

The `local.php` recipe doesn't expose any configuration options. Instead uses same options as standard `common.php` recipe.

### Environmental variables

- **local_deploy_path** - Accepts *string* specifying from where should deploys be done. This is functionally identical with `deploy_path` of `common.php` recipe.

```php
set('local_deploy_path', '/tmp/deployer');
```
#### Additional/relative env variables

- **local_release_path** - Accepts *string* specifying from where should deploys be done. This is functionally identical with `release_path` of `common.php` recipe. If You use full local release scenario, You do **not** wish to set this, as it's relative to `local_deploy_path`.
- **local_git_cache** - Accepts *boolean*. By default it checks if Your git version supports required options (minimum git version is 2.3). If You do not use release workflow, You would not see benefits to this option. Set this to false to use shallow clones, that slims down cloned repository size.

#### Locally bin paths

> Autodetection has issues with non-UNIX OS, thus you are highly recommended to use your own paths instead.

- **local_bin/php** - Accepts *string*. By default it checks for your local PHP bin path, which only works on UNIX OS.
- **local_bin/git** - Accepts *string*. By default it checks for your local Git bin path, which only works on UNIX OS.
- **local_bin/composer** -Accepts *string*. By default it checks for your local Composer bin path, which only works on UNIX OS.

### Tasks

- `local:prepare` - Prepares local dirs for deployment. Instead of failing when `local_deploy_path` does not exist - tries to create it.
- `local:release` - Prepare release directory
- `local:update_code` - Clones repository into local release directory
- `local:vendors` - Run composer locally in release directory
- `local:symlink` - Symlink atomicly to newest release
- `local:current` - Show current symlinked release
- `local:cleanup` - Cleanup local releases.


### Suggested Usage

Depending on what *actually* is possible on remote deploy target or if You want to test deployment locally, there's many ways to utilize this recipe.

#### Scenario #1 

You just want to clone locally and run composer locally. Everything else can be done remotelly.

This can be set as follows:

```php
// deploy.php 

set('local_release_path', '/tmp/my_application');

/**
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'local:update_code',
    'local:vendors',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');

after('local:vendors', function() {
    upload(get('local_release_path'), get('release_path'));
})->desc('Upload local to remote');
```

#### Scenario #2 

You use full local release *and* full remote release. Plus You wish to take advantage of `rsync.php` recipe.

```php
set('rsync_src', function() {
    $local_src = get('local_release_path');
    if(is_callable($local_src)){
        $local_src = $local_src();
    }
    return $local_src;
});

task('deploy', [
    'local:prepare',
    'deploy:prepare',
    'local:release',
    'deploy:release',
    'rsync:warmup',
    'local:update_code',
    'rsync',
    'local:symlink',
    'deploy:symlink',
    'cleanup',
    'local:cleanup'
])->desc('Deploy your project');

```
