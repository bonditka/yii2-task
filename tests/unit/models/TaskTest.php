<?php
/**
 * Created by PhpStorm.
 * User: bonditka
 * Date: 05.04.2018
 * Time: 15:01
 */

namespace bonditka\task\tests\unit\models;

use Codeception\Test\Unit;

use yii\helpers;

use bonditka\task\tests as tests;
use bonditka\task\models\Task;

class TaskTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {
        $this->tester->haveFixtures([
            'task' => [
                'class' => tests\_fixtures\TaskFixture::class,
                'dataFile' => '@tests/_fixtures/data/task.php'
            ],
            'customTask' => [
                'class' => tests\unit\_fixtures\TaskFixture::class,
            ],
        ]);
    }

    public function testCreateNew()
    {
        $task = Task::createNew();
        $this->assertTrue($task instanceof Task);
    }

    public function testTaskSave()
    {
        $task = Task::createNew();

        $customTasks = $this->tester->grabFixture('customTask');

        $customTask = $customTasks['task01'];
        foreach ($customTask as $prop => $value) {
            $task->{$prop} = $value;
        }

        if(!$task->taskSave()){
            foreach ($task->getErrors() as $attributeError) {
                $arError[] = implode($attributeError);
            }
            $this->fail(implode(' ',$arError));
            $this->assertTrue(false);
        }
        $this->assertTrue($task instanceof Task && (int)$task->id > 0);
    }

    public function testGetTaskByUser()
    {
        $task = Task::find()->byUser(2)->one();
        $this->assertTrue($task instanceof Task && (int)$task->id > 0);
    }

    public function testTaskDelete()
    {
        $task = Task::find()->limit(1)->one();
        $task->taskDelete();
    }

    public function testFailed()
    {
        $task = Task::find()->limit(1)->one();
        $task->failed()->save();

        $this->assertTrue($task->run_status  === Task::STATUS_FAILED);
    }

    public function testComplete()
    {
        $task = Task::find()->limit(1)->one();
        $task->complete()->save();

        $this->assertTrue($task->run_status  === Task::STATUS_SUCCEED);
    }

    public function testTaskGet()
    {
        $task = Task::find()->select('id')->limit(1)->one();
        $taskCheck = Task::taskGet($task->id);

        $this->assertTrue($task instanceof Task && $taskCheck->id == $task->id);
    }

    public function testGetAll()
    {
        $tasks = Task::getAll();
        //test only two items
        $i=0;
        foreach ($tasks as $task){
            $i++;
            if($i>2){
                break;
            }
            $this->assertTrue($task instanceof Task && $task->id > 0);
        }
    }

    public function testRun()
    {
        try {
//            $output = new \Codeception\Lib\Console\Output([]);
            $task = Task::find()->limit(1)->one();
            do {
                $arResult = $task->run();
//                $output->debug($arResult);

                $this->assertTrue(is_array($arResult));

                if (helpers\ArrayHelper::keyExists('errors', $arResult)) {
                    $output = new \Codeception\Lib\Console\Output([]);
                    $output->debug($arResult['errors']);
                    $this->assertTrue(false);
                }

                $this->assertArrayHasKey('progress', $arResult);
                $this->assertArrayHasKey('nextStep', $arResult);

                $step = helpers\ArrayHelper::getValue($arResult, 'nextStep');
                $task->setStep($step)->taskSave();


            } while (!empty($arResult['nextStep']));
            $task->complete()->taskSave();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $output = new \Codeception\Lib\Console\Output([]);
            $output->debug($e->getMessage());
        }
    }
}
