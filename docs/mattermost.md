# Mattermost Recipe

## Installing

Create a Mattermost incoming webhook, through the administration panel.

Require the new recipe into your `deploy.php`

```php
require 'recipe/mattermost.php';
```

Add hook on deploy:

```
before('deploy', 'mattermost:notify');
```

## Configuration

 - `mattermost_webhook` - incoming mattermost webook **required**
   ```
   set('mattermost_webook', 'https://{your-mattermost-site}/hooks/xxx-generatedkey-xxx');
   ```
   
 - `mattermost_channel` - overrides the channel the message posts in 
   ```
   set('mattermost_channel', 'town-square');
   ```
 
 - `mattermost_username` - overrides the username the message posts as 
   ```
   set('mattermost_username', 'deployer');
   ```
   
 - `mattermost_icon_url` - overrides the profile picture the message posts with 
   ```
   set('mattermost_icon_url', 'https://domain.com/your-icon.png');
   ```

 - `mattermost_text` - notification message
   ```
   set('mattermost_text', '_{{user}}_ deploying `{{branch}}` to **{{target}}**');
   ```

 - `mattermost_success_text` – success template, default:
   ```
   set('mattermost_success_text', 'Deploy to **{{target}}** successful {{mattermost_success_emoji}}');
   ```
   
 - `mattermost_failure_text` – failure template, default:
   ```
   set('mattermost_failure_text', 'Deploy to **{{target}}** failed {{mattermost_failure_emoji}}');
   ```

 - `mattermost_success_emoji` – emoji added at the end of success text
 - `mattermost_failure_emoji` – emoji added at the end of failure text
 
 For detailed information about Mattermost hooks see: https://developers.mattermost.com/integrate/incoming-webhooks/ 

## Tasks

- `mattermost:notify` – send message to mattermost
- `mattermost:notify:success` – send success message to mattermost
- `mattermost:notify:failure` – send failure message to mattermost

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'mattermost:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'mattermost:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'mattermost:notify:failure');
```
