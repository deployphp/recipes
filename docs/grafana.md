# Grafana recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/grafana.php';
```

## Configuration options

- **url** *(required)*: the URL to the creates annotation api endpoint.
- **token** *(required)*: authentication token. Can be created at Grafana Console.
- **time** *(optional)* – set deploy time of annotation. specify epoch milliseconds. (Defaults is set to the current time in epoch milliseconds.)
- **tags** *(optional)* – set tag of annotation.
- **text** *(optional)* – set text of annotation. (Defaults is set to "Deployed " + git log -n 1 --format="%h")

```php
// deploy.php

set('grafana', [
    'token' => 'eyJrIj...',
    'url' => 'http://grafana/api/annotations',
    'tags' => ['deploy', 'production'],
]);

```

## Tasks

- `grafana:annotation` create annotation to Grafana


## Usage

If you want to create annotation about successful end of deployment.

```php
after('success', 'grafana:annotation');
```
