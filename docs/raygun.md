# Raygun recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/raygun.php';
```

## Configuration

- `raygun_api_key` – the API key of your Raygun application
- `raygun_version` – the version of your application that this deployment is releasing
- `raygun_owner_name` – the name of the person creating this deployment
- `raygun_email` – the email of the person creating this deployment
- `raygun_comment` – the deployment notes
- `raygun_scm_identifier` – the commit that this deployment was built off
- `raygun_scm_type` - the source control system you use

## Tasks

- `raygun:notify` – send deployment details to Raygun

## Usage

To notify Raygun of a succesful deployment, you can use the 'raygun:notify' task after a deployment.

```php
after('deploy', 'raygun:notify');
```
