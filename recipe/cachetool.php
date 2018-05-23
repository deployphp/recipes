<?php
/* (c) Samuel Gordalina <samuel.gordalina@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

set('cachetool', '');
set('cachetool_bin', function(){
	return run("{{bin/php}} -r \"echo version_compare(phpversion(), '7.1') == -1 ? 'cachetool-3.2.1.phar' : 'cachetool.phar';\"");
});

desc('Clearing APC system cache');
task('cachetool:clear:apc', function () {
    $releasePath = get('release_path');
    $options = get('cachetool');

    if (strlen($options)) {
        $options = "--fcgi={$options}";
    }

    cd($releasePath);
    $hasCachetool = run("if [ -e $releasePath/{{cachetool_bin}} ]; then echo 'true'; fi");

    if ('true' !== $hasCachetool) {
        run("curl -sO https://gordalina.github.io/cachetool/downloads/{{cachetool_bin}}");
    }

    run("{{bin/php}} {{cachetool_bin}} apc:cache:clear system {$options}");
});

/**
 * Clear opcache cache
 */
desc('Clearing OPcode cache');
task('cachetool:clear:opcache', function () {
    $releasePath = get('release_path');
    $options = get('cachetool');

    if (strlen($options)) {
        $options = "--fcgi={$options}";
    }

    cd($releasePath);
    $hasCachetool = run("if [ -e $releasePath/{{cachetool_bin}} ]; then echo 'true'; fi");

    if ('true' !== $hasCachetool) {
        run("curl -sO https://gordalina.github.io/cachetool/downloads/{{cachetool_bin}}");
    }

    run("{{bin/php}} {{cachetool_bin}} opcache:reset {$options}");
});
