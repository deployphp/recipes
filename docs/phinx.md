# Phinx recipe

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/phinx.php';
```

### Configuration options

All options are in the environment variable `phinx` specified as a dictionary
(instead of the `phinx_path` variable).
All parameters are *optional*, but you can specify them with a dictionary(to change all parameters)
or by deployer dot notation(to change one option).

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

env('phinx_path', '/usr/local/phinx/bin/phinx');
env('phinx.environment', 'production');
env('phinx.configuration', './migration/.phinx.yml');

after('cleanup', 'phinx:migrate');

//Or set it for a specific server
server('dev', 'my-dev-server.local')
    ->user('user')
    ->env('deploy_path', '/var/www')
    ->env('phinx.environment', 'development')
    ->env('phinx_path', '');
```

### Tasks

- `phinx:migrate` Migrate your database
- `phinx:rollback` Rollback your database
- `phinx:seed` Run seeds for your database

### Suggested Usage

You can run all tasks after or before any 
tasks(but you need to specify external configs for phinx).
If you use internal configs(which are in your project) you need 
to run it after `deploy:update_code` task is completed.

### Read more

For further reading check the [phinx.org](https://phinx.org). Options description are on the [commands page](http://docs.phinx.org/en/latest/commands.html).
