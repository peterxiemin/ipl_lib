<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/16
 * Time: 17:30
 */

class LogAnalyze extends WorkerProcess {
    public function __construct()
    {
        $this->setIsRun(true);
    }

    public function __destruct()
    {

    }

    public function Run()
    {
        $remoteip = $this->task_data;
        $ssh = new MutiProcessSSH2($remoteip[0], "liuxianpeng", "8iE6dA0Zx87htQlddnjThD1q");
        $cmd = "find /data/logs/nginx/newwap/ -name \"*.gz\"";

        $filelist = $ssh->getRemoteResult($cmd);
        $data = array();
        foreach ($filelist as $file) {
            $parts = explode("/", $file);
            $date = $parts[5]."".$parts[6];
            $cmd = "zcat $file |grep 'publishid=3073' | grep -e 'uid=[0-9]' | awk -F 'uid=' '{print $2}' | awk -F'&' '{++A[$1]} END {for(a in A){print a\" : \"A[a]}}'";
            $data[$date] = $ssh->getRemoteResult($cmd);
            exit(0);
        }
    }

}