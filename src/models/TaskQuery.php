<?php

namespace bonditka\task\models;

/**
 * This is the ActiveQuery class for [[Task]].
 *
 * @see Task
 */
class TaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Task[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Task|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $userId
     * @return $this
     */
    public function byUser($userId)
    {
        return $this->andWhere(['create_user' => $userId]);
    }

    /**
     * @return $this
     */
    public function outstanding()
    {
        return $this->andWhere(['run_status' => Task::STATUS_WAITING]);
    }
}
