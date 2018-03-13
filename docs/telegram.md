# Telegram recipe

## Installing
  1. Create telegram bot by any manual in the internet
  2. Take telegrambot token from BotFather
  3. Send /start to your bot and open https://api.telegram.org/bot{$TELEGRAM_TOKEN_HERE}/getUpdates
  4. Take chat_id from response
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
- `telegram_title` – the title of application, default `{{application}}`
- `telegram_text` – notification message template
  ```
  _{{user}}_ deploying `{{branch}}` to *{{target}}*
  ```
- `telegram_success_text` – success template, default:
  ```
  Deploy to *{{target}}* successful
  ```

## Tasks

- `telegram:notify` – send message to telegram
- `telegram:notify:success` – send success message to telegram

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'telegram:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'telegram:notify:success');
```

