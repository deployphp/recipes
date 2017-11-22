<?php
/* (c) Tomas Majer <tomasmajer@gmail.com>
/* (c) Elan Ruusamäe <glen@delfi.ee>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

/*
 * Get local username
 */
set('local_user', function () {
    return trim(run('whoami'));
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
    $stage = get('stages')[0];
    $branch = get('branch');
    if (input()->hasOption('branch')) {
        $inputBranch = input()->getOption('branch');
        if (!empty($inputBranch)) {
            $branch = $inputBranch;
        }
    }
    $defaultConfig = [
        'channel'     => '#general',
        'icon'        => ':sunny:',
        'username'    => 'Deploy',
        'message'     => "Deployment to `{{host}}` on *{{stage}}* was successful\n({{release_path}})",
        'app'         => 'app-name',
        'attachments' => [
            [
                'text' => sprintf(
                    'Revision %s deployed to %s by %s',
                    substr($revision, 0, 6),
                    $stage,
                    $user
                ),
                'title'    => 'Deployment Complete',
                'fallback' => sprintf('Deployment to %s complete.', $stage),
                'color'    => '#7CD197',
                'fields'   => [
                    [
                        'title' => 'User',
                        'value' => $user,
                        'short' => true,
                    ],
                    [
                        'title' => 'Stage',
                        'value' => $stage,
                        'short' => true,
                    ],
                    [
                        'title' => 'Branch',
                        'value' => $branch,
                        'short' => true,
                    ],
                    [
                        'title' => 'Host',
                        'value' => get('server.host'),
                        'short' => true,
                    ],
                ],
            ],
        ],
    ];

    $newConfig = get('slack');

    if (is_callable($newConfig)) {
        $newConfig = $newConfig();
    }

    $config = array_merge($defaultConfig, (array) $newConfig);
    if(isset($newConfig['attachmentCustom'])){
        $config['attachments'][0] = array_merge($config['attachments'][0], $newConfig['attachmentCustom']);
    }

    if (!is_array($config) || !isset($config['token']) || !isset($config['team']) || !isset($config['channel'])) {
        throw new \RuntimeException("Please configure new slack: set('slack', ['token' => 'xoxp...', 'team' => 'team', 'channel' => '#channel', 'messsage' => 'message to send']);");
    }

    $server = \Deployer\Task\Context::get()->getServer();
    if ($server instanceof \Deployer\Server\Local) {
        $user = get('local_user');
    } else {
        $user = $server->getConfiguration()->getUser() ?: null;
    }

    $messagePlaceHolders = [
        '{{release_path}}' => get('release_path'),
        '{{host}}'         => get('server.host'),
        '{{stage}}'        => $stage,
        '{{user}}'         => $user,
        '{{branch}}'       => $branch,
        '{{app_name}}'     => isset($config['app']) ? $config['app'] : 'app-name',
    ];
    $config['message'] = strtr($config['message'], $messagePlaceHolders);

    $urlParams = [
        'channel'    => $config['channel'],
        'token'      => $config['token'],
        'text'       => $config['message'],
        'username'   => $config['username'],
        'icon_emoji' => $config['icon'],
        'pretty'     => true,
    ];

    foreach (['unset_text' => 'text', 'icon_url' => 'icon_emoji'] as $set => $unset) {
        if (isset($config[$set])) {
            unset($urlParams[$unset]);
        }
    }

    foreach (['parse', 'link_names', 'icon_url', 'unfurl_links', 'unfurl_media', 'as_user'] as $option) {
        if (isset($config[$option])) {
            $urlParams[$option] = $config[$option];
        }
    }

    if (isset($config['attachments'])) {
        $urlParams['attachments'] = json_encode($config['attachments']);
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
