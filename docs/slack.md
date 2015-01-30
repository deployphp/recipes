# Slack recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/slack.php';
```

### Configuration options

- **slack** *(required)*: accepts an *array* with the api token and team name. Token can be generated on [slack api website](https://api.slack.com/web]).

You can provide also other configuration options:

 - *message* - default is **Deployment to '{$host}' on *{$prod}* was successful\n({$releasePath})**
 - *channel* - default is **#general**
 - *icon* - default is **:sunny:**
 - *username* - default is **Deploy**


```php
// deploy.php

set('slack', [
    'license' => 'xoxp-...',
    'team' => 'team name',
]);
```

### Tasks

- `deploy:slack` send message to slack

### Suggested Usage

Since you should only notify Slack channel of a successfull deployment, the `deploy:slack` task should be executed right at the end.

```php
// deploy.php

before('deploy:end', 'deploy:slack');
```