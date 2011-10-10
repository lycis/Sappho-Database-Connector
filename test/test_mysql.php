<?php
	// test setup
	require_once("../lib/sappho_dbc.php");
	$dbtype = SapphoDatabaseConnection::db_type_mysql;
	$dbhost = 'localhost';
	$dbname = 'sappho';
	$dbuser = 'sappho';
	$dbpassword = '1234';
	
	// connecting
	include('open_testcon.php');
	
	// include testcases
	include('queryopt_test.php');
	include('date_test.php');
	include('operations_test.php');
	include('transaction_test.php');
	include('clone_test.php');
?>