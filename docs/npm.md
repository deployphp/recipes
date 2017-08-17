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

## Usage

~~~php
after('deploy:update_code', 'npm:install');
~~~
