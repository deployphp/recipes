<?php
/* (c) Tomas Majer <tomasmajer@gmail.com>
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



desc('Notifying Slack channel of deployment');
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

    if (!is_array($config) || !isset($config['token']) || !isset($config['team']) || !isset($config['channel'])) {
        throw new \RuntimeException("Please configure new slack: set('slack', ['token' => 'xoxp...', 'team' => 'team', 'channel' => '#channel', 'messsage' => 'message to send']);");
    }

    $server = \Deployer\Task\Context::get()->getServer();
    if ($server instanceof \Deployer\Server\Local) {
        $user = get('local_user');
    } else {
        $user = $server->getConfiguration()->getUser() ? : null;
    }

    $messagePlaceHolders = [
        '{{release_path}}' => get('release_path'),
        '{{host}}'         => get('server.host'),
        '{{stage}}'        => get('stages')[0],
        '{{user}}'         => $user,
        '{{branch}}'       => get('branch'),
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

    if (isset($config['icon_url'])) {
        unset($urlParams['icon_emoji']);
        $urlParams['icon_url'] = $config['icon_url'];
    }

    $url = 'https://slack.com/api/chat.postMessage?' . http_build_query($urlParams);
    $result = @file_get_contents($url);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }
});
