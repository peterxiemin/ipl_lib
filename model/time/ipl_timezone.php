<?php
/*
 *
 * date: 2015-01-15
 * author : xiemin
 * file: ipl_timezone.php
 * descript: 设置时区，否则时间函数会报告错误
 *
 */
date_default_timezone_set('Asia/Shanghai');

class CostTime
{

    private $begin_time = null;
    
    private $end_time = null;

    public function stub_costtime()
    {
        $this->begin_time = time();
    }

    public function print_costtime()
    {
        $this->end_time = time();
        $costtime = $this->end_time - $this->begin_time;
        $hour = floor($costtime / 3600);
        $min = floor(($costtime % 3600) / 60);
        $seconds = $costtime % 3600 % 60;
        echo "costtime: ".$hour."小时".$min."分".seconds."秒";
    }
}

?>
