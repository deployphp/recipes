# Phinx recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/phinx.php';
```

## Configuration options

All options are in the config parameter `phinx` specified as an array (instead of the `phinx_path` variable).
All parameters are *optional*, but you can specify them with a dictionary (to change all parameters)
or by deployer dot notation (to change one option).

### Phinx params

- `phinx.environment`
- `phinx.date`
- `phinx.configuration` N.B. current directory is the project directory
- `phinx.target`
- `phinx.seed`
- `phinx.parser`
- `phinx.remove-all` (pass empty string as value)

### Phinx path params

- `phinx_path` Specify phinx path (by default phinx is searched for in $PATH, ./vendor/bin and ~/.composer/vendor/bin)

### Example of usage

```php
$phinx_env_vars = [
  'environment' => 'development',
  'configuration' => './migration/.phinx.yml'
  'target' => '20120103083322',
  'remove-all' => ''
];

set('phinx_path', '/usr/local/phinx/bin/phinx');
set('phinx', $phinx_env_vars);

after('cleanup', 'phinx:migrate');

// or set it for a specific server
host('dev')
    ->user('user')
    ->set('deploy_path', '/var/www')
    ->set('phinx', $phinx_env_vars)
    ->set('phinx_path', '');
```

## Tasks

- `phinx:migrate` Migrate your database
- `phinx:rollback` Rollback your database
- `phinx:seed` Run seeds for your database
- `phinx:breakpoint` Set a breakpoint for your database (note that breakpoints are toggled on/off automatically by Phinx, so you will need to call this command once with the `remove-all` option, then delete the `remove-all` option from the recipe configuration and re-run `phinx:breakpoint` to set the breakpoint to the current migration)

## Suggested Usage

You can run all tasks before or after any 
tasks (but you need to specify external configs for phinx).
If you use internal configs (which are in your project) you need 
to run it after the `deploy:update_code` task is completed.

## Read more

For further reading see [phinx.org](https://phinx.org). Complete descriptions of all possible options can be found on the [commands page](http://docs.phinx.org/en/latest/commands.html).
