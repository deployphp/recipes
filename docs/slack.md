# Slack recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/slack.php';
```

### Configuration options

- **slack** *(required)*: accepts an *array* with the api token and team name. Token can be generated on [slack api website](https://api.slack.com/web]).

You can provide also other configuration options:

 - *message* - default is **Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})**
  - the available placeholders for the message parameter are:
    - {{release_path}}
    - {{host}}
    - {{stage}}
    - {{user}}
    - {{branch}}
    - {{app_name}}
 - *channel* - default is **#general**
 - *icon* - default is **:sunny:**
 - *username* - default is **Deploy**


```php
// deploy.php

set('slack', [
    'token' => 'xoxp-...',
    'team'  => 'team name',
    'app'   => 'app name',
]);
```

### Tasks

- `deploy:slack` send message to slack

### Suggested Usage

Since you should only notify Slack channel of a successfull deployment, the `deploy:slack` task should be executed right at the end.

```php
// deploy.php

after('deploy', 'deploy:slack');
```