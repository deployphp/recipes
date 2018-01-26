<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

// Title of project
set(
	'slack_title',
	function () {
		return get('application', 'Project');
	}
);

// Deploy message
set('slack_text', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
set('slack_success_text', 'Deploy to *{{target}}* successful');
set('slack_failure_text', 'Deploy to *{{target}}* failed');

// Color of attachment
set('slack_color', '#4d91f7');
set('slack_success_color', 'good');
set('slack_failure_color', 'danger');

// Username
set('slack_username', 'Deployer');

// Icon (Deployer icon by default)
set('slack_icon_emoji', '');
set('slack_icon_url', 'https://deployer.org/images/deployer-sticker.png');

// Pretext
set('slack_pretext', '');
set('slack_success_pretext', '');
set('slack_failure_pretext', '');

desc('Notifying Slack');
task(
	'slack:notify',
	function () {
		if (!get('slack_webhook', false)) {
			return;
		}

		$messageContent = [
			'username' => get('slack_username'),
			'icon_emoji' => get('slack_icon_emoji'),
			'icon_url' => get('slack_icon_url'),
			'attachments' => [
				[
					'title' => get('slack_title'),
					'pretext' => get('slack_pretext'),
					'text' => get('slack_text'),
					'color' => get('slack_color'),
					'mrkdwn_in' => ['text', 'pretext'],
				],
			],
		];


		Httpie::post(get('slack_webhook'))->body($messageContent)->send();
	}
)
	->once()
	->shallow()
	->setPrivate();

desc('Notifying Slack about deploy finish');
task(
	'slack:notify:success',
	function () {
		if (!get('slack_webhook', false)) {
			return;
		}

		$messageContent = [
			'username' => get('slack_username'),
			'icon_emoji' => get('slack_icon_emoji'),
			'icon_url' => get('slack_icon_url'),
			'attachments' => [
				[
					'title' => get('slack_title'),
					'pretext' => get('slack_success_pretext'),
					'text' => get('slack_success_text'),
					'color' => get('slack_success_color'),
					'mrkdwn_in' => [
						'text',
						'pretext',
					],
				],
			],
		];


		Httpie::post(get('slack_webhook'))->body($messageContent)->send();
	}
)
	->once()
	->shallow()
	->setPrivate();

desc('Notifying Slack about deploy failure');
task(
	'slack:notify:failure',
	function () {
		if (!get('slack_webhook', false)) {
			return;
		}

		$messageContent = [
			'username' => get('slack_username'),
			'icon_emoji' => get('slack_icon_emoji'),
			'icon_url' => get('slack_icon_url'),
			'attachments' => [
				[
					'title' => get('slack_title'),
					'pretext' => get('slack_failure_pretext'),
					'text' => get('slack_failure_text'),
					'color' => get('slack_failure_color'),
					'mrkdwn_in' => ['text', 'pretext'],
				],
			],
		];


		Httpie::post(get('slack_webhook'))->body($messageContent)->send();
	}
)
	->once()
	->shallow()
	->setPrivate();
