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
> `shared/server/apache.conf.tpl` -> `shared/server/apache.conf`   

### Tasks

- `deploy:configure` compile configure files and upload to servers

### Suggested Usage

Since you should only once time create configure files before deployment, 
the `deploy:configure` task should be executed right only once time at the first. 
If environment variable is changed, you can use this task to re-create configure files.
