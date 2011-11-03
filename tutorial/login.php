<?php
	require_once('connect.php');
	
	if(!isset($_POST["username"]) ||
	   !isset($_POST["password"])) die("Please provide username and password.");
	
	$options = $sdbc->queryOptions()
	                ->where('name', SapphoQueryOptions::EQUALS, $_POST["username"]);
	if($sdbc->select('login', '*', $options))
		die("Could not verify user existence: ".$sdbc->lastError());
	
	if($sdbc->rowCount() < 1)
		die("This user does not exist.");
	
	$user = $sdbc->nextData();
	if($_POST["password"] != $user["password"])
		die("No, that's not the password!");
		
	echo "Login successful.<br/>";
	$_SESSION["loggedin"] = true;
	echo "<a href='index.php'>Click here to continue</a>";
?>