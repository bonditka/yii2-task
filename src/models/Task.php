<?php

namespace bonditka\task\models;

use yii\behaviors;
use yii\db\ActiveRecord;
use yii\helpers;

use bonditka\task\Module;

use bonditka\task\components\TaskInterface;

/**
 * This is the model class for table "pi_task".
 *
 * @property int $id
 * @property string $module
 * @property string $task
 * @property string $description
 * @property string $datetime
 * @property string $run_status
 * @property string $param
 * @property int $create_user
 * @property int $create_time
 * @property int $update_user
 * @property int $update_time
 */
class Task extends ActiveRecord implements TaskInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pi_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module', 'task', 'datetime'], 'required'],
            [['run_status'], 'string'],
            [['datetime'], 'safe'],
            [['create_user', 'create_time', 'update_user', 'update_time'], 'integer'],
            [['module', 'task', 'description'], 'string', 'max' => 150],
            [['param'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $arBehaviors = [
            'timestamp' => [
                'class' => behaviors\TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
            ],
        ];
        if(YII_ENV !== 'test'){
            $arBehaviors = helpers\ArrayHelper::merge($arBehaviors, [
                'blameable' => [
                    'class' => behaviors\BlameableBehavior::class,
                    'createdByAttribute' => 'create_user',
                    'updatedByAttribute' => 'update_user',
                ],
            ]);
        }
        return $arBehaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('common', 'ID'),
            'module' => Module::t('common', 'Module'),
            'task' => Module::t('common', 'Task'),
            'description' => Module::t('common', 'Description'),
            'run_status' => Module::t('common', 'Run Status'),
            'param' => Module::t('common', 'Param'),
            'create_user' => Module::t('common', 'Create User'),
            'create_time' => Module::t('common', 'Create Time'),
            'update_user' => Module::t('common', 'Update User'),
            'update_time' => Module::t('common', 'Update Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(Module::getInstance()->getUser(), ['id' => 'create_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateUser()
    {
        return $this->hasOne(Module::getInstance()->getUser(), ['id' => 'update_user']);
    }

    /**
     * @inheritdoc
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public static function taskGet($taskId)
    {
        return self::findOne($taskId);
    }

    public static function getAll()
    {
        return self::findAll([]);
    }

    public static function createNew()
    {
        return new self();
    }

    public function taskDelete()
    {
        return $this->delete();
    }

    public function taskSave()
    {
        return $this->save();
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function getStatus()
    {
        return $this->run_status;
    }

    public function setStatus($status)
    {
        $this->run_status = $status;
        return $this;
    }

    public function setOption(array $options){
        foreach ($options as $option => $value) {
            $this->{$option} = $value;
        }
    }

    /**
     * Set task status to success
     */
    public function complete(){
        return $this->setStatus(self::STATUS_SUCCEED);
    }

    /**
     * Set task status to failed
     */
    public function failed(){
        return $this->setStatus(self::STATUS_FAILED);
    }

    public function setParamFromArray(array $params)
    {
        $this->param = helpers\Json::encode($params);
    }

    public function parseParam()
    {
        return helpers\Json::decode($this->param);
    }

    public function setStep($step){
        $param = $this->parseParam();
        $param['step'] = $step;
        $this->setParamFromArray($param);
        return $this;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(is_array($this->param)){
                $this->param = $this->setParamFromArray($this->param);
            }
            return true;
        } else {
            return false;
        }
    }

    public function getStatusVariant(){
        return [
            Task::STATUS_WAITING => Module::t('common', 'Status waiting'),
            Task::STATUS_SUCCEED => Module::t('common', 'Status succeed'),
            Task::STATUS_FAILED => Module::t('common', 'Status failed'),
            Task::STATUS_PROGRESS => Module::t('common', 'Status progress'),
        ];
    }

    /**
     * @return array|mixed
     */
    public function run()
    {
        try {
            $this->setStatus(self::STATUS_PROGRESS)->taskSave();

            $param = $this->parseParam();
            $step = helpers\ArrayHelper::getValue($param, 'step', null);

            $task = $this->task;
            $model = new $task(['step' => $step]);
            $arResult = $model->executeStep($param);

            return helpers\ArrayHelper::merge(['model' => $model], $arResult);

        } catch (\Exception $e) {
            $this->failed()->taskSave();
            return [
                'errors' => $e->getTraceAsString()
            ];
        }
    }

    public static function getTaskToRun(){
        return Task::find()->andWhere(['<=', 'datetime', time()])->outstanding()->all();
    }

}
