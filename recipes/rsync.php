<?php
/* (c) HAKGER[hakger.pl] Hubert Kowalski <h.kowalski@hakger.pl> 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'recipe/common.php';

set('rsync',[
  'exclude'=> [
    '.git',
    '*_deployer',
    'releases',
    'deploy.php',
    ],
  'exclude-file' => false,
  'include'=> [],
  'include-file' => false,
  'filter'=> [],
  'filter-file' => false,
  'filter-perdir' => false,
  'flags' => 'rz',
  'options' => ['delete'],
  'local_release_dir' => '/tmp'
]);

env('local_release_path', function () {
    $config = get('rsync');
    $dir = $config['local_release_dir'];
    return str_replace("\n", '', runLocally("readlink $dir/{{server.host}}_deployer"));
});

env('rsync_excludes', function () {
  $config = get('rsync');
  $excludes = $config['exclude'];
  $excludeFile = $config['exclude-file'];
  $excludesRsync='';
  foreach($excludes as $exclude){
    $excludesRsync.=' --exclude='.escapeshellarg($exclude);
  }
  if(!empty($excludeFile) && file_exists($excludeFile) && is_file($excludeFile) && is_readable($excludeFile)){
    $excludesRsync .= ' --exclude-from='.escapeshellarg($excludeFile);
  }
  
  return $excludesRsync;
});

env('rsync_includes', function () {
  $config = get('rsync');
  $includes = $config['include'];
  $includeFile = $config['include-file'];
  $includesRsync='';
  foreach($includes as $include){
    $includesRsync.=' --include='.escapeshellarg($include);
  }
  if(!empty($includeFile) && file_exists($includeFile) && is_file($includeFile) && is_readable($includeFile)){
    $includesRsync .= ' --include-from='.escapeshellarg($includeFile);
  }
  
  return $includesRsync;
});

env('rsync_filter', function () {
  $config = get('rsync');
  $filters = $config['filter'];
  $filterFile = $config['filter-file'];
  $filterPerDir = $config['filter-perdir'];
  $filtersRsync='';
  foreach($filters as $filter){
    $filtersRsync.=" --filter='$filter'";
  }
  if(!empty($filterFile)){
    $filtersRsync .= " --filter='merge $filterFile'";
  }
  if(!empty($filterPerDir)){
    $filtersRsync .= " --filter='dir-merge $filterFile'";
  }
  return $filtersRsync;
});

env('rsync_options', function () {
  $config = get('rsync');
  $options = $config['options'];
  $optionsRsync = '';
  foreach($options as $option){
    $optionsRsync .= "--$option";
  }
  return $optionsRsync;
});

task('deploy:local_release', function () {
    $release = date('YmdHis');
    $config = get('rsync');
    $dir = $config['local_release_dir'];
    
    $releasePath = "$dir/releases/$release";

    $i = 0;
    while (is_dir(env()->parse($releasePath)) && $i < 42) {
        $releasePath .= '.' . ++$i;
    }

    runLocally("mkdir -p $releasePath");

    runLocally("cd $dir && if [ -h {{server.host}}_deployer ]; then rm {{server.host}}_deployer; fi");

    runLocally("ln -s $releasePath $dir/{{server.host}}_deployer");
})->desc('Prepare local release');

task('deploy:update_code', function () {
    $repository = get('repository');
    $branch = env('branch');
    if (input()->hasOption('tag')) {
        $tag = input()->getOption('tag');
    }

    $at = '';
    if (!empty($tag)) {
        $at = "-b $tag";
    } else if (!empty($branch)) {
        $at = "-b $branch";
    }

    runLocally("git clone $at --depth 1 --recursive -q $repository {{local_release_path}} 2>&1");

})->desc('Updating code locally');

task('deploy:rsync', function(){
  
  $config = get('rsync');
  
  $server = \Deployer\Task\Context::get()->getServer()->getConfiguration();
  $host = $server->getHost();
  $port = $server->getPort() ? ' -p'.$server->getPort(): '';
  $user = !$server->getUser() ? '' : $server->getUser().'@';
  
  runLocally("rsync -{$config['flags']} -e 'ssh$port' {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} {{local_release_path}}/ $user$host:{{release_path}}/");
  
  
})->desc('Rsync local->remote');

task('deploy', [
    'deploy:prepare',
    'deploy:local_release',
    'deploy:release',
    'deploy:update_code',
    'deploy:rsync',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');