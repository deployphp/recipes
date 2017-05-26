<?php
/* (c) Laurent Goussard <loranger@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;


/**
 * Get local username
 */
set('local_user', function () {
    return trim(run("whoami"));
});

desc('Notifying Rollbar of deployment');
task('deploy:rollbar', function () {
    global $php_errormsg;

    $defaultConfig = [
        'access_token'      => null,
        'environment'       => get('stage'),
        'revision'          => trim(runLocally('git log -n 1 --format="%h"')),
        'local_username'    => trim(runLocally('git config user.name')),
        'rollbar_username'  => null,
        'comment'           => "Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})",
    ];

    $config = array_merge($defaultConfig, (array) get('rollbar', []));

    if (!is_array($config) || !isset($config['access_token'])) {
        throw new \RuntimeException("Please configure new rollbar: set('rollbar', ['access_token' => 'c09a3...', 'revision' => 'v4.3', 'rollbar_username' => 'John Doe', 'comment' => 'Brand new version']);");
    }

    $host = \Deployer\Task\Context::get()->getHost();
    if ($host instanceof \Deployer\Host\Localhost) {
        $user = get('local_user');
    } else {
        $user = $host->getUser() ? : null;
    }

    $commentPlaceHolders = [
        '{{release_path}}' => get('release_path'),
        '{{host}}'         => $host,
        '{{stage}}'        => get('stage'),
        '{{user}}'         => $user,
        '{{branch}}'       => get('branch'),
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
});
