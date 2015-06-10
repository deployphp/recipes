<?php
/* (c) HAKGER[hakger.pl] Hubert Kowalski <h.kowalski@hakger.pl> 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'recipe/common.php';

set('rsync_excludes', [
    '.git',
    'deployer_release',
    'releases',
    'deploy.php',
]);

set('rsync_user', 'server_user');

set('rsync_local_release_dir', '/tmp');

env('local_release_path', function () {
    $dir = get('rsync_local_release_dir');
    return str_replace("\n", '', runLocally("readlink $dir/deployer_release"));
});

task('deploy:local_release', function () {
    $release = date('YmdHis');
    $dir = get('rsync_local_release_dir');
    
    $releasePath = "$dir/releases/$release";

    $i = 0;
    while (is_dir(env()->parse($releasePath)) && $i < 42) {
        $releasePath .= '.' . ++$i;
    }

    runLocally("mkdir -p $releasePath");

    runLocally("cd $dir && if [ -h deployer_release ]; then rm deployer_release; fi");

    runLocally("ln -s $releasePath $dir/deployer_release");
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
  
  $server = env('server.host');
  $user = get('rsync_user');
  
  $excludes = get('rsync_excludes');
  $excludesRsync='';
  foreach($excludes as $exclude){
    $excludesRsync.=' --exclude='.escapeshellarg($exclude);
  }
  
  runLocally("rsync -rav -e 'ssh' $excludesRsync {{local_release_path}}/ $user@$server:{{release_path}}/");
  
  
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