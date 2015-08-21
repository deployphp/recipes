# New Relic recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/newrelic.php';
```

### Configuration options

- **newrelic** *(required)*: accepts an *array* with the license key for you new relic application and its application_id or app_name.

```php
// deploy.php

set('newrelic', [
    'license'        => 'xad3...',
    'application_id' => '12873',
    // or
    'app_name' => 'your_app_name'
]);
```

### Tasks

- `deploy:newrelic` Notifies New Relic of a new deployment

### Suggested Usage

Since you should only notify New Relic of a successfull deployment, the `deploy:newrelic` task should be executed right at the end.

```php
// deploy.php

before('deploy:end', 'deploy:newrelic');
```
