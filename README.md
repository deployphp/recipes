# Deployer Recipes

This repository contains third party recipes to integrate with deployer.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/deployphp/deployer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Using these recipes

First install this repository as a dev dependency.

**For Deployer 4.x**

```sh
composer require --dev deployer/recipes
```

**For Deployer 3.x**

```sh
composer require --dev "deployphp/recipes ~3.0"
```

**For Deployer 2.x**

```sh
composer require --dev "deployphp/recipes ~2.0"
```

Include recipes as desired.

```php
// deploy.php

require 'vendor/deployer/recipes/cachetool.php';
```

## Recipes

| Recipe     | Docs                       | Usage
| ------     | ----                       | -----
| cachetool  | [read](docs/cachetool.md)  | `require 'vendor/deployer/recipes/cachetool.php';`
| cloudflare | [read](docs/cloudflare.md) | `require 'vendor/deployer/recipes/cloudflare.php';`
| hipchat    | [read](docs/hipchat.md)    | `require 'vendor/deployer/recipes/hipchat.php';`
| local      | [read](docs/local.md)      | `require 'vendor/deployer/recipes/local.php';`
| newrelic   | [read](docs/newrelic.md)   | `require 'vendor/deployer/recipes/newrelic.php';`
| phinx      | [read](docs/phinx.md)      | `require 'vendor/deployer/recipes/phinx.php'`
| rabbit     | [read](docs/rabbit.md)     | `require 'vendor/deployer/recipes/rabbit.php';`
| rsync      | [read](docs/rsync.md)      | `require 'vendor/deployer/recipes/rsync.php';`
| slack      | [read](docs/slack.md)      | `require 'vendor/deployer/recipes/slack.php';`
| npm        | [read](docs/npm.md)        | `require 'vendor/deployer/recipes/npm.php';`

## Contributing a recipe

All code contributions must go through a pull request and be approved by a core developer before being merged.
This is to ensure proper review of all the code.

* [Fork and clone](https://help.github.com/articles/fork-a-repo).
* Create a branch.
  * If the recipe is for Deployer `4.x` then create your branch based on `master`
  * If the recipe is for Deployer `3.x` then create your branch based on `3.x`
  * If the recipe is for Deployer `2.x` then create your branch based on the `2.x` branch
* Add your recipe to the `recipes` folder, it must be licensed as MIT.
* Add documentation in Markdown for your recipe to the `docs` folder; you could base your documentation on
[cachetool.md](docs/cachetool.md) as it is fairly complete.
* Add your recipe to the table above in `README.md`. Please use alphabetical order.
* Commit, push and send us a [pull request](https://help.github.com/articles/using-pull-requests).
* You can use the documentation of your recipe as a description for your pull request.

To ensure a consistent code base, you should make sure the code follows
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

### Recipe Do's and Don'ts

For easier integration in existing projects and fewer changes in your recipe (and/or docs for it) you should try and
follow these general guidelines:

* Use short file names for recipes, e.g. `ftp` instead of `ftp_upload_to_server_recipe_by_me`
* Prefix all tasks in recipe with your recipe name. If you have a task named `mytest` in
`myrecipe` it should be named `myrecipe:mytest`
* Use global settings prefixed by your recipe name. If you have one setting, give it the same name as your recipe.
If you have multiple settings, use an associative array
* Use environment variables prefixed by your recipe name. If you have an environment variable named `better_path`
in recipe `myrecipe`, call it `myrecipe_better_path`
* Do not override existing tasks (for example those in `common.php`). Instead document thoroughly how tasks from your
recipe can be integrated into the workflow
* If your recipe depends on another (be it included in deployer or 3rd party) document it thoroughly.
It's better for the end user to use `require_once` in `deploy.php` than to force dependencies.


### License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).
