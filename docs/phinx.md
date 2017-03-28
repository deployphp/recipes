# Phinx recipe

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/phinx.php';
```

### Configuration options

All options are in the environment variable `phinx` specified as a dictionary
(instead of the `phinx_path` variable).
All parameters are *optional*, but you can specify them with a dictionary (to change all parameters)
or by deployer dot notation (to change one option).

#### Phinx environment variable

- `phinx.environment`
- `phinx.date`
- `phinx.configuration` N.B. current directory is the project directory
- `phinx.target`
- `phinx.seed`
- `phinx.parser`

#### Phinx path environment variable

- `phinx_path` Specify phinx path(by default phinx is searched in 
$PATH, ./vendor/bin and ~/.composer/vendor/bin)

#### Example of usage

```php
//deploy.php

set('phinx_path', '/usr/local/phinx/bin/phinx');
set('phinx.environment', 'production');
set('phinx.configuration', './migration/.phinx.yml');

after('cleanup', 'phinx:migrate');

//Or set it for a specific server
server('dev', 'my-dev-server.local')
    ->user('user')
    ->set('deploy_path', '/var/www')
    ->set('phinx.environment', 'development')
    ->set('phinx_path', '');
```

### Tasks

- `phinx:migrate` Migrate your database
- `phinx:rollback` Rollback your database
- `phinx:seed` Run seeds for your database
- `phinx:breakpoint` Set a breakpoint for your database (note that breakpoints are toggled automatically by Phinx, so you will need to call this command once with the `--remove-all` option, then again to set the breakpoint to the current migration)

### Suggested Usage

You can run all tasks before or after any 
tasks (but you need to specify external configs for phinx).
If you use internal configs (which are in your project) you need 
to run it after the `deploy:update_code` task is completed.

### Read more

For further reading see [phinx.org](https://phinx.org). Complete descriptions of all possible options can be found on the [commands page](http://docs.phinx.org/en/latest/commands.html).
