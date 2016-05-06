<?php

/**
 * Phinx recipe for Deployer
 *
 * @author    Alexey Boyko <ket4yiit@gmail.com>
 * @copyright 2016 Alexey Boyko 
 * @license   MIT https://github.com/deployphp/recipes/blob/master/LICENSE
 *
 * @link https://github.com/deployphp/recipes
 *
 * @see http://deployer.org
 * @see https://phinx.org
 */

//Default configuration
env(
    'phinx', [
        'environment' => '',
        'date' => '',
        'configuration' => '', 
        'target' => '',
        'seed' => '',
        'parser' => ''
    ]
);
env('phinx_path', 'getPhinx'); 

/**
 * Make Phinx command from env options 
 * 
 * @param string $cmdName Name of command
 * @param array  $conf    Command options(config)
 *
 * @return string Phinx command to execute
 */
function getPhinxCmd($cmdName, $conf)
{
    $phinx = env('phinx_path');
    
    $phinxCmd = "$phinx $cmdName";

    $options = '';

    foreach ($conf as $name => $value) {
        if ($value !== '') {
            $options .= " --$name $value";
        }
    }

    $phinxCmd .= $options;

    return $phinxCmd;
}

/**
 * Get Phinx command(by default it returns phinx in your ~/.composer if it is 
 * not in $PATH)
 *
 * @return Path to Phinx
 */
function getPhinx()
{
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

/**
 * Returns options array that allowed for command
 *
 * @param array $allowedOptions List of allowed options
 *
 * @return array Array of options
 */
function getAllowedConfig($allowedOptions)
{
    $opts = [];

    foreach (env('phinx') as $key => $val) {
        if (in_array($key, $allowedOptions)) {
            $opts[$key] = $val;
        }
    }

    return $opts;
}

task(
    'phinx:migrate', function () {
        $ALLOWED_OPTIONS = [
            'configuration',
            'date',
            'environment',
            'target',
            'parser'
        ];

        $conf = getAllowedConfig($ALLOWED_OPTIONS); 

        cd('{{release_path}}');
        
        $phinxCmd = getPhinxCmd('migrate', $conf);

        run($phinxCmd);

        cd('{{deploy_path}}');
    }
);

task(
    'phinx:rollback', function () {
        $ALLOWED_OPTIONS = [
            'configuration',
            'date',
            'environment',
            'target',
            'parser'
        ];


        $conf = getAllowedConfig($ALLOWED_OPTIONS); 

        cd('{{release_path}}');

        $phinxCmd = getPhinxCmd('rollback', $conf);

        run($phinxCmd);        

        cd('{{deploy_path}}');
    }
);

task(
    'phinx:seed', function () {
        $ALLOWED_OPTIONS = [
            'configuration',
            'environment',
            'parser',
            'seed'
        ];

        $conf = getAllowedConfig($ALLOWED_OPTIONS); 

        cd('{{release_path}}');

        $phinxCmd = getPhinxCmd('seed:run', $conf);

        run($phinxCmd);

        cd('{{deploy_path}}');
    }
);

