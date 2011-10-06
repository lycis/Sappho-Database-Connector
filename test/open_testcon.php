<?php
require_once("../lib/sappho_dbc.php");
$sdbc = new SapphoDatabaseConnection(SapphoDatabaseConnection::db_type_postgre, 'localhost', 'testdb', 'sappho');
	$sdbc->setDebug(0);
	if($sdbc->connect('1234') != 0)
		die("<cont color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
	echo "<font color='#00ff00'>OK</font>";
	echo "</p>";
?>