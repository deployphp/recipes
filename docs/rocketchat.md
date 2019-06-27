# RocketChat Recipe

## Installing

Create a RocketChat incoming webhook, through the administration panel.

Require the new recipe into your `deploy.php`

```php
require 'recipe/rocketchat.php';
```

Add hook on deploy:

```
before('deploy', 'rocketchat:notify');
```

## Configuration

 - `rocketchat_webhook` - incoming rocketchat webook **required**
   ```
   set('rocketchat_webook', 'https://rocketchat.yourcompany.com/hooks/XXXXX');
   ```

 - `rocketchat_title` - the title of the application, defaults to `{{application}}`
 - `rocketchat_text` - notification message
   ```
   set('rocketchat_text', '_{{user}}_ deploying {{branch}} to {{target}}');
   ```

 - `rocketchat_success_text` – success template, default:
  ```
  set('rocketchat_success_text', 'Deploy to *{{target}}* successful');
  ```
 - `rocketchat_failure_text` – failure template, default:
  ```
  set('rocketchat_failure_text', 'Deploy to *{{target}}* failed');
  ```

 - `rocketchat_color` – color's attachment
 - `rocketchat_success_color` – success color's attachment
 - `rocketchat_failure_color` – failure color's attachment

## Tasks

- `rocketchat:notify` – send message to rocketchat
- `rocketchat:notify:success` – send success message to rocketchat
- `rocketchat:notify:failure` – send failure message to rocketchat

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'rocketchat:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'rocketchat:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'rocketchat:notify:failure');
```
