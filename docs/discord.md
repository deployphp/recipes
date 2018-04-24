# Discord recipe

## Installing

Require discord recipe in your `deploy.php` file:

```php
require 'recipe/discord.php';
```

Add hook on deploy:
 
```php
before('deploy', 'discord:notify');
```

## Configuration

- `discord_channel` – Discord channel ID, **required** 
- `discord_token` – Discord channel token, **required**

- `discord_notify_text` – notification message template, markdown supported, default:
  ```markdown
  :information_source: **{{user}}** is deploying branch `{{branch}}` to _{{target}}_
  ```
- `discord_success_text` – success template, default:
  ```markdown
  :white_check_mark: Branch `{{branch}}` deployed to _{{target}}_ successfully
  ```
- `discord_failure_text` – failure template, default:
  ```markdown
  :no_entry_sign: Branch `{{branch}}` has failed to deploy to _{{target}}_

## Tasks

- `discord:notify` – Notify Discord
- `discord:notify:success` – Notify Discord about deploy finish
- `discord:notify:failure` – Notify Discord about deploy failure
- `discord:test` – Just notify your Discord channel with all messages, without deploying

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'discord:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'discord:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'discord:notify:failure');
```
