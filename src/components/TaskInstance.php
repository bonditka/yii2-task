<?php

namespace bonditka\task\components;
/**
 * Interface TaskInterface
 * Common interface to handle tasks
 * @author  bonditka
 * Date: 02.04.18
 * Time: 15:51
 */
interface TaskInstance
{
    /**
     * @return integer between 0 and 100 of progress by current task
     */
    public function getProgress();

    /**
     * @param $param
     * @return array $arResult
     */
    public function executeStep($param);
}