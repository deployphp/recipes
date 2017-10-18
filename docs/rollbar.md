# Rollbar recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/rollbar.php';
```

## Configuration

- `rollbar_token` – access token to rollbar api
- `rollbar_comment` – comment about deploy, default to 
  ```php
  set('rollbar_comment', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
  ```
- `rollbar_username` – rollbar user name  

### Tasks

- `rollbar:notify` – send message to rollbar

## Usage

Since you should only notify Rollbar channel of a successfull deployment, the `rollbar:notify` task should be executed right at the end.

```php
after('deploy', 'rollbar:notify');
```
