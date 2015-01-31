<?php

class DefaultController extends IPlController
{
	public function actionIndex()
	{
		$this->getView()->assign('test', 'hello world');
	}
}

?>
