<?php
/* (c) Samuel Gordalina <samuel.gordalina@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

set('newrelic_deploy_user', function() {
    return trim(runLocally('git config user.name'));
});
set('newrelic_deploy_revision', function() {
    return trim(runLocally('git log -n 1 --format="%h"'));
});
set('newrelic_deploy_description', function() {
    return trim(runLocally('git log -n 1 --format="%an: %s" | tr \'"\' "\'"'));
});

desc('Notifying New Relic of deployment');
task('deploy:newrelic', function () {
    global $php_errormsg;

    $config = get('newrelic', []);

    // Notify existing users of upgrade.
    if (!empty(array_intersect(array_keys($config), ['license', 'app_name']))) {
        throw new \RuntimeException("New Relic recipe has been updated to use Deployment API v2. Please replace license and/or app_name with api_key and application_id");
    }

    if (!is_array($config) ||
        !isset($config['api_key']) || !isset($config['application_id'])
    ) {
        throw new \RuntimeException("<comment>Please configure New Relic:</comment> <info>set('newrelic', array('api_key' => 'xad3...', 'application_id' => '12873'));</info>");
    }

    $deploy_data = [
        'user' => get('newrelic_deploy_user'),
        'revision' => get('newrelic_deploy_revision'),
        'description' => get('newrelic_deploy_description'),
    ];

    $options = ['http' => [
        'method' => 'POST',
        'header' =>
            "Content-type: application/json\r\n" .
            "X-Api-Key: {$config['api_key']}\r\n",
        'content' => json_encode(['deployment' => $deploy_data]),
    ]];

    $context = stream_context_create($options);
    $endpoint = "https://api.newrelic.com/v2/applications/{$config['application_id']}/deployments.json";
    $result = @file_get_contents($endpoint, false, $context);

    if ($result === false) {
        throw new \RuntimeException($php_errormsg);
    }
});
