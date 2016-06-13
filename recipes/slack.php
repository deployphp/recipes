<?php
/* (c) Tomas Majer <tomasmajer@gmail.com>
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
 * Notify Slack of successful deployment
 */
task('deploy:slack', function () {
    global $php_errormsg;

    $defaultConfig = [
        'channel'    => '#general',
        'icon'       => ':sunny:',
        'username'   => 'Deploy',
        'message'    => "Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})",
        'app'        => 'app-name',
        'unset_text' => false,
    ];

    $newConfig = get('slack', []);

    if (is_callable($newConfig)) {
        $newConfig = $newConfig();
    }

    $config = array_merge($defaultConfig, (array) $newConfig);

    if (!is_array($config) || !isset($config['token']) || !isset($config['team']) || !isset($config['channel'])) {
        throw new \RuntimeException("Please configure new slack: set('slack', ['token' => 'xoxp...', 'team' => 'team', 'channel' => '#channel', 'messsage' => 'message to send']);");
    }

    $server = \Deployer\Task\Context::get()->getServer();
    if ($server instanceof \Deployer\Server\Local) {
        $user = env('local_user');
    } else {
        $user = $server->getConfiguration()->getUser() ? : null;
    }

    $messagePlaceHolders = [
        '{{release_path}}' => env('release_path'),
        '{{host}}'         => env('server.host'),
        '{{stage}}'        => env('stages')[0],
        '{{user}}'         => $user,
        '{{branch}}'       => env('branch'),
        '{{app_name}}'     => isset($config['app']) ? $config['app'] : 'app-name',
    ];
    $config['message'] = strtr($config['message'], $messagePlaceHolders);

    $urlParams = [
        'channel'    => $config['channel'],
        'token'      => $config['token'],
        'text'       => $config['message'],
        'username'   => $config['username'],
        'icon_emoji' => $config['icon'],
        'pretty'     => true
    ];

    if ($config['unset_text']) {
        unset($urlParams['text']);
    }

    foreach (['parse', 'link_names', 'icon_url', 'unfurl_links', 'unfurl_media', 'as_user'] as $option) {
        if (isset($config[$option])) {
            $urlParams[$option] = $config[$option];
        }
    }

    if (isset($config['attachments'])) {
        $urlParams['attachments'] = json_encode($config['attachments']);
    }

    if (isset($config['icon_url'])) {
        unset($urlParams['icon_emoji']);
    }

    $url = 'https://slack.com/api/chat.postMessage?' . http_build_query($urlParams);
    $result = @file_get_contents($url);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }

    $response = @json_decode($result);

    if (!$response || isset($response->error)) {
        throw new \RuntimeException($response->error);
    }
})->desc('Notifying Slack channel of deployment');
