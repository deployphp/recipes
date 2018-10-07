# Sentry recipe

### Installing

```php
// deploy.php

require 'recipe/sentry.php';
```

### Configuration options

- **organization** *(required)*: the slug of the organization the release belongs to.
- **projects** *(required)*: array of slugs of the projects to create a release for.
- **token** *(required)*: authentication token. Can be created at [https://sentry.io/settings/account/api/auth-tokens/]
- **version** *(required)* – a version identifier for this release. 
Can be a version number, a commit hash etc. (Defaults is set to git log -n 1 --format="%h".)
- **ref** *(optional)* – an optional commit reference. This is useful if a tagged version has been provided.
- **refs** *(optional)* - array to indicate the start and end commits for each repository included in a release. 
Head commits must include parameters *repository* and *commit) (the HEAD sha). 
They can optionally include *previousCommit* (the sha of the HEAD of the previous release), 
which should be specified if this is the first time you’ve sent commit data.
- **commits** *(optional)* - array commits data to be associated with the release. 
Commits must include parameters *id* (the sha of the commit), and can optionally include *repository*, 
*message*, *author_name*, *author_email* and *timestamp*.
- **url** *(optional)* – a URL that points to the release. This can be the path to an online interface to the sourcecode for instance.
- **date_released** *(optional)* – an optional date that indicates when the release went live. If not provided the current time is assumed.
- **sentry_server** *(optional)* – an optional sentry server (if you host it yourself). default to hosted sentry service.

```php
// deploy.php

set('sentry', [
    'organization' => 'exampleorg', 
    'projects' => [
        'exampleproj'
    ], 
    'token' => 'd47828...', 
    'version' => '0.0.1'
]);
```

### Tasks

- `deploy:sentry` send message to Sentry

### Suggested Usage

Since you should only notify Sentry of a successful deployment, the deploy:sentry task should be executed right at the end.

```php
// deploy.php

after('deploy', 'deploy:sentry');
```
