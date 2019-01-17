# Telegram recipe

## Installing
  1. Create telegram bot with [BotFather](https://t.me/BotFather) and grab the token provided
  2. Send `/start` to your bot and open https://api.telegram.org/bot{$TELEGRAM_TOKEN_HERE}/getUpdates
  3. Take chat_id from response
Require telegram recipe in your `deploy.php` file:

```php
require 'recipe/telegram.php';
```

Add hook on deploy:
 
```php
before('deploy', 'telegram:notify');
```

## Configuration

- `telegram_token` – telegram bot token, **required** 
- `telegram_chat_id` — chat ID to push messages to
- `telegram_proxy` - proxy connection string in [CURLOPT_PROXY](https://curl.haxx.se/libcurl/c/CURLOPT_PROXY.html) form like:
  ```
  http://proxy:80
  socks5://user:password@host:3128
   ```
- `telegram_title` – the title of application, default `{{application}}`
- `telegram_text` – notification message template
  ```
  _{{user}}_ deploying `{{branch}}` to *{{target}}*
  ```
- `telegram_success_text` – success template, default:
  ```
  Deploy to *{{target}}* successful

  ```
- `telegram_failure_text` – failure template, default:
  ```
  Deploy to *{{target}}* failed
  ```

## Tasks

- `telegram:notify` – send message to telegram
- `telegram:notify:success` – send success message to telegram
- `telegram:notify:failure` – send failure message to telegram

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'telegram:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'telegram:notify:success');
```
If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'telegram:notify:failure');

