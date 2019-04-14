# DirectAdmin recipe

### Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/directadmin.php';
```

### Configuration
- `directadmin` – array with configuration for DirectAdmin
    - `host` – DirectAdmin host
    - `port` – DirectAdmin port (default: 2222, not required)
    - `scheme` – DirectAdmin scheme (default: http, not required)
    - `username` – DirectAdmin username
    - `password` – DirectAdmin password (it is recommended to use login keys!)
    - `db_user` – Database username (required when using directadmin:createdb or directadmin:deletedb)
    - `db_name` – Database namse (required when using directadmin:createdb)
    - `db_password` – Database password (required when using directadmin:createdb)
    - `domain_name` – Domain to create, delete or edit (required when using directadmin:createdomain, directadmin:deletedomain, directadmin:symlink-private-html or directadmin:php-version)
    - `domain_ssl` – Enable SSL, options: ON/OFF, default: ON (optional when using directadmin:createdb)
    - `domain_cgi` – Enable CGI, options: ON/OFF, default: ON (optional when using directadmin:createdb)
    - `domain_php` – Enable PHP, options: ON/OFF, default: ON (optional when using directadmin:createdb)
    - `domain_php_version` – Domain PHP Version, default: 1 (required when using directadmin:php-version)


### Tasks
- `directadmin:createdb` Create a database on DirectAdmin
- `directadmin:deletedb` Delete a database on DirectAdmin
- `directadmin:createdomain` Create a domain on DirectAdmin
- `directadmin:deletedomain` Delete a domain on DirectAdmin
- `directadmin:symlink-private-html` Symlink your private_html to public_html
- `directadmin:php-version` Change the PHP version from a domain

### Usage

A complete example with configs, staging and deployment

```
<?php
namespace Deployer;

require 'recipe/directadmin.php';

// Project name
set('application', 'myproject.com');
// Project repository
set('repository', 'git@github.com:myorg/myproject.com');

// DirectAdmin config
set('directadmin', [
    'host' => 'example.com',
    'scheme' => 'https', // Optional
    'port' => 2222, // Optional
    'username' => 'admin',
    'password' => 'Test1234' // It is recommended to use login keys!
]);

add('directadmin', [
    'db_name' => 'website',
    'db_user' => 'website',
    'db_password' => 'Test1234',

    'domain_name' => 'test.example.com'
]);


host('example.com')
    ->stage('review')
    ->user('admin')
    ->set('deploy_path', '~/domains/test.example.com/repository')


// Tasks
desc('Create directadmin domain and database');
task('directadmin:prepare', [
    'directadmin:createdomain',
    'directadmin:symlink-private-html',
    'directadmin:createdb',
])->onStage('review');

task('deploy', [
    'deploy:info',
    'directadmin:prepare',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
])->desc('Deploy your project');
```
