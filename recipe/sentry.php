<?php
/* (c) Viacheslav Ostrovskiy <chelout@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

desc('Notifying Sentry of deployment');
task(
    'deploy:sentry',
    function () {
        $defaultConfig = [
            'version' => trim(runLocally('git log -n 1 --format="%h"')),
            'refs' => [],
            'ref' => null,
            'commits' => [],
            'url' => null,
            'date_released' => date('c'),
            'sentry_server' => 'https://sentry.io',
        ];

        $config = array_merge($defaultConfig, (array) get('sentry'));

        if (
            ! is_array($config) || ! isset($config['organization'])
            || (empty($config['projects']) || ! is_array($config['projects']))
            || ! isset($config['token']) || ! isset($config['version'])
        ) {
            throw new \RuntimeException(
                <<<EXAMPLE
Required data missing. Please configure sentry: 
set(
    'sentry', 
    [
        'organization' => 'exampleorg', 
        'projects' => [
            'exampleproj', 
            'exampleproje2'
        ], 
        'token' => 'd47828...', 
    ]
);"
EXAMPLE
            );
        }

        $postData = array_filter(
            [
                'version' => $config['version'],
                'refs' => $config['refs'],
                'ref' => $config['ref'],
                'url' => $config['url'],
                'commits' => $config['commits'],
                'dateReleased' => $config['date_released'],
                'projects' => $config['projects'],
            ]
        );

        $response = Httpie::post(
            $config['sentry_server'] . '/api/0/organizations/' . $config['organization'] . '/releases/'
        )
            ->header(sprintf('Authorization: Bearer %s', $config['token']))
            ->body($postData)
            ->getJson();

        if (isset($response['detail'])) {
            throw new \RuntimeException(sprintf('Unable to create a release: %s', $response['detail']));
        }

        writeln(
            sprintf(
                'Release of version %s for projects: %s created successfully.',
                $response['version'],
                implode(', ', array_column($response['projects'], 'slug'))
            )
        );
    }
);
