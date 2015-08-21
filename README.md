# Deployer Recipes

This repository contains third party recipes to integrate with deployer.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/deployphp/deployer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Using these recipes

First install this repository as a dev dependency.

**For Deployer 3.x**

```sh
$ php composer.phar require --dev "deployphp/recipes ~3.0"
```

**For Deployer 2.x**

```sh
$ php composer.phar require --dev "deployphp/recipes ~2.0"
```

Include the recipes to your will.

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/cachetool.php';
```

## Recipes

| Recipe    | Docs                      | Usage
| ------    | ----                      | -----
| cachetool | [read](docs/cachetool.md) | `require 'vendor/deployphp/recipes/recipes/cachetool.php';`
| local     | [read](docs/local.md)     | `require 'vendor/deployphp/recipes/recipes/local.php';`
| newrelic  | [read](docs/newrelic.md)  | `require 'vendor/deployphp/recipes/recipes/newrelic.php';`
| rabbit    | [read](docs/rabbit.md)    | `require 'vendor/deployphp/recipes/recipes/rabbit.php';`
| rsync     | [read](docs/rsync.md)     | `require 'vendor/deployphp/recipes/recipes/rsync.php';`
| slack     | [read](docs/slack.md)     | `require 'vendor/deployphp/recipes/recipes/slack.php';`
| configure | [read](docs/configure.md) | `require 'vendor/deployphp/recipes/recipes/configure.php';`
| hipchat   | [read](docs/hipchat.md)   | `require 'vendor/deployphp/recipes/recipes/hipchat.php';`

## Contributing a recipe

All code contributions must go through a pull request and approved by a core developer before being merged. This is to ensure proper review of all the code.

* [Fork and clone](https://help.github.com/articles/fork-a-repo).
* Create a branch.
  * If the recipe is for Deployer `3.x` then create your branch based on `master`
  * If the recipe is for Deployer `2.x` then create your branch based on the `2.x` branch
* Add your recipe to the `recipes` folder, it must be licensed as MIT.
* Add documentation in Markdown for your recipe to the `docs` folder, you can base your documentation from [cachetool.md](http://github.com/deployphp/recipes/blob/master/docs/cachetool.md) as it is fairly complete.
* Add your recipe to the table above in `README.md`, please use alphabetical order.
* Commit, push and send us a [pull request](https://help.github.com/articles/using-pull-requests).
* You can use the documentation of your recipe as a description to your pull request.

To ensure a consistent code base, you should make sure the code follows the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

### Recipe Do's and Don'ts

For easier integration in existing project, and fewer changes in your recipe and/or docs for it, you should try and follow this general guidelines:

* Use short file names for recipes.  Eg `ftp` instead of `ftp_upload_to_server_recype_by_me`
* Prefix all tasks in recipe with recipe name. if You have task named `mytest` in `myrecipe` it should be named `myrecipe:mytest`
* Use global settings keyed by your recipe name. If You have one setting, name it the same as Your recipe. If You have multiple settings, use associative array
* Use environment variables prefixed by Your recipe name. If You have environment varaible named `better_path` in recipe `myrecipe`, call it `myrecipe_better_path`
* Do not override existing tasks (for example - those in `common.php`). Instead document throughtly how tasks from your recipe can be integrated into workflow
* If your recipe depends on another (be it included in deployer or 3rd party) - document it throughtly. It's better for user to use `require_once` in `deploy.php`, rather than force dependecies.


### License

Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
