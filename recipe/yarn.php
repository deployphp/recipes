<?php
/* (c) Nick DeNardis <nick.denardis@gmail.com>
 * (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

set('bin/yarn', function () {
    return run('which yarn');
});

desc('Install Yarn packages');
task('yarn:install', function () {
    if (has('previous_release')) {
        if (test('[ -d {{previous_release}}/node_modules ]')) {
            run('cp -R {{previous_release}}/node_modules {{release_path}}');

            // If package.json is unmodified, then skip running `yarn install`
            if (!run('diff {{previous_release}}/package.json {{release_path}}/package.json')) {
                return;
            }
        }
    }
    run("cd {{release_path}} && {{bin/yarn}}");
});
