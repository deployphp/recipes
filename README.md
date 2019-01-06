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

| Recipe     | Docs
| ------     | ----
| bugsnag    | [read](docs/bugsnag.md)
| cachetool  | [read](docs/cachetool.md)
| cimonitor  | [read](docs/cimonitor.md)
| cloudflare | [read](docs/cloudflare.md)
| discord    | [read](docs/discord.md)
| hipchat    | [read](docs/hipchat.md)
| newrelic   | [read](docs/newrelic.md)
| npm        | [read](docs/npm.md)
| phinx      | [read](docs/phinx.md)
| rabbit     | [read](docs/rabbit.md)
| rollbar    | [read](docs/rollbar.md)
| rsync      | [read](docs/rsync.md)
| sentry     | [read](docs/sentry.md)
| slack      | [read](docs/slack.md)
| telegram   | [read](docs/telegram.md)
| yammer     | [read](docs/yammer.md)
| yarn       | [read](docs/yarn.md)
| raygun     | [read](docs/raygun.md)


## Contributing

Read the [contributing](https://github.com/deployphp/recipes/blob/master/CONTRIBUTING.md) guide, join the [discussions](https://deployer.org/discuss), take a look on open [issues](https://github.com/deployphp/recipes/issues)

## License

Licensed under the [MIT license](https://github.com/deployphp/recipes/blob/master/LICENSE).
