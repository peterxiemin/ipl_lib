<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/15
 * Time: 11:55
 */
abstract class WorkerProcess
{
    protected  $isrun = false;
    protected $task_data;
    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    public function beginRun()
    {
        global $conf_store;
        $memc = new MutilProcessMemcq($conf_store);
        $this->task_data = (array) json_decode($memc->Get($memc->getQueueName()));
    }

    public function realRun()
    {
        if ($this->isrun === true) {
            $this->beginRun();
            $this->Run();
            $this->afterRun();
        }
    }

    public function Run()
    {

    }

    public function afterRun()
    {
        unset($this->task_data);
    }

    public function setIsRun($isrun)
    {
        if (isset($this->isrun)) {
            $this->isrun = $isrun;
        }
        else {
            $this->isrun = false;
        }
    }
}
