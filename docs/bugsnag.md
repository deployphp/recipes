# Bugsnag recipe

### Installing

```php
// deploy.php

require 'recipe/bugsnag.php';
```

### Configuration options

- **bugsnag** *(required)*: accepts an *array* with the api_key. Key is given by the [Bugsnag website](https://bugsnag.com/).

You can provide also other configuration options:

- *api_key* - The API Key associated with the project. Informs Bugsnag which project has been deployed. This is the only required field.
- *release_stage* - The release stage (eg, production, staging) currently being deployed. (Optional, defaults to **{{stage}}**.)
- *repository* - The URL of the repository containing the source code being deployed. (Optional, defaults to **{{repository}}**.)
- *provider* - The name of your source control provider. Required when repository is supplied and only for on-premise services.
- *branch* - The source control branch from which you are deploying the code. (Optional, defaults to **{{branch}}**.)
- *revision* - The source control revision id for the code you are deploying. (Optional defaults to `git log -n 1 --format="%h"`.)
- *app_version* - The app version of the code you are currently deploying. Only set this if you tag your releases with semantic version numbers and deploy infrequently. (Optional.)

```php
// deploy.php

set('bugsnag', [
    'api_key' => "daf9a694....",
]);
```

### Tasks

- `deploy:bugsnag` send message to Bugsnag

### Suggested Usage

Since you should only notify Bugsnag of a successfull deployment, the `deploy:bugsnag` task should be executed right at the end.

```php
// deploy.php

after('deploy', 'deploy:bugsnag');
```
