<?php

/**
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 21:24
 */
class MutilProcessMemc
{
    public $memc;
    public $conf;
    const try_times = 3;
    const sleep_time = 5;

    public function __construct($conf)
    {
        $this->memc = new \Memcached();
        $this->conf = $conf;
        $this->memc->addserver($conf['memcache']['host'], $conf['memcache']['port']);
    }

    public function __destruct()
    {

    }

    public function Set($key, $val)
    {
        $ret = $this->memc->set($key, $val);
        if ($ret === false) {
            log_info("memcache get failed key: $key");
        }
        return 0;
    }

    public function Get($key)
    {
        $ret = $this->memc->get($key);
        if ($ret === false) {
            log_info("memcache get failed key: $key");
        }
        return $ret;
    }
//    public function close()
//    {
//        $this->memc->close();
//    }

    public function reConnect($k)
    {
        for ($i = 1; $i <= self::try_times; $i++) {
            unset($this->memc);
            \mynamespace\log_info("key: $k, sleep...");
            sleep(self::sleep_time * $i);
            $this->memc = new \mynamespace\MyMemc($this->conf);
            $ret = $this->memc->get($k);
            if ($ret !== false) {
                return $ret;
            }
        }
        return false;
    }
}