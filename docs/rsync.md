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
- *timeout*: accepts an *int* defining timeout for rsync command to run locally.

#### Sample Configuration:

Following is default configuration. By default rsync ignores only git dir and `deploy.php` file.

```php
// deploy.php

set('rsync',[
    'exclude'      => [
        '.git',
        'deploy.php',
    ],
    'exclude-file' => false,
    'include'      => [],
    'include-file' => false,
    'filter'       => [],
    'filter-file'  => false,
    'filter-perdir'=> false,
    'flags'        => 'rz', // Recursive, with compress
    'options'      => ['delete'],
    'timeout'      => 60,
]);
```

If You have multiple excludes, You can put them in file and reference that instead. If You use `deploy:rsync_warmup` You could set additional options that could speed-up and/or affect way things are working. For example:

```php
// deploy.php

set('rsync',[
    'exclude'       => ['excludes_file'],
    'exclude-file'  => /tmp/localdeploys/excludes_file, //Use absolute path to avoid possible rsync problems
    'include'       => [],
    'include-file'  => false,
    'filter'        => [],
    'filter-file'   => false,
    'filter-perdir' => false,
    'flags'         => 'rzcE', // Recursive, with compress, check based on checksum rather than time/size, preserve Executable flag
    'options'       => ['delete', 'delete-after', 'force'], //Delete after successful trasfer, delete even if deleted dir is not empty
    'timeout'       => 3600, //for those huge repos or crappy connection
]);
```


### Environimental Variables

- **rsync_src**: per-environment rsync source. This can be server, stage or whatever-dependent. By default it's set to current directory
- **rsync_dest**: per-environment rsync destination. This can be server, stage or whatever-dependent. by default it's equivalent to release deploy destination.

#### Sample configurations:

This is default configuration: 

```php
// deploy.php 


env('rsync_src', __DIR__);
env('rsync_dest','{{release_path}}');
```

If You use local deploy recipe You can set src to local release:

```php
// deploy.php

server('local_deploy','local_deploy.host',22)
        ->env('deploy_path','/var/www/vhosts/app')
        ->env('rsync_src', function(){
            $local_src = env('local_release_path');
            if(is_callable($local_src)) {
                $local_src = $local_src();
            }
            return $local_src;
        });
```

### Tasks

- `rsync` perorms rsync from local `rsync_src` dir to remote `rsync_dest` dir
- `rsync:warmup` performs a warmup rsync on remote. Useful only when using `rsync` task instead of `deploy:update_code`

### Suggested Usage

#### `rsync` task

Set `rsync_src` to locally cloned repository and rsync to `rsync_dest`. Then set this task instead of `deploy:update_code` in Your `deploy` task if Your hosting provider does not allow git.

#### `rsync:warmup` task

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
