<?php


class SplitBy2Power {
	const ROUTEPOWER = 2;
	public $serverpool = array();

	public function __construct() {
	}

	public function initConfig($config) {
		$idx = 0;
		if (is_array($config)) {
			foreach ($config as $server) {
				$this->serverpool[$idx] = $server;
				$idx++;
			}
		}
		else {
			return false;
		}
	}

	public function getServByRoute($key) {
		$idx = intval(crc32($key)) % self::ROUTEPOWER;
		return $this->serverpool[$idx];
	}
}

function getRandomString($strlen = 8) {
	$randstr = '';  
	for ($i = 0; $i < $strlen; $i++)  
	{  
		$randstr .= chr(mt_rand(33, 126));  
	}  
	return $randstr;  
}

/* for test */
$servers = array(
		array('ip'	=>	'172.30.204.100', 
			'port'	=>	'27701'
		     ),
		array('ip'	=>	'172.30.204.101', 
			'port'	=>	'27701'
		     )
		);
$sbp = new SplitBy2Power();
$sbp->initConfig($servers);

$k = $v = 0;
for ($i = 0; $i < 100000; $i++) {
	$randomstr = getRandomString();
	$ret = $sbp->getServByRoute($randomstr);
	switch ($ret['ip']) {
		case '172.30.204.100':
			$k++;
			break;
		case '172.30.204.101':
			$v++;
			break;
		default:
			break;
	}
}

echo "172.30.204.100 : ".$k."\n";
echo "172.30.204.101 : ".$v."\n";


?>
