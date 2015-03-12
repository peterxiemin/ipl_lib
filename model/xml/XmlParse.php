<?php
class myXmlDeal {
	const xmlname = 'AndroidManifest.xml';
	const attrname = 'android:name';
	const attrvalue = 'android:value';
	const nodename = 'meta-data';

	protected $xmlpath = null;
	public function __construct($xmlpath = '')
	{
		$this->xmlpath = $xmlpath;
	}

	public function __destruct()
	{
		unset($this->xmlpath);
	}

	function replaceAttr($key, $val)
	{
		if (!file_exists($this->xmlpath)) {
			return -1;
		}
		$dom=new DOMDocument('1.0');
		$dom->load($this->xmlpath."/".self::xmlname);
		$meta_datas = $dom->getElementsByTagName(self::nodename);
		$i = 0;
		foreach($meta_datas as $meta_data){
			if ($meta_datas->item($i)->getAttribute(self::attrname) == $key) {
				//var_dump($meta_datas->item($i));
				$meta_data->setAttribute(self::attrvalue, $val);
			}
			$i++;
		}
		$dom->save($this->xmlpath."/".self::xmlname);
		return 0;
	}
	
	function setXmlPath($xmlpath = '')
	{
		$this->xmlpath = $xmlpath;
	}
}
/* test */
//$xml = new myXmlParse("/tmp");
//$xml->replaceAttr('UMENG_APPKEY', 'xiemin');
//$xml->replaceAttr('UMENG_CHANNEL', 'wangyufeng');

?>
