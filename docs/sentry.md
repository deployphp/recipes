# Sentry recipe

### Installing

```php
// deploy.php

require 'recipe/sentry.php';
```

### Configuration options

- **organization** *(required)*: the slug of the organization the release belongs to.
- **project** *(required)*: the slug of the project to create a release for.
- **token** *(required)*: authentication token. Can be created at https://sentry.io/api/
- **version** *(required)* – a version identifier for this release. Can be a version number, a commit hash etc. (Defaults is set to git log -n 1 --format="%h".)
- **ref** *(optional)* – an optional commit reference. This is useful if a tagged version has been provided.
- **url** *(optional)* – a URL that points to the release. This can be the path to an online interface to the sourcecode for instance.
- **date_started** *(optional)* – an optional date that indicates when the release process started.
- **date_released** *(optional)* – an optional date that indicates when the release went live. If not provided the current time is assumed.
- **sentry_server** *(optional)* – an optional sentry server (if you host it yourself). default to hosted sentry service.

```php
// deploy.php

set('sentry', [
    'organization' => 'example org', 
    'project' => 'example proj', 
    'token' => 'd47828...', 
    'version' => '0.0.1'
]);
```

### Tasks

- `deploy:sentry` send message to Sentry

### Suggested Usage

Since you should only notify Sentry of a successfull deployment, the deploy:sentry task should be executed right at the end.

```php
// deploy.php

after('deploy', 'deploy:sentry');
```
