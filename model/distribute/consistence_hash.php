<?php


<?php
/*$memcache_obj = new Memcache;
$memcache_obj->connect('10.50.3.183', 1361);
$var = $memcache_obj->get('10013908');
print_r(json_decode($var,true));
exit;*/
/*
 * author : xiemin
 * date:    2015/5/4
 * file:    consistance hash
 */

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit', '2048M');
define("BENCH_NUM", 10000);
define("VIR_NUM", 32);


/* 这里要特别注意， time33这个函数这能在64bit机器上正常运行
   32 bit机器会出现问题
*/
function myHash($str) {
    // hash(i) = hash(i-1) * 33 + str[i]
    $hash = 0;
    $s    = md5($str);
    $seed = 5;
    $len  = 32;
    for ($i = 0; $i < $len; $i++) {
        // (hash << 5) + hash 相当于 hash * 33
        //$hash = sprintf("%u", $hash * 33) + ord($s{$i});
        //$hash = ($hash * 33 + ord($s{$i})) & 0x7FFFFFFF;
        $hash = ($hash << $seed) + $hash + ord($s{$i});
    }

    return $hash & 0x7FFFFFFF;
}

class ConsistentHash {
    
    // server列表
    private $_server_list = array();
    // 延迟排序，因为可能会执行多次addServer
    private $_layze_sorted = FALSE;
    private $_set = [];

    public function __construct($set = []) {
        if(!empty($set)) {
            foreach ($set as $set_name => $id) {
                $this->addServer($set_name);
            }

            $this->_set = $set;
        }
    }

    public function addServer($server) {
        for ($i = 0; $i < VIR_NUM; $i++) {
            $vir_server = $server.":$i";
            $hash = myHash($vir_server);
            $this->_layze_sorted = FALSE;
            if (!isset($this->_server_list[$hash])) {
                $this->_server_list[$hash] = $server;
            }
        }

        return $this;
    }

    public function find($key) {
        // 排序
        if (!$this->_layze_sorted) {
            ksort($this->_server_list);
            $this->_layze_sorted = TRUE;
        }

        $hash = myHash($key);
        $len  = sizeof($this->_server_list);
        if ($len == 0) {
            return FALSE;
        }

        $keys   = array_keys($this->_server_list);
        $values = array_values($this->_server_list);

        // 如果不在区间内，则返回最后一个server
        // 这里为了让它成为一个闭环
        if ($keys[$len - 1] <= $hash && $hash < $keys[0]) {
            return $values[$len - 1];
        }

        foreach ($keys as $key=>$pos) {
            $next_pos = NULL;
            if (isset($keys[$key + 1]))
            {
                $next_pos = $keys[$key + 1];
            }

            if (is_null($next_pos)) {
                return $values[$key];
            }

            // 区间判断
            if ($pos <= $hash && $hash < $next_pos) {
                return $values[$key];
            }
        }
    }
}

$consisHash = new ConsistentHash();
$consisHash->addServer("serv1")->addServer("serv2");
$ids = file('./dat/x00',FILE_IGNORE_NEW_LINES);
$rs = [];
foreach ($ids as $value) {
    $rs[$consisHash->find($value)]++;
}
print_r($rs);
?>

?>
