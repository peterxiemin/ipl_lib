<?php
if(!defined('ACCESS')) {exit('Access denied.');}
class MyXmlReader {
	public static function readUpLoadDistXML($file){
		$channelinfos = array();
		$parentinfos = array();
		$xml = simplexml_load_file($file);
		foreach ($xml as $dist) {
    //将一级节点放入channelinfos中
			$node_dist = $dist->attributes();
			$channelinfo = array();
			foreach ($node_dist as $name => $val) {
				$channelinfo[$name] = (string)$val;
			}
    //讲二级节点放入channelinfos中
			foreach ($dist as $prop) {
				$node_prop = $prop->attributes();
				$name = (string)$node_prop['name'];
				$val  = (string)$node_prop['value'];
				if ($name != 'umengChannel') {
					$parentinfos[$name] = $val;
				}
				else {
					$channelinfo[$name] = $val;
				}
			} 

    //讲googleplay多出来的部分，追加其他的节点上去
			foreach ($parentinfos as $name => $val) {
				$channelinfo[$name]= $val;
			}
			array_push($channelinfos, $channelinfo);
			$index++;
		}
		return $channelinfos;
	}
}
?>

