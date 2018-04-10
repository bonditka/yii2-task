<?php
/**
 * Created by PhpStorm.
 * User: bonditka
 * Date: 10.04.2018
 * Time: 14:16
 */

namespace bonditka\task\tests\models;

use yii\base as yiiBase;

class TaskInstance extends yiiBase\Model implements \bonditka\task\components\TaskInstance
{
    public $step;

    public function rules()
    {
        return [
            [['step'], 'required'],
            [['step'], 'string'],
        ];
    }

    /** @var array of task step */
    public $arStep = [
        'start' => [
			'methodName' => 'myFirstAwesomeMethod',
			'message' => 'myAwesomeMethod is done. Next step is second',
		],
        'second' => [
			'methodName' => 'mySecondAwesomeMethod',
			'message' => 'myAwesomeMethod is done. Next step is third',
		],
        'third' => [
			'methodName' => 'myThirdAwesomeMethod',
			'message' => 'myAwesomeMethod is done. Task is done.',
		]
    ];


    /** @param array $arStep contains the steps of the task.
     *  Every step must contain
     *      'methodName' — the name of the method of the current instance of the class that is currently running,
     *      optional* 'message' — description of actions of the current step for the user
     */

    public function setSteps(array $arStep)
    {
        $this->arStep = $arStep;
    }

    public function getSteps(){
        return $this->arStep;
    }


    /**
     * @return integer between 0 and 100 of progress by current task
     */
    public function getProgress()
    {
        $stepPosition = array_search($this->step, array_keys($this->arStep));
        return ceil((($stepPosition + 1) / count($this->arStep)) * 100);
    }

    protected function getNextStep()
    {
        $keys = array_keys($this->arStep);
        return isset($keys[array_search($this->step, $keys) + 1]) ? $keys[array_search($this->step, $keys) + 1] : null;
    }

    /**
     * @param $param
     */
    protected function myFirstAwesomeMethod($param)
    {
        //do something...
        return true;
    }

    /**
     * @param $param
     */
    protected function mySecondAwesomeMethod($param)
    {
        //do something...
        return true;
    }

    /**
     * @param $param
     */
    protected function myThirdAwesomeMethod($param)
    {
        //do something...
        return true;
    }

    /**
     * @param $param
     * @return array $arResult
     * @throws yiiBase\ErrorException
     * @throws yiiBase\InvalidConfigException
     * Основной метод пошагового выполнения задания
     */
    public function executeStep($param)
    {
        if (empty($this->arStep)) {
            throw new yiiBase\InvalidConfigException;
        }

        if (empty($this->step)) {
            reset($this->arStep);
            $this->step = key($this->arStep);
        }

        $methodResult = $this->{$this->arStep[$this->step]['methodName']}($param);

        if($methodResult === false){
            throw new yiiBase\ErrorException;
        }

        return [
            'progress' => $this->getProgress(),
            'nextStep' => $this->getNextStep(),
            'result' => $methodResult
        ];
    }
}