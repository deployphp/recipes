<?php
/* (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Notify Hipchat of successful deployment
 */
task('deploy:hipchat', function () {
    global $php_errormsg;

    $config = get('hipchat', []);

    if (!isset($config['message'])) {
        $releasePath = env('release_path');
        $host = env('server.host');
        $stage = env('stages')[0];
        $config['message'] = "Deployment to '{$host}' on *{$stage}* was successful\n($releasePath)";
    }

    if (!isset($config['from'])) {
        $stage = env('stages')[0];
        $config['from'] = $stage;
    }

    $defaultConfig = [
        'color' => 'green',
        'format' => 'json',
        'notify' => 0,
        'endpoint' => 'https://api.hipchat.com/v1/rooms/message',
    ];

    $config = array_merge($defaultConfig, $config);
    if (!is_array($config) ||
        !isset($config['auth_token']) ||
        !isset($config['room_id']))
    {
        throw new \RuntimeException("Please configure new hipchat: set('hipchat', array('auth_token' => 'xxx', 'room_id' => 'yyy'));");
    }

    $endpoint = $config['endpoint'];
    unset($config['endpoint']);

    $urlParams = [
        'room_id' => $config['room_id'],
        'from' => $config['from'],
        'message' => $config['message'],
        'color' => $config['color'],
        'auth_token' => $config['auth_token'],
        'notify' => $config['notify'],
        'format' => $config['format'],
    ];

    $url = $endpoint . '?' . http_build_query($urlParams);

    $result = @file_get_contents($url);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }
})->desc('Notifying Hipchat channel of deployment');
