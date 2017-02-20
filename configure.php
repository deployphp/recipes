<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

/**
 * Deploy configure
 * Make shared_dirs and configure files from templates
 */
desc('Make configure files for your stage');
task('deploy:configure', function() {

    /**
     * Parser value for template compiler
     *
     * @param array $matches
     * @return string
     */
    $parser = function($matches) {
        if (isset($matches[1])) {
            $value = get($matches[1]);
            if (is_null($value) || is_bool($value) || is_array($value)) {
                $value = var_export($value, true);
            }
        } else {
            $value = $matches[0];
        }
        return $value;
    };

    /**
     * Template compiler
     *
     * @param string $contents
     * @return string
     */
    $compiler = function ($contents) use ($parser) {
        $contents = preg_replace_callback('/\{\{\s*([\w\.]+)\s*\}\}/', $parser, $contents);
        return $contents;
    };

    $finder = new \Symfony\Component\Finder\Finder();
    $iterator = $finder
        ->files()
        ->name('*.tpl')
        ->in(__DIR__ . '/shared');
    $tmpDir = sys_get_temp_dir();

    /* @var $file \Symfony\Component\Finder\SplFileInfo */
    foreach ($iterator as $file) {
        $error = 'Can not make temporary file';
        // Make tmp file
        $tmpFile = tempnam($tmpDir, 'tmp');
        if (!empty($tmpFile)) {
            try {
                $contents = $compiler($file->getContents());
                $target = preg_replace('/\.tpl$/', '', $file->getRelativePathname());
                // Put contents and upload tmp file to server
                if (file_put_contents($tmpFile, $contents) > 0) {
                    upload($tmpFile, '{{deploy_path}}/shared/' . $target);
                    $error = false;
                } else {
                    $error = 'Put content to temporary file fail';
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            // Delete tmp file
            unlink($tmpFile);
        }

        if (empty($error)) {
            writeln(sprintf("<info>✔</info> %s", $file->getRelativePathname()));
        } else {
            writeln(sprintf("<fg=red>✘</fg=red> %s", $file->getRelativePathname()));
            writeln(sprintf("  <fg=red>ERROR:</fg=red> %s", $error));
        }
    }
});
