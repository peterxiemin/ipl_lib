<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/15
 * Time: 13:17
 */


class TestReadMongo extends  WorkerProcess {
    protected $start_time;
    protected $spend_time;

    public function __construct()
    {
        /* 这里设置这个类是否被执行 */
        $this->setIsRun(false);
    }
    public function __destruct()
    {

    }

    public function Run()
    {
        log_info("process is starting");
        $this->startProcess();
    }

    public function startProcess()
    {
        global $conf_store;
        $mysql = new MutilProcessMysql($conf_store);
        $mongo = new MutilProcessMongo($conf_store);
        $min = $this->task_data['min'];
        $max = $this->task_data['max'];
        log_info("min[$min], max[$max]");
        $sql = "select id from imcms.imcms_doc where id > $min and id <= $max";
//        log_info("$sql");
        $ret = $mysql->exec($sql);
        if ($ret === false) {
            log_error("mysql exec failed, sql: $sql");
            return;
        }

        $this->startTick();

        log_info("count: ".count($ret));
        foreach ($ret as $id) {
            $mongo->findOne(array('docid'   =>  $id[0]));
        }
        $this->endTick();
        if ($this->spend_time === 0) {
            log_info("Division by zero");
        }
        else {
            $qps = count($ret) / $this->spend_time;
            log_info("count: ".count($ret)." spend time: ".$this->spend_time." QPS $qps times / s");
        }
    }
    public function startTick()
    {
        $this->start_time = time();
    }
    public function endTick()
    {
        $this->spend_time = time() - $this->start_time;
    }

}