<?php

/**
 * Phinx recipe for Deployer
 *
 * @author    Alexey Boyko <ket4yiit@gmail.com>
 * @contributor Security-Database <info@security-database.com>
 * @copyright 2016 Alexey Boyko
 * @license   MIT https://github.com/deployphp/recipes/blob/master/LICENSE
 *
 * @link https://github.com/deployphp/recipes
 *
 * @see http://deployer.org
 * @see https://phinx.org
 */

namespace Deployer;

/**
 * Get Phinx command
 *
 * @return string Path to Phinx
 */
set(
    'phinx_path', function () {
    $isExistsCmd = 'if [ -f %s ]; then echo true; fi';

    try {
        $phinxPath = run('which phinx')->toString();
    } catch (\RuntimeException $e) {
        $phinxPath = null;
    }

    if ($phinxPath !== null) {
        return "phinx";
    } else if (run(
        sprintf(
            $isExistsCmd,
            '{{release_path}}/vendor/bin/phinx'
        )
    )->toBool()
    ) {
        return "{{release_path}}/vendor/bin/phinx";
    } else if (run(
        sprintf(
            $isExistsCmd,
            '~/.composer/vendor/bin/phinx'
        )
    )->toBool()
    ) {
        return '~/.composer/vendor/bin/phinx';
    } else {
        throw new \RuntimeException(
            'Cannot find phinx. 
            Please specify path to phinx manually'
        );
    }
}
);

/**
 * Returns options array that allowed for command
 *
 * @param array $allowedOptions List of allowed options
 *
 * @return array Array of options
 */
set('phinx_get_allowed_config', function () {
    $opts = [];
    $allowedOptions = [
        'configuration',
        'date',
        'environment',
        'target',
        'parser'
    ];

    try {
        foreach (get('phinx') as $key => $val) {
            if (in_array($key, $allowedOptions)) {
                $opts[$key] = $val;
            }
        }
    } catch (\RuntimeException $e) {
    }

    return $opts;
});

desc('Migrating database by phinx');
task('phinx:migrate', function () {

    cd('{{release_path}}');

    $phinxCmd = get('phinx_path') . " migrate";

    foreach (get('phinx_get_allowed_config') as $name => $value) {
        $phinxCmd .= " --$name $value";
    }

    $result = run($phinxCmd);
    $messages = $result->toArray();

    writeln("<comment>\t" . end($messages) . "</comment>");

    cd('{{deploy_path}}');
});

desc('Rollback database by phinx');
task('phinx:rollback', function () {

    cd('{{release_path}}');

    $phinxCmd = get('phinx_path') . " rollback";

    foreach (get('phinx_get_allowed_config') as $name => $value) {
        $phinxCmd .= " --$name $value";
    }

    $result = run($phinxCmd);
    $messages = $result->toArray();

    writeln("<comment>\t" . end($messages) . "</comment>");

    cd('{{deploy_path}}');
});

desc('Seed database by phinx');
task('phinx:seed', function () {

    cd('{{release_path}}');

    $phinxCmd = get('phinx_path') . " seed:run";

    foreach (get('phinx_get_allowed_config') as $name => $value) {
        $phinxCmd .= " --$name $value";
    }

    $result = run($phinxCmd);
    $messages = $result->toArray();

    writeln("<comment>\t" . end($messages) . "</comment>");

    cd('{{deploy_path}}');
});
