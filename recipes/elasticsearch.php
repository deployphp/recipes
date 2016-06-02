<?php
/* (c) Malte Blaettermann <malte.blaettermann@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

set('elasticsearch', [
    'host' => null,
    'protocol' => 'https',
    'index' => 'deployments',
    'index-date-pattern' => null,
    'doc_type' => 'deployment',
    'username' => null,
    'password' => null,
    'insecure' => false,
]);

env('elasticsearch_basic_auth', function() {
    $config = get('elasticsearch');

    $basicAuth = '';
    if ($config['username'] && $config['password'] && $config['protocol'] === 'https') {
        $basicAuth = sprintf(' --user %s:%s', $config['username'], $config['password']);
    }

    return $basicAuth;
});

env('elasticsearch_index_name', function () {
    $config = get('elasticsearch');
    $indexName = $config['index'];
    if(is_string($config['index-date-pattern'])) {
        $indexName .= '-' . date($config['index-date-pattern']);
    }

    return $indexName;
});

env('elasticsearch_get_revision', function() {
    return runLocally(env('bin/git'). ' rev-parse --short HEAD');
});

env('elasticsearch_cmd', function () {
    $config = get('elasticsearch');

    if (!is_string($config['host'])) {
        throw new \RuntimeException('Please set an elasticsearch host via set(\'elasticsearch.host\', \'your.host:port\' )');
    }

    $messagePlaceHolders = [
        '{{revision}}'     => env('elasticsearch_get_revision'),
        '{{host}}'         => env('server.host'),
        '{{stage}}'        => env('stages')[0],
        '{{branch}}'       => env('branch'),

    ];


    $insecure = '';
    if ($config['insecure']) {
        $insecure = ' --insecure ';
    }

    $payload = [
        'revision' => '{{revision}}',
        'stage'    => '{{stage}}',
        'branch'   => '{{branch}}',
        'datetime' => date(DateTime::ISO8601),
    ];

    $basicQueryString = sprintf('curl %s %s-XPOST %s://%s/%s/%s/  -d "%s"',
        env('elasticsearch_basic_auth'),
        $insecure,
        $config['protocol'],
        $config['host'],
        env('elasticsearch_index_name'),
        $config['doc_type'],
        addslashes(strtr(json_encode($payload), $messagePlaceHolders))
        );

    return $basicQueryString;
});


task('elasticsearch', function() {
    $baseCmd = env('elasticsearch_cmd');

    runLocally($baseCmd);
})->desc('Report deployment to Elasticsearch');
