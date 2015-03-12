<?php
function pushToIPushWithCurl($url,array $payload) {
	$ret = curlPost($url, $payload);
	$code = $ret['code'];
	$err = $ret['err'];
	$response = $ret['data'];
	if ($err || $code >= 400) {
		$loginfo = 'ipush_error:'.json_encode($payload).' ipush server is unusable:'.$code.' curl_error:'.$err;
		comLog($loginfo, 'error');
		return false;
	} 
}

function curlPost($url, $data) {
	$ch = curl_init();   
	//该接口只支持get访问方式，不支持post方式
	//curl_setopt($ch, CURLOPT_POST, 1); 
	var_dump($data);exit(-1);
	$paramStr = http_build_query($data);
	$url = $url.$paramStr;
	comLog("url: ".$url);
	curl_setopt($ch, CURLOPT_URL, $url);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0); //超时设置5秒 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); //超时设置5秒 
	$data = curl_exec($ch); 
	$err = curl_error($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$result = array();
	$result['data'] = $data;
	$result['code'] = $code;
	$result['err'] = $err;
	return $result;   
}

?>
