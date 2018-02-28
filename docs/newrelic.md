# New Relic recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/newrelic.php';
```

## Configuration

- `newrelic_app_id` – newrelic's app id
- `newrelic_api_key` – newrelic's api key
- `newrelic_description` – message to send


## Tasks

- `newrelic:notify` – notifies New Relic of a new deployment



## Usage

Since you should only notify New Relic of a successfull deployment, the `newrelic:notify` task should be executed right at the end.

```php
after('deploy', 'newrelic:notify');
```
