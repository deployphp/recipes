<?php
/* (c) Raz <raz@eviladmin.xyz>
 * Based on Slack nofifier recipe by Anton Medvedev
 */
namespace Deployer;
use Deployer\Utility\Httpie;

// Title of project
set('telegram_title', function () {
    return get('application', 'Project');
});

// Telegram settings
    set('telegram_token', $TELEGRAM_TOKEN_HERE);
    set('telegram_chat_id', $TELEGRAM_CHATID_HERE);
    set('telegram_url', function () {
       return 'https://api.telegram.org/bot' . get('telegram_token') . '/sendmessage';
    });

// Deploy message
set('telegram_text', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
set('telegram_success_text', 'Deploy to *{{target}}* successful');


desc('Notifying Telegram');

task('telegram:notify', function () {
    if (!get('telegram_token', false)) {
        return;
    }
    
    if (!get('telegram_chat_id', false)) {
        return;
    }
    
    $telegramUrl = get('telegram_url') . '?' . http_build_query (
        Array (
            'chat_id' => get('telegram_chat_id'), 
            'text' => get('telegram_text'),
        )
    );

    Httpie::get($telegramUrl)->send();
})
    ->once()
    ->shallow()
    ->setPrivate();

  desc('Notifying Telegram about deploy finish');
  task('telegram:notify:success', function () {
      if (!get('telegram_token', false)) {
          return;
      }
      
      if (!get('telegram_chat_id', false)) {
          return;
      }
    
      $telegramUrl = get('telegram_url') . '?' . http_build_query (
          Array (
              'chat_id' => get('telegram_chat_id'),
              'text' => get('telegram_success_text'),
          )
      );
    
    Httpie::get($telegramUrl)->send();
})
    ->once()
    ->shallow()
    ->setPrivate();
