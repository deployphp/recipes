<?php
/* (c) Samuel Gordalina <samuel.gordalina@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
set('cachetool', '');
/**
 * Clear apc cache
 */
task('cachetool:clear:apc', function () {
    $releasePath = env('release_path');
    $env = env();
    $options = $env->has('cachetool') ? $env->get('cachetool') : get('cachetool');

    if (strlen($options)) {
        $options = "--fcgi={$options}";
    }

    cd($releasePath);
    if (commandExist('cachetool')) {
        $cacheTool = 'cachetool';
    } else {
        run("curl -sO http://gordalina.github.io/cachetool/downloads/cachetool.phar");
        $cacheTool = 'php cachetool.phar';
    }

    run("{$cacheTool} apce:cache:clear system {$options}");
})->desc('Clearing APC system cache');

/**
 * Clear opcache cache
 */
task('cachetool:clear:opcache', function () {
    $releasePath = env('release_path');
    $env = env();
    $options = $env->has('cachetool') ? $env->get('cachetool') : get('cachetool');

    if (strlen($options)) {
        $options = "--fcgi={$options}";
    }

    if (commandExist('cachetool')) {
        $cacheTool = 'cachetool';
    } else {
        run("curl -sO http://gordalina.github.io/cachetool/downloads/cachetool.phar");
        $cacheTool = 'php cachetool.phar';
    }

    cd($releasePath);

    run("{$cacheTool} opcache:reset {$options}");
})->desc('Clearing OPcode cache');
