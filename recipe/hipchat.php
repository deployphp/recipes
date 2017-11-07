<?php
/* (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

set('hipchat_color', 'green');
set('hipchat_from', '{{target}}');
set('hipchat_message', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
set('hipchat_url', 'https://api.hipchat.com/v1/rooms/message');

desc('Notifying Hipchat channel of deployment');
task('hipchat:notify', function () {
    Hipchat::notify(get('hipchat_message'), get('hipchat_color'));
});

class Hipchat
{
    /**
     * @param string $message
     * @param string $color
     * @param int    $notify
     */
    public static function notify($message, $color = 'green', $notify = 0)
    {
        $params = [
            'auth_token' => get('hipchat_token'),
            'room_id' => get('hipchat_room_id'),
            'from' => get('target'),
            'message' => $message,
            'color' => $color,
            'notify' => $notify,
            'format' => 'json',
        ];

        Httpie::get(get('hipchat_url'))
            ->query($params)
            ->send();
    }
}
