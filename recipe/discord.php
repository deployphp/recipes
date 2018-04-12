<?php
/* (c) Lucas Mezêncio <lucas.mezencio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer {
    use Deployer\Discord\Messaging;
    use Deployer\Discord\MessagingInterface;
    use Deployer\Task\Context;
    use Deployer\Utility\Httpie;

    set('discord_webhook', function () {
        return 'https://discordapp.com/api/webhooks/{{discord_channel}}/{{discord_token}}/slack';
    });

    // Deploy messages
    set('discord_notify_text', ':information_source: **{{user}}** is deploying branch `{{branch}}` to _{{target}}_');
    set('discord_success_text', ':white_check_mark: Branch `{{branch}}` deployed to _{{target}}_ successfully');
    set('discord_failure_text', ':no_entry_sign: Branch `{{branch}}` has failed to deploy to _{{target}}_');

    // Tasks
    desc('Setup Discord Deployer recipe');
    task('discord:setup', function () {
        if (!get('discord_channel', false) || !get('discord_token', false)) {
            throw new \Exception('`discord_channel` and `discord_token` must be set.');
        }

        $messagingClass = get('discord_class', false);
        $objectMessaging = new Messaging();

        if ($messagingClass !== false) {
            if (!class_exists($messagingClass)) {
                throw new \Exception('`discord_class` must be a valid class');
            }

            $objectMessaging = new $messagingClass();

            if (!($objectMessaging instanceof MessagingInterface)) {
                throw new \Exception('`discord_class` must implement Deployer\\Discord\\MessagingInterface');
            }
        }

        set('discord_message_object', $objectMessaging);
    })
        ->once()
        ->shallow()
        ->isPrivate();

    desc('Just notify your Discord channel with all messages, without deploying');
    task('discord:test', function () {
        $context = Context::get();

        $setup = task('discord:setup');
        $setup->run($context);

        /** @var MessagingInterface $discordMessageObject */
        $discordMessageObject = get('discord_message_object');

        $notify = $discordMessageObject->notify();
        $success = $discordMessageObject->success();
        $failure = $discordMessageObject->failure();

        Httpie::post(get('discord_webhook'))
            ->body($notify)
            ->send();

        Httpie::post(get('discord_webhook'))
            ->body($success)
            ->send();

        Httpie::post(get('discord_webhook'))
            ->body($failure)
            ->send();
    })
        ->once()
        ->shallow();

    desc('Notify Discord');
    task('discord:notify', function () {
        $body = get('discord_message_object')->notify();

        Httpie::post(get('discord_webhook'))->body($body)->send();
    })
        ->once()
        ->shallow()
        ->isPrivate();

    desc('Notify Discord about deploy finish');
    task('discord:notify:success', function () {
        $body = get('discord_message_object')->success();

        Httpie::post(get('discord_webhook'))->body($body)->send();
    })
        ->once()
        ->shallow()
        ->isPrivate();

    desc('Notify Discord about deploy failure');
    task('discord:notify:failure', function () {
        $body = get('discord_message_object')->failure();

        Httpie::post(get('discord_webhook'))->body($body)->send();
    })
        ->once()
        ->shallow()
        ->isPrivate();

    after('deploy:prepare', 'discord:setup');
}

/**
 *
 */
namespace Deployer\Discord {
    /**
     * Interface MessagingInterface
     *
     * @package Deployer\Discord
     */
    interface MessagingInterface
    {
        /**
         * @return array
         */
        public function notify();
        /**
         * @return array
         */
        public function success();
        /**
         * @return array
         */
        public function failure();
    }

    /**
     * Class Messaging
     *
     * @package Deployer\Discord
     */
    class Messaging implements MessagingInterface
    {
        /**
         * @inheritdoc
         */
        public function notify()
        {
            return [
                'text' => get('discord_notify_text'),
            ];
        }
        /**
         * @inheritdoc
         */
        public function success()
        {
            return [
                'text' => get('discord_success_text'),
            ];
        }
        /**
         * @inheritdoc
         */
        public function failure()
        {
            return [
                'text' => get('discord_failure_text'),
            ];
        }
    }
}
