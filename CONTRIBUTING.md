# Contributing

Thank you for considering contributing to Deployer. Please make sure to read the following sections if you plan on submitting new issues or pull requests.

## Bug

In order for us to provide you with help as fast as possible, please make sure to include the following when reporting bugs.

* Deployer version
* PHP version
* Deployment target(s) OS
* Content of `deploy.php`
* Output log with enabled option for verbose output `-vvv`

## New features

All code contributions must go through a pull request and approved by a core developer before being merged.
This is to ensure proper review of all the code.

Fork the project, create a feature branch, and send a pull request.

To ensure a consistent code base, you should make sure the code follows
the [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md).

## Recipe Do's and Don'ts

For easier integration in existing projects and fewer changes in your recipe (and/or docs for it) you should try and
follow these general guidelines:

* Use short file names for recipes, e.g. `ftp` instead of `ftp_upload_to_server_recipe_by_me`
* Prefix all tasks in recipe with your recipe name. If you have a task named `mytest` in `myrecipe` it should be named `myrecipe:mytest`
* Use config prefixed by your recipe name. If you have one config, give it the same name as your recipe.
* Do not override existing tasks (for example those in `common.php`). Instead document thoroughly how tasks from your recipe can be integrated into the workflow
* If your recipe depends on another (be it included in deployer or 3rd party) document it thoroughly.
