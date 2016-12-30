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

    $config = get('newrelic', array());

    if (!is_array($config) ||
        !isset($config['license']) ||
        (!isset($config['app_name']) && !isset($config['application_id']))
    ) {
        throw new \RuntimeException("<comment>Please configure new relic:</comment> <info>set('newrelic', array('license' => 'xad3...', 'application_id' => '12873'));</info>");
    }

    $git = [
        'user' => get('newrelic_deploy_user'),
        'revision' => get('newrelic_deploy_revision'),
        'description' => get('newrelic_deploy_description'),
    ];

    $postdata = array_merge($git, $config);
    unset($postdata['license']);

    $options = array('http' => array(
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "X-License-Key: {$config['license']}\r\n",
        'content' => http_build_query(array('deployment' => $postdata)),
    ));

    $context = stream_context_create($options);
    $result = @file_get_contents('https://api.newrelic.com/deployments.xml', false, $context);

    if ($result === false) {
        throw new \RuntimeException($php_errormsg);
    }
});
