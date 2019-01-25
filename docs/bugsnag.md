# Bugsnag recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/bugsnag.php';
```

## Configuration

- *bugsnag_api_key* – the API Key associated with the project. Informs Bugsnag which project has been deployed. This is the only required field.
- *bugsnag_provider* – the name of your source control provider. Required when repository is supplied and only for on-premise services.
- *bugsnag_app_version* – the app version of the code you are currently deploying. Only set this if you tag your releases with semantic version numbers and deploy infrequently. (Optional.)

## Tasks

- `bugsnag:notify` – send message to Bugsnag

## Usage

Since you should only notify Bugsnag of a successfull deployment, the `deploy:bugsnag` task should be executed right at the end.

```php
after('deploy', 'bugsnag:notify');
```
