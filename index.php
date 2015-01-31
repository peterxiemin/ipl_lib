<?php
function xm_dump($msg)
{
	var_dump($msg);
	exit(-1);
}
require('./system.php');
IS::$ipl = array();
IS::$ipl['ipl'] = array(
	'IPlMVC' => 'IPl.Framework.IPlMVC'
);
IS::loadConfig('./conf');
$mvc = IS::getInstance('IPlMVC');
$mvc->run();
?>
