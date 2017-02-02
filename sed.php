<?php
/* (c) Niklas Vosskoetter <niklas@vosskoetter.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

desc('Search and replace with sed');
task('sed:replace', function () {
    global $php_errormsg;

    $config = get('sed', []);
    $paths = $config['paths'];
    $searches = $config['searches'];
    $replacements = $config['replacements'];
    $i = 0;

    if (!is_array($config) ||
       (!isset($config['paths']) && !isset($config['searches']) && !isset($config['replacements']))
    ) {
       throw new \RuntimeException("<comment>Please configure sed:</comment> \n <info>set('sed', [</info> \n <info>  'paths' => ['/path/to/file1','/path/to/file2'],</info> \n <info>  'searches' => ['foo_file1','foo_file2'],</info> \n <info>  'replacements' => ['bar_file1','bar_file2']</info> \n <info>]);</info>");
    }

    for ($i; $i < count($paths); $i++) {
      run('sed -i "s/' . $searches[$i] . '/' . $replacements[$i] . '/g" ' . $paths[$i]);
    }

});
