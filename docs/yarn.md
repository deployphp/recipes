# Yarn recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/yarn.php';
```

## Configuration

- **bin/yarn** *(optional)*: set Yarn binary, automatically detected otherwise.

## Tasks

- `yarn:install` Install Yarn packages

## Usage

```php
after('deploy:update_code', 'yarn:install');
```
