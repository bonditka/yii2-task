<?php

namespace bonditka\task;

use \Yii;

class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public static $messagesCategory = 'bonditka/task';

    public $user = 'user';

    /**
     * @return \yii\web\User
     */
    public function getUser()
    {
        return \Yii::$app->{$this->user};
    }

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        if (!array_key_exists(self::$messagesCategory, Yii::$app->i18n->translations)) {
            Yii::$app->i18n->translations[self::$messagesCategory.'/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => __DIR__ . '/messages',
                'fileMap' => [
                    self::$messagesCategory.'/common' => 'common.php'
                ],
            ];
        }
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Module::t(self::$messagesCategory .'/'. $category, $message, $params, $language);
    }
}
