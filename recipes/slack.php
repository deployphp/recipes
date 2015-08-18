<?php
/* (c) Tomas Majer <tomasmajer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Notify Slack of successful deployment
 */
task('deploy:slack', function () {
    global $php_errormsg;

    $defaultConfig = [
        'channel'  => '#general',
        'icon'     => ':sunny:',
        'username' => 'Deploy',
        'message'  => "Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})",
        'app'      => 'app-name',
    ];

    $config = array_merge($defaultConfig, (array) get('slack', []));
    
    $server = \Deployer\Task\Context::get()->getServer()->getConfiguration();
    $user = !$server->getUser() ? null : $server->getUser();
    $messagePlaceHolders = [
        '{{release_path}}' => env('release_path'),
        '{{host}}'         => env('server.host'),
        '{{stage}}'        => env('stages')[0],
        '{{user}}'         => $user,
        '{{branch}}'       => env('branch'),
        '{{app_name}}'     => isset($config['app']) ? $config['app'] : 'app-name',
    ];
    $config['message'] = strtr($config['message'], $messagePlaceHolders);
    
    if (!is_array($config) ||
        !isset($config['token']) ||
        !isset($config['team']) ||
        !isset($config['channel']))
    {
        throw new \RuntimeException("Please configure new slack: set('slack', ['token' => 'xoxp...', 'team' => 'team', 'channel' => '#channel', 'messsage' => 'message to send']);");
    }

    $urlParams = [
        'channel'    => $config['channel'],
        'token'      => $config['token'],
        'text'       => $config['message'],
        'username'   => $config['username'],
        'icon_emoji' => $config['icon'],
        'pretty'     => true
    ];

    if (isset($config['icon_url'])) {
        unset($urlParams['icon_emoji']);
        $urlParams['icon_url'] = $config['icon_url'];
    }

    $url = 'https://slack.com/api/chat.postMessage?' . http_build_query($urlParams);
    $result = @file_get_contents($url);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }
})->desc('Notifying Slack channel of deployment');
