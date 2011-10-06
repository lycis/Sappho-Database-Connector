<h3>Testmodule: Date Formatting</h3>

<table>
	<tr>
		<th>Creation of test table</th>
		<td>
			<?php
				$query = "errorasdf";
				if($dbtype == SapphoDatabaseConnection::db_type_postgre)
					$query = "CREATE TABLE date_test (  date date, \"time\" time without time zone,   \"timestamp\" timestamp without time zone, \"interval\" interval)";
				else if($dbtype == SapphoDatabaseConnection::db_type_mysql)
					$query = "CREATE TABLE date_test (  date date, time datetime, timestamp timestamp)";
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
				if($sdbc->insert('date_test', array('date' => time(),
								 'time' => time(),
								 'timestamp' => time())))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Dropping test environment</th>
		<td>
			<?php
				if($sdbc->execute("DROP TABLE date_test"))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
</table>