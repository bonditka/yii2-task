<?php
/**
 * Application configuration shared by all applications and test types
 */

return [
    'components' => [
        'db'=>[
            'class'=>'yii\db\Connection',
            'dsn' => env('TEST_DB_DSN'),
            'username' => env('TEST_DB_USERNAME'),
            'password' => env('TEST_DB_PASSWORD'),
            'tablePrefix' => env('TEST_DB_TABLE_PREFIX'),
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],
    ]
];