<?php

return  array(
	'IPlMVC'	=> array(
		'controllerKey' 					=> 'ctrl',
		'actionKey'		 					=> 'actn',
		'defaultControllerName'	 			=> 'Default',
		'defaultActionMethodName'			=> 'Index',
		'controllerSuffix'					=> 'Controller',
		'actionMethodNamePrefix'			=> 'action',
		'controllerPath'					=> IPL_PATH.'/controller',

		'Smarty'							=> array(
			'leftDelimiter' 		=> '{{',
			'rightDelimiter'		=> '}}',
			'cacheLifetime'			=> 3600,
			'configDir'				=> 'conf',
			'cacheDir'				=> IPL_PATH.'/cache',
			'templateDir'			=> IPL_PATH.'/view',
			'compileDir'			=> IPL_PATH.'/_view',
			'tplFileExt'			=> 'html'
		)
	)
);

?>
