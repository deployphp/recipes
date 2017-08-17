# Deployer Recipes

This repository contains third party recipes to integrate with deployer.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/deployphp/deployer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Installing

~~~sh
composer require deployer/recipes --dev 
~~~

Include recipes in `deploy.php` file.

```php
require 'recipe/slack.php';
```

## Recipes

| Recipe     | Docs                       | Usage
| ------     | ----                       | -----
| bugsnag    | [read](docs/bugsnag.md)    | `require 'recipe/bugsnag.php';`
| cachetool  | [read](docs/cachetool.md)  | `require 'recipe/cachetool.php';`
| cloudflare | [read](docs/cloudflare.md) | `require 'recipe/cloudflare.php';`
| hipchat    | [read](docs/hipchat.md)    | `require 'recipe/hipchat.php';`
| newrelic   | [read](docs/newrelic.md)   | `require 'recipe/newrelic.php';`
| npm        | [read](docs/npm.md)        | `require 'recipe/npm.php';`
| phinx      | [read](docs/phinx.md)      | `require 'recipe/phinx.php'`
| rabbit     | [read](docs/rabbit.md)     | `require 'recipe/rabbit.php';`
| rollbar    | [read](docs/rollbar.md)    | `require 'recipe/rollbar.php';`
| rsync      | [read](docs/rsync.md)      | `require 'recipe/rsync.php';`
| sentry     | [read](docs/sentry.md)     | `require 'recipe/sentry.php';`
| slack      | [read](docs/slack.md)      | `require 'recipe/slack.php';`
| yarn       | [read](docs/yarn.md)       | `require 'recipe/yarn.php';`


## Contributing

Read the [contributing](https://github.com/deployphp/recipes/blob/master/CONTRIBUTING.md) guide, join the [discussions](https://deployer.org/discuss), take a look on open [issues](https://github.com/deployphp/recipes/issues)

## License

Licensed under the [MIT license](https://github.com/deployphp/recipes/blob/master/LICENSE).
