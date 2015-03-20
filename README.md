# Deployer Recipes

This repository contains third party recipes to integrate with deployer.

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

| Recipe    | Docs                                                                      | Usage
| ------    | ----                                                                      | -----
| cachetool | [read](http://github.com/deployphp/recipes/blob/master/docs/cachetool.md) | `require 'vendor/deployphp/recipes/recipes/cachetool.php';`
| newrelic  | [read](http://github.com/deployphp/recipes/blob/master/docs/newrelic.md)  | `require 'vendor/deployphp/recipes/recipes/newrelic.php';`
| slack     | [read](http://github.com/deployphp/recipes/blob/master/docs/slack.md)     | `require 'vendor/deployphp/recipes/recipes/slack.php';`
| rabbit    | [read](http://github.com/deployphp/recipes/blob/master/docs/rabbit.md)    | `require 'vendor/deployphp/recipes/recipes/rabbit.php';`

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

To ensure a consistent code base, you should make sure the code follows the [Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html) which we borrowed from Symfony.

### License

Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
