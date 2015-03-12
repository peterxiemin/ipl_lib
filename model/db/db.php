<?php


function insert2DB($data) {
	$keys = implode(",", array_keys($data));
	$vals = implode("','", array_values($data));
	try {
		$dbh = new PDO('mysql:host=172.30.204.122;dbname=userinfo_area', 'root', '123456');
		//foreach($dbh->query('insert * from FOO') as $row) {
		//      print_r($row);
		//}
		//$sth = $dbh->prepare("SELECT name, colour FROM fruit");
		//$sth->execute();
		//
		///* 获取结果集中所有剩余的行 */
		//print("Fetch all of the remaining rows in the result set:\n");
		//$result = $sth->fetchAll();
		$sql = "replace into userinfo_01 ($keys) values('$vals')";
		echo $sql."\n";
		$ret = $dbh->query($sql)->fetchAll();
		$dbh = null;
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}


?>
