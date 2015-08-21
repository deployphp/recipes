# Hipchat recipe

### Installing

```php
// deploy.php

require 'vendor/deployphp/recipes/recipes/hipchat.php';
```

### Configuration options

- **hipchat** *(required)*: accepts an *array* with the auth token and room id. Token can be generated on the "Group Admin / API" page on the [hipchat website](https://hipchat.com/).

 - *auth_token* - Hipchat V1 auth token
 - *room_id* - Room ID or name

You can provide also other configuration options:

 - *message* - Deploy message, default is **Deployment to '{$host}' on *{$prod}* was successful\n({$releasePath})**
 - *color* - Message color, default is **green**
 - *notify* - Notify, default is **0**
 - *endpoint* - API endpoint, change this if you run your own hipchat instance, default is **https://api.hipchat.com/v1/rooms/message**

```php
// deploy.php

set('hipchat', [
    'auth_token' => 'abcdef...',
    'room_id'    => 'my room',
]);
```

### Tasks

- `deploy:hipchat` send message to hipchat

### Suggested Usage

Since you should only notify Hipchat room of a successfull deployment, the `deploy:hipchat` task should be executed right at the end.

```php
// deploy.php

after('deploy', 'deploy:hipchat');
```
