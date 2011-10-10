<h3>Testmodule: Transactions</h3>

<table>
	<tr>
		<th>Creation of test table</th>
		<td>
			<?php
				$query = "errorasdf";
				if($dbtype == SapphoDatabaseConnection::db_type_postgre)
					$query = "CREATE TABLE transaction_test (  id SERIAL, value VARCHAR(255) )";
				else if($dbtype == SapphoDatabaseConnection::db_type_mysql)
					$query = "CREATE TABLE transaction_test ( id INTEGER AUTO_INCREMENT PRIMARY KEY, value VARCHAR(255) )";
				else "please die :)";
				if($sdbc->execute($query))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Inserting test data</th>
		<td>
			<?php
				if($sdbc->insert('transaction_test', array('value' => "Transaction Test Text")))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Start transaction & rollback</th>
		<td>
			<?php
				if(!$sdbc->startTransaction())
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				if(!$sdbc->rollbackTransaction())
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Locking select</th>
		<td>
			<?php
				if($sdbc->select('transaction_test', '*', '', true))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Start transaction & commit</th>
		<td>
			<?php
				if(!$sdbc->startTransaction())
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				if(!$sdbc->commitTransaction())
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Dropping test environment</th>
		<td>
			<?php
				if($sdbc->execute("DROP TABLE transaction_test"))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
</table>