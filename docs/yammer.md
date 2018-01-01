# Yammer recipe

## Installing

Require yammer recipe in your `deploy.php` file:

```php
require 'recipe/yammer.php';
```

Add hook on deploy:
 
```php
before('deploy', 'yammer:notify');
```

## Configuration

- `yammer_url` – The URL to the message endpoint, default is https://www.yammer.com/api/v1/messages.json
- `yammer_token` *(required)* – Yammer auth token
- `yammer_group_id` *(required)* - Group ID 
- `yammer_title` – the title of application, default `{{application}}`
- `yammer_body` – notification message template, default:
  ```
  <em>{{user}}</em> deploying {{branch}} to <strong>{{target}}</strong>
  ```
- `yammer_success_body` – success template, default:
  ```
  Deploy to <strong>{{target}}</strong> successful
  ```
- `yammer_failure_body` – failure template, default:
  ```
  Deploy to <strong>{{target}}</strong> failed
  ```

## Tasks

- `yammer:notify` – send message to Yammer
- `yammer:notify:success` – send success message to Yammer
- `yammer:notify:failure` – send failure message to Yammer

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'yammer:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'yammer:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'yammer:notify:failure');
```
