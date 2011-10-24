<?php
	require_once('connect.php');
?>
<html>
	<head>
		<title>SDBC Tutorial</title>
		<link rel="stylesheet" type="text/css" href="tutorial.css"/>
	</head>
	<body>
		<h1>SDBC Tutorial: News Board</h1>
		<div id="login-panel">
			<form method="POST" action="login.php">
				<table>
					<tr>
						<td>user: </td>
						<td><input type="text" name="username" /></td>
						<td>password: </td>
						<td><input type="password" name="password" /></td>
						<td><input type="submit" value="log in" /></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="newsboard">
			<h3>Latest News</h3>
		</div>
	</body>
</html>