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
  this parameter is required. but if you setting to `rollbar_quietly` is `true`, it will be optional parameter. 
- `rollbar_username` – rollbar user name  
- `rollbar_quietly` - not post the comment to rollbar. if it is not necessary to comment, set `true`. default `false`. 

### Tasks

- `rollbar:notify` – send message to rollbar

## Usage

Since you should only notify Rollbar channel of a successfull deployment, the `rollbar:notify` task should be executed right at the end.

```php
after('deploy', 'rollbar:notify');
```
