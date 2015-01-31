<?php

return  array(
	'IPlMVC'	=> array(
		'controllerKey' 					=> 'ctrl',
		'acitionKey'	 					=> 'actn',
		'defaultControllerName'	 			=> 'index',
		'defaultActionMethodName'			=> 'default',
		'controllerSuffix'					=> 'controller',
		//'controllerPath'					=> IPL_PATH.'/view',

		'Smarty'							=> array(
			'leftDelimiter' 		=> '{{',
			'rightDelimiter'		=> '}}',
			'cacheLifetime'			=> 3600,
			'configDir'				=> 'conf',
			'cacheDir'				=> IPL_PATH.'/cache',
			'templateDir'			=> IPL_PATH.'/view',
			'compileDir'			=> IPL_PATH.'/_view'
		)
	)
);

?>
