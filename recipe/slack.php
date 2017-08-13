<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Request;

// Title of project
set('slack_title', function () {
    return get('application', 'Project');
});

// Name of stage or host where deploying
set('slack_target', function () {
    return input()->getArgument('stage') ?: get('hostname');
});

// Deploy message
set('slack_text', '_{{user}}_ deploying `{{branch}}` to *{{slack_target}}*');
set('slack_success_text', 'Deploy to *{{slack_target}}* successful');

// Color of attachment
set('slack_color', '#4d91f7');
set('slack_success_color', '{{slack_color}}');

desc('Notifying Slack');
task('slack:notify', function () {
    if (!get('slack_webhook', false)) {
        return;
    }

    $attachment = [
        'title' => get('slack_title'),
        'text' => get('slack_text'),
        'color' => get('slack_color'),
        'mrkdwn_in' => ['text'],
    ];

    Request::post(get('slack_webhook'), ['attachments' => [$attachment]]);
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notifying Slack about deploy finish');
task('slack:notify:success', function () {
    if (!get('slack_webhook', false)) {
        return;
    }

    $attachment = [
        'title' => get('slack_title'),
        'text' => get('slack_success_text'),
        'color' => get('slack_success_color'),
        'mrkdwn_in' => ['text'],
    ];

    Request::post(get('slack_webhook'), ['attachments' => [$attachment]]);
})
    ->once()
    ->shallow()
    ->setPrivate();
