# Slack recipe

### Installing

<a href="https://slack.com/oauth/authorize?&client_id=162408975313.167726381175&scope=incoming-webhook"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" /></a>

Add this to your `deploy.php` file:

```php
require 'recipe/slack.php';
```

### Environmental variables

- **slack_skip_notification** - Skips the entire task when set to `true`. This is particularly useful if you want to disable slack notifications for certain stages.

### Configuration options

- **slack** *(required)*: accepts an *array* with the api token and team name. Token can be generated on [slack api website](https://api.slack.com/docs/oauth-test-tokens).

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
