# Slack recipe

### Installing

<a href="https://slack.com/oauth/authorize?&client_id=113734341365.225973502034&scope=incoming-webhook"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" /></a>

Require slack recipe in your `deploy.php` file:

```php
require 'recipe/slack.php';
```

Add hook on deploy:
 
```bash

```

### Configuration

- `slack_webhook` – slack incoming webhook url, **required** 
- `slack_title` – the title of application, default `{{application}}`
- `slack_target` – the name of current stage or hostname
- `slack_text` – notification message template, markdown supported
  ```
  _{{user}}_ deploying `{{branch}}` to *{{slack_target}}*
  ```
- `slack_success_text` – success template, default:
  ```
  Deploy to *{{slack_target}}* successful
  ```
- `slack_color` – color's attachment
- `slack_success_color` – success color's attachment

### Tasks

- `slack:notify` – send message to slack
- `slack:notify:success` – send success message to slack

### Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'slack:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'slack:notify:success');
```
