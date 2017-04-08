# NPM recipe

### Installing

~~~php
require 'recipe/npm.php';
~~~

### Configuration options

- `bin/npm` *(optional)*: set NPM binary, automatically detected otherwise. 

### Tasks

- `npm:install` Install NPM packages

### Suggested Usage

~~~php
after('deploy:update_code', 'npm:install');
~~~
