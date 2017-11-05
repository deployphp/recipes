<?php
/* (c) Matt Byers <matt@raygun.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

set('scm_identifier', function () {
    return get('application', 'Project');
});

desc('Notifying Raygun of deployment');
task('raygun:notify', function () {
    $data = [
        'apiKey'       => get('raygun_api_key'),
        'version' => get('version'),
        'repository'   => get('repository'),
        'provider'     => get('bugsnag_provider', ''),
        'branch'       => get('branch'),
        'revision'     => runLocally('git log -n 1 --format="%h"'),
        'appVersion'   => get('bugsnag_app_version', ''),
    ];

    Httpie::post('https://app.raygun.io/deployments')
        ->body($data)
        ->send();
});