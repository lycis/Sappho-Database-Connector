<?php
	require_once('../lib/sdbc/sappho_sdbc.php');
	
	$sdbc = new SapphoDatabaseConnection(
				SapphoDatabaseConnection::db_type_mysql,
				"localhost",
				"sdbc_tutorial",
				"sdbc_tutorial"
			);
			
	if($sdbc->connect('sdbc1234'))
	  die("Could not connect to the database: ".$sdbc->lastError());
?>