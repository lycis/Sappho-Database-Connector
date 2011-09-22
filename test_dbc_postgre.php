<?php
	require_once('lib/sappho_dbc.php');
	
	echo "<h1>Database Module Test: postgreSQL</h1>";
	echo "<h3>Establishing connection</h3>";
	echo "<p>";
	$db = new SapphoDatabaseConnection(SapphoDatabaseConnection::db_type_postgre, 'localhost', 'sappho_db', 'sappho');
	$db->setDebug(2);
	if($db->connect('sappho') != 0)
		die("foo ".$db->lastError());
	echo "Connection OK";
	echo "</p>";
	
	echo "<h3>simple select</h3>";
	echo "<p>";
	$select = $db->select('object', array('object_id', 'object_name'));
	if($select) die("foo - $select: ".$db->lastError());
	
	$object = $db->nextData() or die("blaaa: ".$db->lastError());
	echo "</p>";
	
	echo "<h3>select with WHERE clause</h3>";
	echo "<p>";
	$select = $db->select('object', '*', 'object_id = 4');
	if($select) die("error - $select: ".$db->lastError());
	$object = $db->nextData() or die("error - nextdata: ".$db->lastError());
	echo "</p>";
	
	echo "<h3>execute</h3>";
	echo "<p>";
	$exec = $db->execute("select * from object");
	if($exec) die("error - $exec: ".$db->lastError());
	$object = $db->nextData() or die("error: ".$db->lastError());
	echo "</p>";
	
	echo "<h3>insert</h3>";
	echo "<p>";
	$data = array("user_name" => "'daniel'",
	              "user_password" => "'".crypt("test123")."'");
	$insert = $db->insert('user', $data);
	//if($insert) die("error - $insert: ".$db->lastError());
	echo "</p>";
	
	
	echo "<h3>update</h3>";
	echo "<p>";
	$data = array("user_password" => "'".crypt("test123")."'");
	$update = $db->update('user', $data);
	if($update) die("error - $update: ".$db->lastError());
	echo "</p>";
	
	echo "<h3>select for update</h3>";
	echo "<p>";
	$db->execute("BEGIN");
	$select = $db->select('object', array('object_id', 'object_name'),'',true);
	if($select) die("foo - $select: ".$db->lastError());
	
	$object = $db->nextData() or die("blaaa: ".$db->lastError());
	$db->execute("ROLLBACK");
	echo "</p>";
	
	echo "<h3>close connection</h3>";
	echo "<p>";
	$r = $db->close();
	if($r) die("error - $r: ".$db->lastError());
	echo "connection closed.";
	echo "</p>";
?>