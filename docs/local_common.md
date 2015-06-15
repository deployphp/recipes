# Local_common recipe

This recipe is a re-implementation of `common.php` recipe, for running some of the tasks locally instead of remotelly. This recipe is especially useful if hosting provider that You're deploying to has some limitations of what can/can't be run. Best example is a *Shared Hosting* environment.

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/local_common.php';
```

### Configuration options

The `local_common.php` recipe doesn't expose any configuration options. Instead uses same options as standard `common.php` recipe.

### Environmental variables

- **local_git_cache** - Accepts *boolean*. Set to true if You wish to speed up cloning and You use full local-release workflow. This uses git with `--reference` and `--dissociate` set in order to speed up cloning.
- **local_deploy_path** - Accepts *string* specifying from where should deploys be done. This is functionally identical with `deploy_path` of `common.php` recipe.
- **local_release_path** - Accepts *string* specifying from where should deploys be done. This is functionally identical with `release_path` of `common.php` recipe. If You use full local release scenario, You do **not** wish to set this, as it's relative to `local_deploy_path`.

```php
env('local_git_cache', true);
env('local_deploy_path', '/tmp/deployer');
```

### Tasks

- `deploy:local:prepare` - Prepares local dirs for deployment. Instead of failing when `local_deploy_path` does not exist - tries to create it.
- `deploy:local:release` - Prepare release directory
- `deploy:local:update_code` - Clones repository into local release directory
- `deploy:local:vendors` - Run composer locally in release directory
- `deploy:local:symlink` - Symlink atomicly to newest release
- `current:local` - Show current symlinked release
- `cleanup:local` - Cleanup local releases.


### Suggested Usage

Depending on what *actually* is possible on remote deploy target or if You want to test deployment locally, there's many ways to utilize this recipe.

#### Scenario #1 

You just want to clone locally and run composer locally. Everything else can be done remotelly.

This can be set as follows:

```php
// deploy.php 

env('local_release_path', '/tmp/my_application');

/**
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:local:update_code',
    'deploy:local:vendors',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');

after('deploy:local:vendors', function() {
  upload(env('local_release_path'), env('release_path'));
})->desc('Upload local to remote');
```

#### Scenario #2 

You use full local release *and* full remote release. Plus You wish to take advantage of `rsync.php` recipe.

```php
env('rsync_src', function(){
  $local_src = env('local_release_path');
  if(is_callable($local_src)){
    $local_src = $local_src();
  }
  return $local_src;
});

task('deploy', [
    'deploy:local:prepare',
    'deploy:prepare',
    'deploy:local:release',
    'deploy:release',
    'deploy:rsync_warmup',
    'deploy:local:update_code',
    'rsync',
    'deploy:local:symlink',
    'deploy:symlink',
    'cleanup',
    'cleanup:local'
])->desc('Deploy your project');

```