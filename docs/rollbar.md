# Rollbar recipe

### Installing

```php
// deploy.php

require 'recipe/rollbar.php';
```

### Configuration options

- **rollbar** *(required)*: accepts an *array* with the api access_token, environment and revision. Access token is given by the [rollbar website](https://rollbar.com/).

You can provide also other configuration options:

- *environment* - default is **the current stage**
- *revision* - default is **the last git hash** (`git log -n 1 --format="%h"`)
- *username* - default is **the current git user** (`git config user.name`)
- *comment* - default is **Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})**
- the available placeholders for the message parameter are:
  - {{release_path}}
  - {{host}}
  - {{stage}}
  - {{user}}
  - {{branch}}

```php
// deploy.php

set('rollbar', [
    'access_token' => "daf9a694....",
]);
```

### Tasks

- `deploy:rollbar` send message to rollbar

### Suggested Usage

Since you should only notify Rollbar channel of a successfull deployment, the `deploy:rollbar` task should be executed right at the end.

```php
// deploy.php

after('deploy', 'deploy:rollbar');
```
