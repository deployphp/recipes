# Microsoft Teams recipe

## Installing

Install recipes through composer:  
```
composer require deployer/recipes --dev
```

Require ms-teams recipe in your `deploy.php` file:  
**note:** currently there is a bug in the autoloader detection requiring users to specify the full path ([more info](https://github.com/deployphp/deployer/issues/1371)).

Setup:
1. Open MS Teams
2. Navigate to Teams section
3. Select existing or create new team
4. Select existing or create new channel
5. Hover over channel to get tree dots, click, in menu select "Connectors"
6. Search for and configure "Incoming Webhook"
7. Confirm/create and copy your Webhook URL
8. Setup deploy.php
    Add in header:
```php
require 'vendor/deployer/recipes/recipe/ms-teams.php';
set('teams_webhook', 'https://outlook.office.com/webhook/...');
```
Add in content:
```php
before('deploy', 'teams:notify');
after('success', 'teams:notify:success');
after('deploy:failed', 'teams:notify:failure');
```
9.) Sip your coffee

## Configuration

- `teams_webhook` – teams incoming webhook url, **required** 
  ```
  set('teams_webhook', 'https://outlook.office.com/webhook/...');
  ```
- `teams_title` – the title of application, default `{{application}}`
- `teams_text` – notification message template, markdown supported
  ```
  set('teams_text', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
  ```
- `teams_success_text` – success template, default:
  ```
  set('teams_success_text', 'Deploy to *{{target}}* successful');
  ```
- `teams_failure_text` – failure template, default:
  ```
  set('teams_failure_text', 'Deploy to *{{target}}* failed');
  ```

- `teams_color` – color's attachment
- `teams_success_color` – success color's attachment
- `teams_failure_color` – failure color's attachment

## Tasks

- `teams:notify` – send message to teams
- `teams:notify:success` – send success message to teams
- `teams:notify:failure` – send failure message to teams

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'teams:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'teams:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'teams:notify:failure');
```
