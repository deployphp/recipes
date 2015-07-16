<?php

/**
 * Make shared_dirs and configure files from templates
 */
task('deploy:configure', function () {

    /**
     * Paser value for template compiler
     * 
     * @param array $matches
     * @return string
     */
    $paser = function($matches) {
        if (isset($matches[1])) {
            $value = env()->get($matches[1]);
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
    $compiler = function ($contents) use ($paser) {
        $contents = preg_replace_callback('/\{\{\s*([\w\.]+)\s*\}\}/', $paser, $contents);
        
        return $contents;
    };

    $finder   = new \Symfony\Component\Finder\Finder();
    $iterator = $finder
        ->ignoreDotFiles(false)
        ->files()
        ->name('/\.tpl$/')
        ->in(getcwd() . '/shared');

    $tmpDir = sys_get_temp_dir();
    $deployDir = env('deploy_path');

    /* @var $file \Symfony\Component\Finder\SplFileInfo */
    foreach ($iterator as $file) {
        $success = false;
        // Make tmp file
        $tmpFile = tempnam($tmpDir, 'tmp');
        if (!empty($tmpFile)) {
            try {
                $contents = $compiler($file->getContents());
                $target   = preg_replace('/\.tpl$/', '', $file->getRelativePathname());
                // Put contents and upload tmp file to server
                if (file_put_contents($tmpFile, $contents) > 0) {
                    run("mkdir -p $deployDir/shared/" . dirname($target));
                    upload($tmpFile, "$deployDir/shared/" . $target);
                    $success = true;
                }
            } catch (\Exception $e) {
                $success = false;
            }
            // Delete tmp file
            unlink($tmpFile);
        }
        if ($success) {
            writeln(sprintf("<info>✔</info> %s", $file->getRelativePathname()));
        } else {
            writeln(sprintf("<fg=red>✘</fg=red> %s", $file->getRelativePathname()));
        }
    }
})->desc('Make configure files for your stage');
