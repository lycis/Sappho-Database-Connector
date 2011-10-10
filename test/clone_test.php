<h3>Testmodule: Clone</h3>

<table>
	<tr>
		<th>Creation of test table</th>
		<td>
			<?php
				$query = "errorasdf";
				if($dbtype == SapphoDatabaseConnection::db_type_postgre)
					$query = "CREATE TABLE clone_test (id SERIAL, value VARCHAR(255))";
				else if($dbtype == SapphoDatabaseConnection::db_type_mysql)
					$query = "CREATE TABLE date_test (  id int AUTO_INCREMENT PRIMAREY KEY, value VARCHAR(255))";
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
				if($sdbc->insert('clone_test', array('value' => 'CLONE TEST DATA')))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Cloning connection</th>
		<td>
			<?php
				$clone = $sdbc->cloneConnection();
				if(!$clone)
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Sample select</th>
		<td>
			<?php
				if($clone->select('clone_test', '*'))
					die("<font color='#ff0000'>: ".$clone->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Dropping test environment</th>
		<td>
			<?php
				if($sdbc->execute("DROP TABLE clone_test"))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
</table>