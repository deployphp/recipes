# Google Hangouts Chat recipe

## Installing

Follow [these instructions](https://developers.google.com/hangouts/chat/how-tos/webhooks#define_an_incoming_webhook) to add a new webhook to your channel, and make a note of your webhook URL. (you'll need to set `chat_webhook` in your `deploy.php`.

Require the Google Hangouts Chat recipe in your `deploy.php` file:

```php
require 'recipe/chat.php';
```

Add hook on deploy:
 
```php
before('deploy', 'chat:notify');
```

## Configuration

- `chat_webhook` – chat incoming webhook url, **required** 
- `chat_title` – the title of your notification card, default `{{application}}`
- `chat_subtitle` – the subtitle of your card, default `{{hostname}}`
- `chat_favicon` – an image for the header of your card, default `http://{{hostname}}/favicon.png`
- `chat_line1` – first line of the text in your card, default: `{{branch}}`
- `chat_line2` – second line of the text in your card, default: `{{stage}}`

## Tasks

- `chat:notify` – send message to chat
- `chat:notify:success` – send success message to chat
- `chat:notify:failure` – send failure message to chat

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'chat:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'chat:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'chat:notify:failure');
```
