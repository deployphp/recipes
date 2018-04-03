<?php

namespace Deployer\Discord;

use function Deployer\get;

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
            'attachments' => [
                [
                    'color' => 'good',
                    'title' => 'My Application',
                    'fields' => [
                        [
                            'title' => 'Environent',
                            'value' => get('stage'),
                            'short' => true,
                        ],
                    ],
                ],
            ],
        ];

        //return [
        //    'text' => get('discord_notify_text'),
        //];
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
