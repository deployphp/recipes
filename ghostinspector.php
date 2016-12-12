<?php

namespace Deployer;

desc('Run Ghostinspector test suite');
task('ghostinspector:run', function () {
    $config = get('ghostinspector', []);
    if (!isset($config['version'])) {
        $config['version' ] = 'v1';
    }

    if (empty($config['apikey']) || empty($config['testsuite']) || empty($config['version'])) {
        return;
    }

    if (!askConfirmation('Do you want to run the functional test suite?', true)) {
        return;
    }

    $curl = curl_init();

    $url = 'https://api.ghostinspector.com/' . $config['version'] . '/suites/' . $config['testsuite']  . '/execute/?apiKey=' . $config['apikey'];
    if (isset($config['starturl'])) {
        $url .= '&startUrl=' . $config['starturl'];
    }

    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => 'Deployer deployment recipe for Ghostinspector'
    ));

    $result = curl_exec($curl);

    if ($json = json_decode($result, true)) {
        if (isset($json['code']) && strtoupper($json['code']) == 'SUCCESS') {
            writeln('Ghostinspector suite tests all passed successfully;');
            foreach ($json['data'] as $testResult) {
                writeln('Testname: ' . $testResult['testName']);
                writeln('URLs visited: ' . count($testResult['urls']));
                writeln('Steps performed: ' . count($testResult['steps']));
                writeln('Final screenshot here: ' . $testResult['screenshot']['original']['defaultUrl']);
            }

            return true;
        }
    }

    writeln("Ghostinspector test suite failed!");

    return false;
});