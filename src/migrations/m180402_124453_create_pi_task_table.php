<?php

use yii\db\Migration;

class m180402_124453_create_pi_task_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('pi_task', [
            'id' => $this->primaryKey()->unsigned(),
            'module' => $this->string(50)->notNull()->defaultValue('main'),
            'task' => $this->string(150)->notNull(),
            'description' => $this->string(150)->null(),
            'datetime' => $this->integer(11)->notNull(),
            'run_status' => $this->string(50)->notNull()->defaultValue('waiting'),
            'param' => $this->text()->null(),

            'create_user' => $this->integer(11)->notNull(),
            'create_time' => $this->integer(11)->notNull(),
            'update_user' => $this->integer(11)->notNull(),
            'update_time' => $this->integer(11)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('pi_task');
    }
}