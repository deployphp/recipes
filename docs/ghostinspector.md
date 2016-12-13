# Ghostinspector recipe

### Installing

```php
// deploy.php

require 'vendor/deployer/recipes/ghostinspector.php';
```

### Configuration options

- **apikey**: Ghostinspector API key, find it under 'Run this suite'
- **testsuite**: Ghostinspector testsuite ID, find it under 'Run this suite'

PLUS

- **version** (optional); defaults to v1
- **starturl** (optional); add this to run the tests on a different URL than the configured URL in Ghostinspector


### Tasks

- `ghostinspector:run` Runs the configured testsuite

### Suggested Usage

You can run this suite before deployment, for example when checking everything works as intended on the staging server. Pass the starturl parameter with the staging URL to have it run on that domain. You can also run it after deployment on production, to check whether everything works as intended on production.