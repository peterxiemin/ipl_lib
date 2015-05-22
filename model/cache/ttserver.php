<?php

$tt = new TokyoTyrant("localhost", 11211);
$it = $tt->getIterator();
/* 遍历所有key和val */
foreach ($it as $key    =>  $val) {
    echo $key."\t".$val."\n";
}


?>
