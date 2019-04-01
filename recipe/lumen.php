<?php
    /* (c) Mark Gregory <mark.gregory@gmx.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */
    /*
     * This recipe supports Lumen 5.8+
     */

    namespace Deployer;

    require_once __DIR__ . '/common.php';

// Lumen shared dirs
    set('shared_dirs', [
        'storage',
    ]);

// Lumen shared file
    set('shared_files', [
        '.env',
    ]);

// Lumen writable dirs
    set('writable_dirs', [
        'bootstrap/cache',
        'storage',
        'storage/app',
        'storage/app/public',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
    ]);

    set('lumen_version', function () {
        $result = run('cd {{release_path}} && {{bin/php}} artisan --version');

        preg_match_all('/(\d+\.?)+/', $result, $matches);

        $version = $matches[0][0] ?? 5.8;

        return $version;
    });

    /**
     * Helper tasks
     */
    desc('Disable maintenance mode');
    task('artisan:up', function () {
        $output = run('if [ -f {{deploy_path}}/current/artisan ]; then {{bin/php}} {{deploy_path}}/current/artisan up; fi');
        writeln('<info>' . $output . '</info>');
    });

    desc('Enable maintenance mode');
    task('artisan:down', function () {
        $output = run('if [ -f {{deploy_path}}/current/artisan ]; then {{bin/php}} {{deploy_path}}/current/artisan down; fi');
        writeln('<info>' . $output . '</info>');
    });

    desc('Execute artisan migrate');
    task('artisan:migrate', function () {
        run('{{bin/php}} {{release_path}}/artisan migrate --force');
    })->once();

    desc('Execute artisan migrate:fresh');
    task('artisan:migrate:fresh', function () {
        run('{{bin/php}} {{release_path}}/artisan migrate:fresh --force');
    });

    desc('Execute artisan migrate:rollback');
    task('artisan:migrate:rollback', function () {
        $output = run('{{bin/php}} {{release_path}}/artisan migrate:rollback --force');
        writeln('<info>' . $output . '</info>');
    });

    desc('Execute artisan migrate:status');
    task('artisan:migrate:status', function () {
        $output = run('{{bin/php}} {{release_path}}/artisan migrate:status');
        writeln('<info>' . $output . '</info>');
    });

    desc('Execute artisan db:seed');
    task('artisan:db:seed', function () {
        $output = run('{{bin/php}} {{release_path}}/artisan db:seed --force');
        writeln('<info>' . $output . '</info>');
    });

    desc('Execute artisan cache:clear');
    task('artisan:cache:clear', function () {
        run('{{bin/php}} {{release_path}}/artisan cache:clear');
    });

    desc('Execute artisan queue:restart');
    task('artisan:queue:restart', function () {
        run('{{bin/php}} {{release_path}}/artisan queue:restart');
    });


    /**
     * Task deploy:public_disk support the public disk.
     * To run this task automatically, please add below line to your deploy.php file
     *
     *     before('deploy:symlink', 'deploy:public_disk');
     *
     * @see https://lumen.laravel.com/docs/5.8
     */
    desc('Make symlink for public disk');
    task('deploy:public_disk', function () {
        // Remove from source.
        run('if [ -d $(echo {{release_path}}/public/storage) ]; then rm -rf {{release_path}}/public/storage; fi');

        // Create shared dir if it does not exist.
        run('mkdir -p {{deploy_path}}/shared/storage/app/public');

        // Symlink shared dir to release dir
        run('{{bin/symlink}} {{deploy_path}}/shared/storage/app/public {{release_path}}/public/storage');
    });

    /**
     * Main task
     */
    desc('Deploy your project');
    task('deploy', [
        'deploy:info',
        'deploy:prepare',
        'deploy:lock',
        'deploy:release',
        'deploy:update_code',
        'deploy:shared',
        'deploy:vendors',
        'deploy:writable',
        'deploy:symlink',
        'deploy:unlock',
        'cleanup',
    ]);

    after('deploy', 'success');