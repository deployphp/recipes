<?php
/* (c) Laurent Goussard <loranger@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Get local username
 */
env('local_user', function () {
    return trim(run("whoami"));
});

/**
 * Notify Rollbar of successful deployment
 */
task('deploy:rollbar', function () {
    global $php_errormsg;

    $defaultConfig = [
        'access_token'      => null,
        'environment'       => env('stages')[0],
        'revision'          => trim(runLocally('git log -n 1 --format="%h"')),
        'local_username'    => trim(runLocally('git config user.name')),
        'rollbar_username'  => null,
        'comment'           => "Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})",
    ];

    $config = array_merge($defaultConfig, (array) get('rollbar', []));

    if (!is_array($config) || !isset($config['access_token'])) {
        throw new \RuntimeException("Please configure new rollbar: set('rollbar', ['access_token' => 'c09a3...', 'revision' => 'v4.3', 'rollbar_username' => 'John Doe', 'comment' => 'Brand new version']);");
    }

    $server = \Deployer\Task\Context::get()->getServer();
    if ($server instanceof \Deployer\Server\Local) {
        $user = env('local_user');
    } else {
        $user = $server->getConfiguration()->getUser() ? : null;
    }

    $commentPlaceHolders = [
        '{{release_path}}' => env('release_path'),
        '{{host}}'         => env('server.host'),
        '{{stage}}'        => env('stages')[0],
        '{{user}}'         => $user,
        '{{branch}}'       => env('branch'),
    ];
    $config['comment'] = strtr($config['comment'], $commentPlaceHolders);

    $urlParams = [
        'access_token'      => $config['access_token'],
        'environment'       => $config['environment'],
        'revision'          => $config['revision'],
        'local_username'    => $config['local_username'],
        'rollbar_username'  => $config['rollbar_username'],
        'comment'           => $config['comment'],
    ];

    $options = array('http' => array(
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($urlParams),
    ));

    $context = stream_context_create($options);
    $result = @file_get_contents('https://api.rollbar.com/api/1/deploy/', false, $context);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }
})->desc('Notifying Rollbar of deployment');
