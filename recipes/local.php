<?php
/* (c) HAKGER[hakger.pl] Hubert Kowalski <h.kowalski@hakger.pl> 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Check if we can use local git cache. by default it checks if we're using 
 * git in version at least 2.3. 
 * You can override it if You prefer shalow clones or do not use full
 *  release workflow, that allows You to take advantage of this setting
 */
env('local_git_cache', function() {
    $gitVersion = runLocally('git version');
    $regs = [];
    if (preg_match('/((\d+\.?)+)/', $gitVersion, $regs)) {
        $version = $regs[1];
    } else {
        $version = "1.0.0";
    }

    return version_compare($version, '2.3', '>=');
});

env('local_deploy_path', '/tmp/deployer');

/**
 * Return list of releases on server.
 */
env('local_releases_list', function () {
    $list = runLocally('ls {{local_deploy_path}}/releases')->toArray();

    rsort($list);

    return $list;
});

/**
 * Return release path.
 */
env('local_release_path', function () {
    return str_replace("\n", '', runLocally("readlink {{local_deploy_path}}/release"));
});

/**
 * Return current release path.
 */
env('local_current', function () {
    return runLocally("readlink {{local_deploy_path}}/current")->toString();
});

/**
 * Preparing for local deployment.
 */
task('local:prepare', function () {

    runLocally('mkdir -p {{local_deploy_path}}'); //just to make sure everything exists

    runLocally('if [ ! -d {{local_deploy_path}} ]; then echo ""; fi');

    // Create releases dir.
    runLocally("cd {{local_deploy_path}} && if [ ! -d releases ]; then mkdir releases; fi");

    // Create shared dir.
    runLocally("cd {{local_deploy_path}} && if [ ! -d shared ]; then mkdir shared; fi");
})->desc('Preparing for local deploy');

/**
 * Release
 */
task('local:release', function () {
    $release = date('YmdHis');

    $releasePath = "{{local_deploy_path}}/releases/$release";

    $i = 0;
    while (is_dir(env()->parse($releasePath)) && $i < 42) {
        $releasePath .= '.' . ++$i;
    }

    runLocally("mkdir -p $releasePath");

    runLocally("cd {{local_deploy_path}} && if [ -h release ]; then rm release; fi");

    runLocally("ln -s $releasePath {{local_deploy_path}}/release");
})->desc('Prepare local release');

/**
 * Update project code
 */
task('local:update_code', function () {
    $repository = get('repository');
    $branch = env('branch');
    $gitCache = env('local_git_cache');
    $depth = $gitCache ? '' : '--depth 1';

    if (input()->hasOption('tag')) {
        $tag = input()->getOption('tag');
    }

    $at = '';
    if (!empty($tag)) {
        $at = "-b $tag";
    } else if (!empty($branch)) {
        $at = "-b $branch";
    }

    $releases = env('local_releases_list');

    if ($gitCache && isset($releases[1])) {
        try {
            runLocally("git clone $at --recursive -q --reference {{local_deploy_path}}/releases/{$releases[1]} --dissociate $repository  {{local_release_path}} 2>&1");
        } catch (RuntimeException $exc) {
            // If {{local_deploy_path}}/releases/{$releases[1]} has a failed git clone, is empty, shallow etc, git would throw error and give up. So we're forcing it to act without reference in this situation
            runLocally("git clone $at --recursive -q $repository {{local_release_path}} 2>&1");
        }
    } else {
        // if we're using git cache this would be identical to above code in catch - full clone. If not, it would create shallow clone.
        runLocally("git clone $at $depth --recursive -q $repository {{local_release_path}} 2>&1");
    }
})->desc('Updating code');

/**
 * Check if command exist in bash.
 *
 * @param string $command
 * @return bool
 */
function commandExistLocally($command)
{
    return runLocally("if hash $command 2>/dev/null; then echo 'true'; fi")->toBool();
}

/**
 * Installing vendors tasks.
 */
task('local:vendors', function () {
    if (commandExistLocally('composer')) {
        $composer = 'composer';
    } else {
        runLocally("cd {{local_release_path}} && curl -sS https://getcomposer.org/installer | php");
        $composer = 'php composer.phar';
    }

    runLocally("cd {{local_release_path}} && {{env_vars}} $composer {{composer_options}}");
})->desc('Installing vendors locally');

/**
 * Create symlink to last release.
 */
task('local:symlink', function () {
    runLocally("cd {{local_deploy_path}} && ln -sfn {{local_release_path}} current"); // Atomic override symlink.
    runLocally("cd {{local_deploy_path}} && rm release"); // Remove release link.
})->desc('Creating symlink to local release');

/**
 * Show current release number.
 */
task('local:current', function () {
    writeln('Current local release: ' . basename(env('local_current')));
})->desc('Show current local release.');

/**
 * Cleanup old releases.
 */
task('local:cleanup', function () {
    $releases = env('local_releases_list');

    $keep = get('keep_releases');

    while ($keep > 0) {
        array_shift($releases);
        --$keep;
    }

    foreach ($releases as $release) {
        runLocally("rm -rf {{local_deploy_path}}/releases/$release");
    }

    runLocally("cd {{local_deploy_path}} && if [ -e release ]; then rm release; fi");
    runLocally("cd {{local_deploy_path}} && if [ -h release ]; then rm release; fi");
})->desc('Cleaning up old local releases');
