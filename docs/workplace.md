# Workplace Recipe

## Installing

This recipes works with Custom Integrations and Publishing Bots.

Require the new recipe into your `deploy.php`

```php
require 'recipe/workplace.php';
```

Add hook on deploy:

```
before('deploy', 'workplace:notify');
```

## Configuration

 - `workplace_webhook` - incoming workplace webhook **required**
   ```
   // With custom integration
   set('workplace_webhook', 'https://graph.facebook.com/<GROUP_ID>/feed?access_token=<ACCESS_TOKEN>');

   // With publishing bot
   set('workplace_webhook', 'https://graph.facebook.com/v3.0/group/feed?access_token=<ACCESS_TOKEN>');

   // Use markdown on message
   set('workplace_webhook', 'https://graph.facebook.com/<GROUP_ID>/feed?access_token=<ACCESS_TOKEN>&formatting=MARKDOWN');
   ```

 - `workplace_text` - notification message
   ```
   set('workplace_text', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
   ```

 - `workplace_success_text` – success template, default:
  ```
  set('workplace_success_text', 'Deploy to *{{target}}* successful');
  ```
 - `workplace_failure_text` – failure template, default:
  ```
  set('workplace_failure_text', 'Deploy to *{{target}}* failed');
  ```
 - `workplace_edit_post` – whether to create a new post for deploy result, or edit the first one created, default creates a new post:
  ```
  set('workplace_edit_post', false);
  ```


## Tasks

- `workplace:notify` – create post in workplace
- `workplace:notify:success` – create success post in workplace
- `workplace:notify:failure` – create failure post in workplace

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'workplace:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'workplace:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'workplace:notify:failure');
```
