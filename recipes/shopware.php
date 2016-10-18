<?php

set('shared_files', ['.htaccess']);
set('shared_dirs', [
    'engine/Shopware/Plugins/Community',
    'media',
    'files'
]);
set('copy_dirs', [
    'var/cache',
    'web/cache',
]);
set('create_shared_dirs', [
    'engine/Shopware/Plugins/Community/Frontend',
    'engine/Shopware/Plugins/Community/Core',
    'engine/Shopware/Plugins/Community/Backend',
    'media/archive',
    'media/image',
    'media/image/thumbnail',
    'media/music',
    'media/pdf',
    'media/unknown',
    'media/video',
    'media/temp',
    'files/documents',
    'files/downloads'
]);
set('writable_dirs', [
    'var/cache',
    'web/cache',
    'engine/Shopware/Plugins/Community',
    'recovery',
    'themes'
]);


task('deploy:writable:create_dirs', function() {
    foreach (get('writable_dirs') as $dir) {
        run("cd {{release_path}} && mkdir -p $dir");
    }
});

before('deploy:copy_dirs', 'deploy:writable:create_dirs');
set('writable_use_sudo', false);

/**
 * Installing vendors tasks.
 */
task('deploy:vendors:recovery', function () {
    $composer = env('bin/composer');
    $envVars = env('env_vars') ? 'export ' . env('env_vars') . ' &&' : '';

    run("cd {{release_path}}/recovery/common && $envVars $composer {{composer_options}}");

})->desc('Installing recovery vendors for shopware');

after('deploy:vendors', 'deploy:vendors:recovery');

task('deploy:shared:sub', function () {
    $sharedPath = "{{deploy_path}}/shared";

    foreach (get('create_shared_dirs') as $dir) {
        // Create shared dir if it does not exist.
        run("mkdir -p $sharedPath/$dir");
    }
})->desc('Creating shared subdirs');

after('deploy:shared', 'deploy:shared:sub');

task('deploy:prepare:configuration:1', function() {
    run("cd {{release_path}} && cp {{deploy_path}}/shared/default.ini ./");
    run("cd {{release_path}} && mv ./config.php.dist ./config.php && chmod 777 ./config.php");
});

task('deploy:prepare:configuration:2', function() {
    run("cd {{release_path}} && cp {{deploy_path}}/shared/default.ini ./");
    run("cd {{release_path}} && mv ./config.php.dist ./config.php && chmod 777 ./config.php");
    /**
     * Additionally for install needed:
     * ALTER TABLE `s_core_snippets` ADD `dirty` INT( 1 ) NOT NULL DEFAULT '0';
     * ALTER TABLE `s_core_shops` ADD `always_secure` INT( 1 ) NOT NULL DEFAULT '0';
     */
    upload(__DIR__ . '/_sql/install/latest.sql', '{{release_path}}/recovery/install/data/sql/install.sql');
    upload(__DIR__ . '/_sql/snippets.sql', '{{release_path}}/recovery/install/data/sql/snippets.sql');
});

task('deploy:install:shop', function() {
    run("mysql --defaults-extra-file={{deploy_path}}/shared/default.ini < {{release_path}}/recovery/install/data/sql/install.sql");

    run("cd {{release_path}} && php build/ApplyDeltas.php");
    run("cd {{release_path}} && php bin/console sw:generate:attributes");
    run("cd {{release_path}} && php bin/console sw:theme:initialize");
    run("cd {{release_path}} && php bin/console sw:firstrunwizard:disable");

    run("mysql --defaults-extra-file={{deploy_path}}/shared/default.ini < {{release_path}}/recovery/install/data/sql/snippets.sql");

    run("cd {{release_path}}/recovery/install/data && touch install.lock");
});

after('deploy:prepare:configuration:2', 'deploy:install:shop');

task('shopware:upload-community', function() {
    upload('engine/Shopware/Plugins/Community/Backend', '{{deploy_path}}/shared/engine/Shopware/Plugins/Community/Backend');
    upload('engine/Shopware/Plugins/Community/Core', '{{deploy_path}}/shared/engine/Shopware/Plugins/Community/Core');
    upload('engine/Shopware/Plugins/Community/Frontend', '{{deploy_path}}/shared/engine/Shopware/Plugins/Community/Frontend');
});

before('deploy:symlink', 'shopware:upload-community');

/**
 * Main task
 */
task('shopware:install', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:prepare:configuration:1',
    'deploy:prepare:configuration:2',
    'deploy:symlink',
    'cleanup',
])->desc('Install a complete new shopware instance');

task('shopware:deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:copy_dirs',
    'deploy:writable',
    'deploy:prepare:configuration:1',
    'deploy:symlink',
    'cleanup',
])->desc('Deploys a given shopware instance');

task('shopware:deploy:test', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:copy_dirs',
    'deploy:writable',
    'deploy:prepare:configuration:1',
])->desc('Deploys a given shopware instance, without releasing it!');
