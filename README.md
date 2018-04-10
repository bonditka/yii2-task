Система задач Yii2
====================
Добавляет функционал создания и выполнения произвольной задачи. 
В качестве задачи должен быть указан класс реализующий интрфейс `\bonditka\task\components\TaskInstance`.

В контексте данного расширения задача — это многошаговая операция. Шаги задачи описываются свойством `arStep` в реализации TaskInstance. 

Задачи могут быть тегированы различными модулями, через соответствующее свойство `module`, что позволяется довольно гибко настраивать систему задач в соответствии со своими нуждами.

Установка
------------

Предпочтительный вариант установки через [composer](http://getcomposer.org/download/).

Чтобы установить, выполните следующую команду:

```
php composer.phar require --prefer-dist bonditka/yii2-task "*"
```

или добавьте

```
"bonditka/yii2-task": "*"
```

в блок require вашего `composer.json` файла.

После чего необходимо применить миграции:

```bash
php yii migrate
```

Использование
-----

Пример запуска не выполненных задач по крону:  

```php
//crontask
$tasks = Task::getTaskToRun();
foreach($tasks as $task){
	do {
		$arResult = $task->run();

		if (!is_array($arResult) && helpers\ArrayHelper::keyExists('errors', $arResult)) {
			if(is_array($arResult['errors'])){
				$error = implode('. ', $arResult['errors']);
			}
			else{
				$error = $arResult['errors'];
			}
			throw new yii\base\ErrorException('Задача выполнилась с ошибками: '.$error);
		}

		if(!helpers\ArrayHelper::keyExists('progress', $arResult) || !helpers\ArrayHelper::keyExists('nextStep', $arResult)){
			throw new yii\base\ErrorException('Задача не вернула сигнала о завершении или переходе на следующий шаг');
		}

		$step = helpers\ArrayHelper::getValue($arResult, 'nextStep');
		$task->setStep($step)->taskSave();

	} while (!empty($arResult['nextStep']));
	$task->complete()->taskSave();
}
```


```php
//TaskInstance
use yii\base as yiiBase;
use yii\helpers;

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
        'first' => [
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
```    

Тестирование
-----

Для запуска тестирования необходимо указать подключение к тестовой БД в файле `.env`. Пример файла:

```
# ---------
YII_DEBUG   = true
YII_ENV     = dev

# Databases
# ---------
TEST_DB_DSN           = mysql:host=localhost;port=3306;dbname=testdb
TEST_DB_USERNAME      = testdbu
TEST_DB_PASSWORD      = testdbpsw
TEST_DB_TABLE_PREFIX  =
```

Далее необходимо применить миграции: 

```bash
php tests/bin/yii migrate
```

После чего можно запускать тесты:

```bash
vendor/bin/codecept build
vendor/bin/codecept run
```