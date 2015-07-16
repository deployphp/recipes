# Configure recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/configure.php';
```

### Configuration options

Make `shared` directory and put configure template files to it.   
Template file extension is `.tpl` and it is removed on filename of compiled files.   
> Note: All configure files will be created with the same structure with template files.   
> Eg:   
> `shared/config/app.php.tpl` -> `shared/config/app.php`   
> `shared/server/vhost.conf.tpl` -> `shared/server/vhost.conf`   

### Tasks

- `deploy:configure` compile configure files and upload to servers

### Suggested Usage

Since you should only once time create configure files before deployment, 
the `deploy:configure` task should be executed right only once time at the first. 
If environment variable is changed, you can use this task to re-create configure files.

#### Example

Deploy scripts project:

```
|-- shared
|   |-- config
|   |   |-- app.php.tpl
|   |
|   |-- server
|       |-- vhost.conf.tpl
|
|-- deploy.php
|-- stage
|   |-- servers.yml
```
Content of file `app.php.tpl`:

```php
<?php
// define application environment: development or production
defined('APP_ENV') || define('APP_ENV', '{{app.mode}}');

return [
    'App' => [
        'mode'                => APP_ENV,
        'debug'               => {{app.debug}},
        'templates.path'      => realpath(__DIR__ . '/../src/templates'),
        // Cookies
        'cookies.encrypt'     => {{app.cookies.encrypt}},
        'cookies.lifetime'    => '{{app.cookies.lifetime}}',
        'cookies.path'        => '{{app.cookies.path}}',
        'cookies.domain'      => {{app.cookies.domain}},
        'cookies.secure'      => {{app.cookies.secure}},
        'cookies.httponly'    => {{app.cookies.httponly}},
        // Encryption
        'cookies.secret_key'  => '{{app.cookies.secret_key}}',
        'cookies.cipher'      => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
        // HTTP
        'http.version'        => '1.1',
    ],
    'DB'  => [
        'dsn'      => "mysql:host={{app.mysql.host}};post={{app.mysql.port}};dbname={{app.mysql.dbname}};charset=utf8",
        'username' => '{{app.mysql.username}}',
        'password' => '{{app.mysql.password}}',
        'options'  => {{app.mysql.options}}
    ]
];
```
Content of file `vhost.conf.tpl`:

```
# your site
<VirtualHost *:80>
    ServerName      {{app.domain}}
    DocumentRoot    {{deploy_path}}/current/public/
    ErrorLog        {{deploy_path}}/current/tmp/logs/apache-error.log
    CustomLog       {{deploy_path}}/current/tmp/logs/apache-access.log combined

    <Directory {{deploy_path}}/current/public/>
        AllowOverride All
        Options +FollowSymLinks
        <RequireAny>
            Require all granted
        </RequireAny>
    </Directory>
</VirtualHost>
```
Content of file `servers.yml`:
```yml
# list servers
# -------------
dev-svr:
    host: 192.168.1.2
    port: 22
    user: deployer
    identity_file: ~
    forward_agent: ~
    stage: dev
    deploy_path: /var/www/apps/yourproject
    branch: master
    app:
        domain: yourdomain.com
        mode: development
        debug: true
        cookies:
            encrypt: false
            lifetime: "2 hours"
            path: /
            domain: null
            secure: false
            httponly: false
            secret_key: 83cugZvQ67Cm39P2RN7x81G67i37RXlq
            
        mysql:
            host: 127.0.0.1
            port: 3306
            username: root
            password: ""
            dbname: "test"
            options: 
                1002: "SET NAMES 'UTF8'"
            
```

Content of file `deploy.php`:
```php
<?php
require 'recipe/common.php';
require 'vendor/deployphp/recipes/recipes/configure.php';

date_default_timezone_set('UTC');

set('repository', 'git@github.com:yourname/yourproject.git');
set('keep_releases', 5);
set('shared_dirs', [
    'tmp',
    'public/upload',
]);
set('shared_files', [
    'config/app.php',
]);
set('writable_dirs', [
    'tmp',
    'webroot/upload',
]);
set('writable_use_sudo', true);

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');

serverList(__DIR__ . '/stage/servers.yml');

```
After run task `deploy:configure`, two configure files are created on your server.
Content of file `/var/www/apps/yourproject/shared/config/app.php`:

```php
<?php
// define application environment: development or production
defined('APP_ENV') || define('APP_ENV', 'development');

return [
    'App' => [
        'mode'                => APP_ENV,
        'debug'               => true,
        'templates.path'      => realpath(__DIR__ . '/../src/templates'),
        // Cookies
        'cookies.encrypt'     => false,
        'cookies.lifetime'    => '2 hours',
        'cookies.path'        => '/',
        'cookies.domain'      => NULL,
        'cookies.secure'      => false,
        'cookies.httponly'    => false,
        // Encryption
        'cookies.secret_key'  => '83cugZvQ67Cm39P2RN7x81G67i37RXlq',
        'cookies.cipher'      => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
        // HTTP
        'http.version'        => '1.1',
    ],
    'DB'  => [
        'dsn'      => "mysql:host=127.0.0.1;post=3306;dbname=test;charset=utf8",
        'username' => 'root',
        'password' => '',
        'options'  => array (
            1002 => 'SET NAMES \'UTF8\'',
        )
    ]
];


```
And content of file `/var/www/apps/yourproject/shared/server/vhost.conf`:
```
# your site
<VirtualHost *:80>
    ServerName      yourdomain.com
    DocumentRoot    /var/www/apps/yourproject/current/public/
    ErrorLog        /var/www/apps/yourproject/current/tmp/logs/apache-error.log
    CustomLog       /var/www/apps/yourproject/current/tmp/logs/apache-access.log combined

    <Directory /var/www/apps/yourproject/current/public/>
        AllowOverride All
        Options +FollowSymLinks
        <RequireAny>
            Require all granted
        </RequireAny>
    </Directory>
</VirtualHost>
```
Now, you can make a symlink point to `vhost.conf` in `/etc/httpd/site-enabled` and run your app. :smile: