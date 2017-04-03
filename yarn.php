<?php
/* (c) Nick DeNardis <nick.denardis@gmail.com>
 * (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

set('bin/yarn', function () {
    return (string)run('which yarn');
});

desc('Install Yarn packages');
task('yarn:install', function () {
    $releases = get('releases_list');

    if (isset($releases[1])) {
        if (run("if [ -d {{deploy_path}}/releases/{$releases[1]}/node_modules ]; then echo 'true'; fi")->toBool()) {
            run("cp --recursive {{deploy_path}}/releases/{$releases[1]}/node_modules {{release_path}}");
        }
    }
    run("cd {{release_path}} && {{bin/yarn}}");
});

set('local/bin/yarn', function () {
  return (string)runLocally('which yarn');
});

desc('Install Yarn packages');
task('yarn:local:install', function () {
  $releases = get('local_releases_list');

  if (isset($releases[1])) {
    if (runLocally("if [ -d {{local_deploy_path}}/releases/{$releases[1]}/node_modules ]; then echo 'true'; fi")->toBool()) {
      runLocally("cp -R {{local_deploy_path}}/releases/{$releases[1]}/node_modules {{local_release_path}}");
    }
  }
  runLocally("cd {{local_release_path}} && {{local/bin/yarn}}");
});
