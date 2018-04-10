<?php
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../src/config/common.php'),
    require(__DIR__ . '/../../src/config/common-local.php'),
//    require(__DIR__ . '/../../src/config/web.php'),
//    require(__DIR__ . '/../../src/config/web-local.php'),
    require(__DIR__ . '/../../src/config/test.php'),
    require(__DIR__ . '/../../src/config/test-local.php')
);