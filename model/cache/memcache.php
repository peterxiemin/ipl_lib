<?php

function memcache_connect($host, $port)
{
    $mem = new Memcache;
    /* 这里php的memcache驱动提供分布式功能，可以通过
       1, 重新编译memcache驱动代码
       2, 修改php.ini的memcache配置文件
       3, 使用init_set函数，是一致性哈希功能全局生效
          默认使用取模的算法
    */
    $mem->addServer('memcache_host', 11211);
}
?>
