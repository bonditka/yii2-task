<?php

namespace bonditka\task\components;
/**
 * Interface TaskInterface
 * Common interface to handle tasks
 * @author  bonditka
 * Date: 02.04.18
 * Time: 15:05
 */
interface TaskInterface
{
    const STATUS_WAITING = 'waiting';
    const STATUS_PROGRESS = 'progress';
    const STATUS_SUCCEED = 'succeed';
    const STATUS_FAILED = 'failed';

    /**
     * Returns tasks with given id
     *
     * @param int $taskId
     *
     * @return TaskInterface
     */
    public static function taskGet($taskId);

    /**
     * Returns array of all tasks
     * @return array
     */
    public static function getAll();

    /**
     * Creates new task object and returns it
     * @return TaskInterface
     */
    public static function createNew();

    /**
     * Deletes the task
     * @return mixed
     */
    public function taskDelete();

    /**
     * Saves the task
     * @return mixed
     */
    public function taskSave();

    /**
     * @return string
     */
    public function getDatetime();

    /**
     * @param string $datetime
     */
    public function setDatetime($datetime);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     */
    public function setStatus($status);

    /**
     * @param array $options
     */
    public function setOption(array $options);

    /**
     * Main method to run task. Execute TaskInstance steps
     * @return mixed
     */
    public function run();
}