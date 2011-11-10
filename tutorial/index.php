<?php
	session_start();
	require_once('connect.php');
?>
<html>
	<head>
		<title>SDBC Tutorial</title>
		<link rel="stylesheet" type="text/css" href="tutorial.css"/>
	</head>
	<body>
		<h1>SDBC Tutorial: News Board</h1>
		<?php
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"])
		{
			echo '<div id="login-panel">
					<p>Want to add some news?</p>
					<form method="POST" action="addnews.php">
						<table>
							<tr>
								<td>title:</td><td><input type="text" name="title"</td>
							</tr>
							<tr>
								<td>content:</td><td><textarea name="content"></textarea></td>
							</tr>
							<tr>
								<td><input type="submit" value="add news"/></td>
								<td><input type="reset" value="clear"/></td>
							</tr>
						</table>
					</form>
				  </div>';
		}
		else
			echo '<div id="login-panel">
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
				</div>';
		?>
		<div id="newsboard">
			<h3>Latest News</h3>
			<?php
				require_once('news.php');
			?>
		</div>
	</body>
</html>