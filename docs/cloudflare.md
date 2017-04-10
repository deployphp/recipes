# Cloudflare recipe

### Installing

```php
// deploy.php

require 'recipe/cloudflare.php';
```

### Configuration options

- **service_key**: Cloudflare Service Key. If this is not provided, use api_key and email.

OR

- **api_key**: Cloudflare API key generated on the "My Account" page.
- **email**: Cloudflare Email address associated with your account.

PLUS

- **domain**: The domain you want to clear


### Tasks

- `deploy:cloudflare` Clears cloudflare cache

### Suggested Usage

Since the website should be built and some load is likely about to be applied to your server, this should be one of,
if not the, last tasks before cleanup

### Read more

Keep up to date with Cyber Duck's adventures on https://www.cyber-duck.co.uk/