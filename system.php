<?php
/*
 * 宏定义
 */
define('IPL_PATH', dirname(__FILE__));


class IS
{
	public static $ipl;

	public static function loadConfig($path, $id = '')
	{
		//if (!defined('IPL_CONF_PATH')) {
		//	define('IPL_CONF_PATH', IPL_PATH.$path);
		//}
		self::$ipl['config'] = include($path."/".self::configRoute($id));
	}

	public static function configRoute($id = '')
	{
		return 'config.ipl.'.self::getDomainRouting($id).'.php';
	}

	public static function getDomainRouting($id = '')
	{
		if (!empty($id) && in_array($id, array('server', 'debug', 'local'))) {
			return $id;
		}

		//if (strpos($_SERVER['HTTP_HOST'], 'de.') === 0) {
		//	return 'debug';
		//}

		//if (strpos($_SERVER['HTTP_HOST'], 'imcp') === 0) {
		//	return 'server';
		//}
		//else {
		//	return 'local';
		//}
		return 'local';
	}
	public static function getInstance($ns)
	{
		$classname = $ns;
		self::loadClass($classname);
		return new $classname(/* 这里看是否需要加入参数 */);
	}
	public static function getClassName($ns)
	{
		$pos = strrpos('.', $ns);
		if ($pos === false) {
			return $ns;
		}
		return substr($ns, $pos+1);
	}
	public static function loadClass($ns)
	{
		self::import($ns);
		$classname = self::getClassName($ns);
		if (class_exists($classname) === false) {
			/* 这里需要打印日志或者抛出异常提醒开发者 */
			die("can't find $classname");
		}
		return $ns;
	}
	public static function import($ns, $path = '')
	{	
		empty($path) && $path = IPL_PATH.'/lib/';
		isset(self::$ipl['ipl'][$ns]) && $ns = self::$ipl['ipl'][$ns];
		$ns = $path.str_replace('.', '/', $ns).'.php';
		if (!file_exists($ns)) {
			return false;
		}
		return require_once($ns);
	}
}

?>
