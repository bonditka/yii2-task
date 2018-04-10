<?php
define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

// Composer
require(__DIR__ . '/../vendor/autoload.php');

// Environment
require(__DIR__ . '/common/env.php');

// Yii
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');