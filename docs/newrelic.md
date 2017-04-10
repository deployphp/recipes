# New Relic recipe

### Installing

```php
// deploy.php

require 'recipe/newrelic.php';
```

### Configuration options

- **newrelic** *(required)*: accepts an *array* with the api key for you new relic application and its application id.

```php
// deploy.php

set('newrelic', [
    'license'        => 'xad3...',
    'application_id' => '12873',
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
