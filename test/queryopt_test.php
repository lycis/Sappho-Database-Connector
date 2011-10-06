<h3>Testmodule: Query Options</h3>

<table>
	<tr>
		<th>Creation of test table</th>
		<td>
			<?php
				if($sdbc->execute("CREATE TABLE queryopt_test (qopt_id INT, qopt_text VARCHAR(255))"))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Inserting test data</th>
		<td>
			<?php
				if($sdbc->insert('queryopt_test', array('qopt_id' => 1,
												         'qopt_text' => 'abcd')))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Creating new query options</th>
		<td>
			<?php
				$where = $sdbc->queryOptions();
				if(!$where) die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Dropping test environment</th>
		<td>
			<?php
				if($sdbc->execute("DROP TABLE queryopt_test"))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
</table>