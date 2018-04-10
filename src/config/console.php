<?php
/**
 * Application configuration shared by all applications and test types
 */

return [

    'id' => 'app-console',
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => __DIR__.'/../migrations',
            'migrationTable' => '{{%system_db_migration}}'
        ],
    ]
];