<?php
/* (c) Tim Robertson <funkjedi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

desc('Notifying Bugsnag of deployment');
task('deploy:bugsnag', function () {
    global $php_errormsg;

    $defaultConfig = [
        'api_key'       => null,
        'release_stage' => get('stages')[0],
        'repository'    => get('repository'),
        'provider'      => null,
        'branch'        => get('branch'),
        'revision'      => trim(runLocally('git log -n 1 --format="%h"')),
        'app_version'   => null,
    ];

    $config = array_merge($defaultConfig, (array) get('bugsnag'));

    if (!is_array($config) || !isset($config['api_key'])) {
        throw new \RuntimeException("Please configure new bugsnag: set('bugsnag', ['api_key' => 'c09a3...', 'release_stage' => 'production']);");
    }

    $postdata = [
        'apiKey'       => $config['api_key'],
        'releaseStage' => $config['release_stage'],
        'repository'   => $config['repository'],
        'provider'     => $config['provider'],
        'branch'       => $config['branch'],
        'revision'     => $config['revision'],
        'appVersion'   => $config['app_version'],
    ];

    $options = array('http' => array(
        'method' => 'POST',
        'header' => "Content-type: application/json\r\n",
        'content' => json_encode($postdata),
    ));

    $context = stream_context_create($options);
    $result = @file_get_contents('https://notify.bugsnag.com/deploy', false, $context);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }
});
