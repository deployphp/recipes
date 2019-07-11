# NPM recipe

## Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

~~~php
require 'recipe/npm.php';
~~~

## Configuration

- `bin/npm` *(optional)*: set npm binary, automatically detected otherwise. 

## Tasks

- `npm:install` – install npm packages
- `npm:ci` – install npm packages with a new and "clean" node_modules directory

## Usage

~~~php
after('deploy:update_code', 'npm:install');
~~~

or if you want use `npm ci` command
~~~php
after('deploy:update_code', 'npm:ci');
~~~
