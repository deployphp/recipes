# Cloudflare recipe

### Installing

Install with composer

```bash
composer require deployer/recipes --dev
```

Add to your _deploy.php_

```php
require 'recipe/cloudflare.php';
```

### Configuration

- `cloudflare` – array with configuration for cloudflare
    - `service_key` – Cloudflare Service Key. If this is not provided, use api_key and email.
    - `api_key` – Cloudflare API key generated on the "My Account" page.
    - `email` – Cloudflare Email address associated with your account.
    - `domain` – The domain you want to clear


### Tasks

- `deploy:cloudflare` Clears cloudflare cache

### Usage

Since the website should be built and some load is likely about to be applied to your server, this should be one of,
if not the, last tasks before cleanup
