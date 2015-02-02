<?php
/* (c) Tomas Majer <tomasmajer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Notify Rabbit of successful deployment
 */
task('deploy:rabbit', function () {
    $config = get('rabbit', []);

    if (!isset($config['message'])) {
        $releasePath = env()->getReleasePath();
        $host = config()->getHost();
        $prod = get('env', 'production');
        $config['message'] = "Deployment to '{$host}' on *{$prod}* was successful\n($releasePath)";
    }

    $defaultConfig = array(
        'host' => 'localhost',
        'port' => 5672,
        'username' => 'guest',
        'password' => 'guest',
    );

    $config = array_merge($defaultConfig, $config);

    if (!is_array($config) ||
        !isset($config['channel']) ||
        !isset($config['host']) ||
        !isset($config['port']) ||
        !isset($config['username']) ||
        !isset($config['password']))
    {
        throw new \RuntimeException("<comment>Please configure rabbit config:</comment> <info>set('rabbit', array('channel' => 'channel', 'host' => 'host', 'port' => 'port', 'username' => 'username', 'password' => 'password'));</info>");
    }

    $connection = new \PhpAmqpLib\Connection\AMQPConnection($config['host'], $config['port'], $config['username'], $config['password']);
    $channel = $connection->channel();

    $msg = new \PhpAmqpLib\Message\AMQPMessage($config['message']);
    $channel->basic_publish($msg, '', $config['channel']);

    $channel->close();
    $connection->close();

})->desc('Notifying RabbitMQ channel about deployment');
