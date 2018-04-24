<?php
namespace Deployer;

use Deployer\Task\Context;
use Deployer\Utility\Httpie;

set('discord_webhook', function () {
    return 'https://discordapp.com/api/webhooks/{{discord_channel}}/{{discord_token}}/slack';
});

// Deploy messages
set('discord_notify_text', [
    'text' => ':information_source: **{{user}}** is deploying branch `{{branch}}` to _{{target}}_',
]);
set('discord_success_text', [
    'text' => ':white_check_mark: Branch `{{branch}}` deployed to _{{target}}_ successfully',
]);
set('discord_failure_text', [
    'text' => ':no_entry_sign: Branch `{{branch}}` has failed to deploy to _{{target}}_',
]);

// Helpers
set('send_message', function ($data) {
    Httpie::post(get('discord_webhook'))->body($data)->send();
});

// Tasks
desc('Just notify your Discord channel with all messages, without deploying');
task('discord:test', function () {
    $notify = get('discord_notify_text');
    $success = get('discord_success_text');
    $failure = get('discord_failure_text');

    get('send_message')($notify);
    get('send_message')($success);
    get('send_message')($failure);
})
    ->once()
    ->shallow();

desc('Notify Discord');
task('discord:notify', function () {
    get('send_message')(get('discord_notify_text'));
})
    ->once()
    ->shallow()
    ->isPrivate();

desc('Notify Discord about deploy finish');
task('discord:notify:success', function () {
    get('send_message')(get('discord_success_text'));
})
    ->once()
    ->shallow()
    ->isPrivate();

desc('Notify Discord about deploy failure');
task('discord:notify:failure', function () {
    get('send_message')(get('discord_failure_text'));
})
    ->once()
    ->shallow()
    ->isPrivate();
