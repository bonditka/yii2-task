<?php

return [
    'task01' => [
        'module' => "bookkeeping",
        'task' => '\common\commands\GeneratePurchaseFor1cCommand',
        'description' => 'Simple task',
        'datetime' => strtotime('-1 day'),
        'run_status' => 'error',
        'param' => '{"ret":"error","ter":"aaa"}',
        'create_user' => 2,
        'create_time' => 1439635619,
        'update_user' => 1,
        'update_time' => 1439635619,
    ],
];