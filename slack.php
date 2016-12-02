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

// Do not skip slack notifications by default
set('slack_skip_notification', false);

desc('Notifying Slack channel of deployment');
task('deploy:slack', function () {
    if (true === get('slack_skip_notification')) {
        return;
    }

    global $php_errormsg;

    $user = trim(runLocally('git config --get user.name'));
    $revision = trim(runLocally('git log -n 1 --format="%h"'));

    $defaultConfig = [
        'channel'     => '#general',
        'icon'        => ':sunny:',
        'username'    => 'Deploy',
        'message'     => "Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})",
        'app'         => 'app-name',
        'unset_text'  => true,
        'attachments' => [
            [
                'text' => sprintf(
                    'Revision %s deployed to %s by %s',
                    substr($revision, 0, 6),
                    env('stages')[0],
                    $user
                ),
                'title'    => 'Deployment Complete',
                'fallback' => sprintf('Deployment to %s complete.', env('stages')[0]),
                'color'    => '#7CD197',
                'fields'   => [
                    [
                        'title' => 'User',
                        'value' => $user,
                        'short' => true,
                    ],
                    [
                        'title' => 'Stage',
                        'value' => env('stages')[0],
                        'short' => true,
                    ],
                    [
                        'title' => 'Branch',
                        'value' => env('branch'),
                        'short' => true,
                    ],
                    [
                        'title' => 'Host',
                        'value' => env('server.name'),
                        'short' => true,
                    ],
                ],
            ]
        ]
    ];

    $newConfig = get('slack');

    if (is_callable($newConfig)) {
        $newConfig = $newConfig();
    }

    $config = array_merge($defaultConfig, (array) $newConfig);

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

    if (isset($config['proxy'])) {
      if (!is_array($config) || !isset($config['proxy']) || !isset($config['proxy']['user']) || !isset($config['proxy']['pass']) || !isset($config['proxy']['url'])) {
        throw new \RuntimeException("Incorrectly set up proxy");
      } else {
        $auth = base64_encode("$config['proxy']['user']" . ":" . "$config['proxy']['pass']");
        $options = array(
          'http' => array (
            'method'=>'GET',
            'proxy'=>'tcp://' . $config['proxy']['url'],
            'request_fulluri' => true,
            'header'=> "Proxy-Authorization: Basic $auth"
          ),
          'https' => array (
            'method'=>'GET',
            'proxy'=>'tcp://' . $config['proxy']['url'],
            'request_fulluri' => true,
            'header'=> "Proxy-Authorization: Basic $auth"
          )
        );
        $context = stream_context_create($options);
      }
    }

    $url = 'https://slack.com/api/chat.postMessage?' . http_build_query($urlParams);
    $result = @file_get_contents($url, false, $context);

    if (!$result) {
        throw new \RuntimeException($php_errormsg);
    }

    $response = @json_decode($result);

    if (!$response || isset($response->error)) {
        throw new \RuntimeException($response->error);
    }
})->desc('Notifying Slack channel of deployment');
