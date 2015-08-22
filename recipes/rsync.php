<?php
/* (c) HAKGER[hakger.pl] Hubert Kowalski <h.kowalski@hakger.pl> 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

set('rsync', [
    'exclude' => [
        '.git',
        'deploy.php',
    ],
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'rz',
    'options' => ['delete'],
    'timeout' => 60,
]);

env('rsync_src', __DIR__);
env('rsync_dest', '{{release_path}}');

env('rsync_excludes', function () {
    $config = get('rsync');
    $excludes = $config['exclude'];
    $excludeFile = $config['exclude-file'];
    $excludesRsync = '';
    foreach ($excludes as $exclude) {
        $excludesRsync.=' --exclude=' . escapeshellarg($exclude);
    }
    if (!empty($excludeFile) && file_exists($excludeFile) && is_file($excludeFile) && is_readable($excludeFile)) {
        $excludesRsync .= ' --exclude-from=' . escapeshellarg($excludeFile);
    }

    return $excludesRsync;
});

env('rsync_includes', function () {
    $config = get('rsync');
    $includes = $config['include'];
    $includeFile = $config['include-file'];
    $includesRsync = '';
    foreach ($includes as $include) {
        $includesRsync.=' --include=' . escapeshellarg($include);
    }
    if (!empty($includeFile) && file_exists($includeFile) && is_file($includeFile) && is_readable($includeFile)) {
        $includesRsync .= ' --include-from=' . escapeshellarg($includeFile);
    }

    return $includesRsync;
});

env('rsync_filter', function () {
    $config = get('rsync');
    $filters = $config['filter'];
    $filterFile = $config['filter-file'];
    $filterPerDir = $config['filter-perdir'];
    $filtersRsync = '';
    foreach ($filters as $filter) {
        $filtersRsync.=" --filter='$filter'";
    }
    if (!empty($filterFile)) {
        $filtersRsync .= " --filter='merge $filterFile'";
    }
    if (!empty($filterPerDir)) {
        $filtersRsync .= " --filter='dir-merge $filterFile'";
    }
    return $filtersRsync;
});

env('rsync_options', function () {
    $config = get('rsync');
    $options = $config['options'];
    $optionsRsync = [];
    foreach ($options as $option) {
        $optionsRsync[] = "--$option";
    }
    return implode(' ', $optionsRsync);
});

task('rsync:warmup', function() {
    $config = get('rsync');

    $releases = env('releases_list');

    if (isset($releases[1])) {
        $source = "{{deploy_path}}/releases/{$releases[1]}";
        $destination = "{{deploy_path}}/releases/{$releases[0]}";

        run("rsync -{$config['flags']} {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} $source/ $destination/");
    } else {
        writeln("<comment>No way to warmup rsync.</comment>");
    }
})->desc('Warmup remote Rsync target');

task('rsync', function() {

    $config = get('rsync');

    $src = env('rsync_src');
    while (is_callable($src)) {
        $src = $src();
    }
    $dst = env('rsync_dest');
    while (is_callable($dst)) {
        $dst = $dst();
    }

    $server = \Deployer\Task\Context::get()->getServer()->getConfiguration();
    $host = $server->getHost();
    $port = $server->getPort() ? ' -p' . $server->getPort() : '';
    $user = !$server->getUser() ? '' : $server->getUser() . '@';

    runLocally("rsync -{$config['flags']} -e 'ssh$port' {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} '$src/' '$user$host:$dst/'", $config['timeout']);
})->desc('Rsync local->remote');
