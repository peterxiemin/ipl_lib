<?php

class IPlViewFactory {
	public function create($config = array())
	{
		$c = $this->getConfig($config);
		if (!class_exists('Smarty', false)) {
			require_once(IPL_PATH.'/plugins/Smarty/libs/Smarty.class.php');
		}
		$smarty = new Smarty();
		$smarty->template_dir = $c['templateDir'];
		$smarty->compile_dir  = $c['compileDir'];
		$smarty->config_dir   = $c['configDir'];
		$smarty->cache_dir    = $c['cacheDir'];

		$smarty->debugging	 	= false;
		$smarty->compile_check  = true;
		$smarty->caching 		= 0;
		$smarty->left_delimiter  =  '{{';
		$smarty->right_delimiter =  '}}';
		//$this->registerHelper($smarty); 
		return $smarty;
	}

	//public function registerHelper($smarty)
	//{
	//	$smarty->registerHelper('help',array($this,'smartyCall');
	//}

	//public function smartyCall($param, Smarty &$smarty)
	//{
	//	if(!isset($params['helper'])){
	//		$smarty->trigger_error('helper type is required', E_ERROR);
	//	}
	//	$helper = $params['helper'];
	//	unset($params['helper']);
	//	return IL::helper($helper, $smarty, $params);
	//}

	protected function getConfig($config = array()) 
	{
		if (empty($config) || is_array($config)) {
			return IS::$ipl['config']['IPlMVC']['Smarty'];
		}
		return $config;
	}
}


abstract class IPlController
{
	private $controllername = null;
	private $actionname = null;
	private $config = array();
	protected $request = array();
	protected $view = null;

	public function __construct($request = array(), $config = array())
	{
		$this->request = $request;
		$this->config  = $config;	
	}

	public function setControllerName($cname)
	{
		$this->controllername = $cname;
	}

	public function setActionMethodName($aname)
	{
		$this->actionname = $aname;
	}

	public function setView($viewobj)
	{
		$this->view = $viewobj;
	}

	public function getView()
	{
		return $this->view;
	}
}

class IPlMVC
{
	//public $config = array (
	//	'controllerKey' 					=> 'ctrl',
	//	'acitionKey'	 					=> 'actn',
	//	'defaultControllerName'	 			=> 'index',
	//	'defaultActionMethodName'			=> 'default',
	//	'controllerSuffix'					=> 'controller',
	//	'controllerPath'					=> IPL_PATH.'/view'
	//);

	public $request = array();
	public $config  = array();
	public $view;

	public function __construct()
	{
		$this->setConfig();
	}

	protected function setConfig($config = array())
	{
		if (empty($config)) {
			if (!empty(IS::$ipl['config'][get_class($this)])) {
				$config = IS::$ipl['config'][get_class($this)];
			}
		}
		
		if (empty($config) || !is_array($config)) {
			die('set config failed');
		}
		$this->config = $config;
		$this->getDriver();
	}

	protected function getDriver()
	{
		if (empty($this->config)) {
			die("config error");
		}
		/* 这里可以做一些其他的初始话工作，暂时用不到 */
		$this->setRequest($_REQUEST);
	}

	protected function setRequest($request = array())
	{
		$this->request = $request;
	}

	public function init()
	{
		$this->setReqeust($_REQUEST);
	}

	protected function getControllerClassName($controller)
	{
		if (empty($controller) || $controller === '') {
			$className = $this->config['defaultControllerName'];
		}
		else {
			$className = $controller;
		}
		return $className.$this->config['controllerSuffix'];
	}

	protected function getActionMethodName($action) 
	{
		if (empty($action) || $action === '') {
			$actionMethodName = $this->$this->config['defaultActionMethodName'];
		}
		return $this->config['actionMethodNamePrefix'].$action;
	}	

	protected function loadClass($classname)
	{
		/* 检查这个类是否已经include了 */
		if (class_exists($classname, false)) {
			return;
		}

		$classFilePath = $this->getClassPath($classname);
		if (!empty($classFilePath)) {
			if (!is_file($classFilePath) || !is_readable($classFilePath)) {
				die("$classFilePath is not file or not readable");
			}
		}
		require_once($classFilePath);
		if (!class_exists($classname, false)) {
			die("load class failed");
		}
		return true;
	}

	protected function getClassPath($classname)
	{	
		return  $this->config['controllerPath']."/".$classname.".php";
	}
	protected function doDisPatch($controller, $action, &$param) 
	{
		$controllerClass = $this->getControllerClassName($controller);
		$actionMethodName = $this->getActionMethodName($action);
		$this->loadClass($controllerClass);
		$controllerObj = $this->newController($controllerClass);
		if (!method_exists($controllerObj, $actionMethodName)) {
			die("no $actionMethodName in $controllerClass");
		}
		/* 这里类和方法名都找到了， 下面开始渲染 */
		$controllerObj->setView($this->getView());
		$controllerObj->$actionMethodName();
		$tpl = $this->getTplPath($controller, $action);
		$this->getView()->display($tpl);
	} 

	protected function getTplPath($cname, $aname)
	{
		$ps = array_filter(explode('_',$cname),'strlen'); 
		$s = join('/',$ps);
		$f = $aname.'.'.$this->config['Smarty']['tplFileExt'];
		return $s.'/'.$f; 
	}

	protected function getView()
	{
		if (!$this->view) {
			$iplviewobj = new IPlViewFactory();
			$this->view = $iplviewobj->create( array(
				'compileDir'	=>	$this->config['Smarty']['compileDir'],
				'templateDir'	=>  $this->config['Smarty']['templateDir']
			)
		);
		}
		return $this->view;
	}

	protected function setView($viewobj)
	{
		$this->view = $viewobj;
	}

	protected function newController($classname)
	{
		/* 这里可以加入控制层权限控制 */
		$obj = new $classname($this->request, $this->config/* 这里需要设定参数 */);
		return $obj;
	}

	public function run($controller = null, $action = null)
	{
		if (!empty($controller) && !empty($action)) {
			$cname = $controller;
			$action = $action;
		}
		else {
			$cname = $this->getControllerName();
			$aname = $this->getActionName();
			/* 这里可以加入权限控制 */
		}
		$this->doDisPatch($cname, $aname, $this->request);
	}

	protected function getControllerName()
	{
		if (!empty($this->reqeust[$this->config['controllerKey']])) {
			return $this->request[$this->config['controllerKey']];
		}
		return $this->config['defaultControllerName'];
	}

	protected function getActionName()
	{
		if (!empty($this->reqeust[$this->config['actionKey']])) {
			return $this->request[$this->config['actionKey']];
		}
		return $this->config['defaultActionMethodName'];
	}
}



?>
