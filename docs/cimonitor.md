# CIMonitor recipe

Monitor your deployments on [CIMonitor](https://github.com/CIMonitor/CIMonitor).

![CIMonitorGif](https://www.steefmin.xyz/deployer-example.gif)

## Installing

Install recipes through composer:  
```
composer require deployer/recipes --dev
```

Require cimonitor recipe in your `deploy.php` file:  

```php
require 'recipe/cimonitor.php';
```

Add tasks on deploy:
 
```php
before('deploy', 'cimonitor:notify');
after('success', 'cimonitor:notify:success');
after('deploy:failed', 'cimonitor:notify:failure');
```

## Configuration

- `cimonitor_webhook` – CIMonitor server webhook url, **required** 
  ```
  set('cimonitor_webhook', 'https://cimonitor.enrise.com/webhook/deployer');
  ```
- `cimonitor_title` – the title of application, default the username\reponame combination from `{{repository}}`
  ```
  set('cimonitor_title', '');
  ```
- `cimonitor_user` – User object with name and email, default gets information from `git config`
  ```
  set('cimonitor_user', function () {
    return [
      'name' => 'John Doe',
      'email' => 'john@enrise.com',
    ];
  });
  ```

Various cimonitor statusses are set, in case you want to change these yourselves. See the [CIMonitor documentation](https://cimonitor.readthedocs.io/en/latest/) for the usages of different states.

 
## Tasks

- `cimonitor:notify` – notify CIMonitor of starting deployment
- `cimonitor:notify:success` – send success message to CIMonitor
- `cimonitor:notify:failure` – send failure message to CIMonitor

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'cimonitor:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'cimonitor:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failed', 'cimonitor:notify:failure');
```
