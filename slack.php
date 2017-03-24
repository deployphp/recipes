<?php
/* (c) Tomas Majer <tomasmajer@gmail.com>
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

    if (input()->getOption('branch')) {
        set('branch', input()->getOption('branch'));
    } elseif(empty(get('branch'))) {
        set('branch', 'master');
    }

    $prevRef = trim(run('cd {{deploy_path}} && if [ -e .revision ]; then cat .revision; else echo 0; fi'));
    $currentRef = trim(runLocally('git rev-parse --verify HEAD'));
    if ($prevRef && $currentRef) {
        $commits = runLocally('git log ' . (string)$prevRef . '..' . (string)$currentRef . ' --format="%an: %s"');
    } else {
        $commits = 'Could not correctly fetch recent commits (previous commit: ' . $prevRef . ', current commit: ' . $currentRef . ')';
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
        'icon'        => ':rocket:',
        'username'    => 'Deploy',
        'message'     => "Deployed commits:\n{{commits}}",
        'app'         => 'app-name',
        'unset_text'  => true,
        'use_text_in_attachment' => true,
        'attachments' => [
            [
                'text' => sprintf(
                    'Revision %s deployed to %s by %s',
                    substr($revision, 0, 6),
                    get('stages')[0],
                    $user
                ),
                'title'    => 'Deployment ' . get('projectname') . ' complete',
                'fallback' => sprintf('Deployment to %s complete.', get('stages')[0]),
                'color'    => '#7CD197',
                'fields'   => [
                    [
                        'title' => 'User',
                        'value' => $user,
                        'short' => true,
                    ],
                    [
                        'title' => 'Stage',
                        'value' => get('stages')[0],
                        'short' => true,
                    ],
                    [
                        'title' => 'Branch',
                        'value' => (get('branch') ? get('branch') : 'master'),
                        'short' => true,
                    ],
                    [
                        'title' => 'Host',
                        'value' => get('server.name'),
                        'short' => true,
                    ],
                    [
                        'title' => 'Release Path',
                        'value' => get('release_path'),
                        'short' => true,
                    ],
                ],
            ],
        ],
    ];

    $newConfigs = get('slack');

    if (is_callable($newConfigs)) {
        $newConfigs = $newConfigs();
    }

    // Transform to array even though there is only one indentation
    if (key_exists('token', $newConfigs)) {
        $newConfigs = [$newConfigs];
    }

    foreach ($newConfigs as $key => $newConfig) {
        $config = array_merge($defaultConfig, (array)$newConfig);

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
            '{{host}}' => get('server.host'),
            '{{stage}}' => get('stages')[0],
            '{{user}}' => $user,
            '{{branch}}' => get('branch'),
            '{{app_name}}' => isset($config['app']) ? $config['app'] : 'app-name',
            '{{commits}}' => $commits
        ];
        $config['message'] = strtr($config['message'], $messagePlaceHolders);

        $urlParams = [
            'channel' => $config['channel'],
            'token' => $config['token'],
            'text' => $config['message'],
            'username' => $config['username'],
            'icon_emoji' => $config['icon'],
            'pretty' => true
        ];

        if ($config['unset_text']) {
            unset($urlParams['text']);
        }

        if ($config['use_text_in_attachment']) {
            $config['attachments'][0]['text'] = $config['message'];
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
    }
    exit;
})->desc('Notifying Slack channel of deployment');

task ('slack:revision:save', function () {
    $currentRef = runLocally('git rev-parse --verify HEAD');
    run('cd {{deploy_path}} && echo ' . $currentRef . ' > .revision');
})->desc('Save latest revision to file');

after('deploy:slack', 'slack:revision:save');
