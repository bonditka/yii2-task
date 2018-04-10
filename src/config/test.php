<?php
/**
 * Application configuration shared by all applications and test types
 */

return [
    'id' => 'testapp',
    'basePath' => __DIR__,
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'globalFixtures' => [
                '@tests/_fixtures/task.php',
            ],
        ],
    ]
];