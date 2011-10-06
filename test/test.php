<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 5.0//EN" "http://www.w3.org/TR/html5/strict.dtd">
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/css">
		<title>SDBC: Testing Functions</title>
	</head>
	<body>
		<div id="postgrebox" align="center">
			<h1>System: postgreSQL</h1>
			<?php
				include('test_postgre.php');
			?>
		</div>
		<div id="mysqlbox" align="center">
			<h1>System: MySQL</h1>
			<?php
				include('test_mysql.php');
			?>
		</div>
	</body>
</html>