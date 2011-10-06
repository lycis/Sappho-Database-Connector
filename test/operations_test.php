<h3>Testmodule: Operations</h3>

<table>
	<tr>
		<th>Creation of test table</th>
		<td>
			<?php
				$create = "asdferror!!!";
				if($dbtype == SapphoDatabaseConnection::db_type_postgre)
					$create = "CREATE TABLE operations_test (id SERIAL PRIMARY KEY, value VARCHAR(255))";
				else if($dbtype == SapphoDatabaseConnection::db_type_mysql)
					 $create = "CREATE TABLE operations_test (id INTEGER AUTO_INCREMENT PRIMARY KEY, value VARCHAR(255))";
					 
				if($sdbc->execute($create))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Insert</th>
		<td>
			<?php
				if($sdbc->insert('operations_test', array('value' => 'testdata')))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Get last ID</th>
		<td>
			<?php
				$lastid = $sdbc->lastId();
				echo $lastid;
			?>
		</td>
	</tr>
	<tr>
		<th>Select (simple)</th>
		<td>
			<?php
				if($sdbc->select('operations_test'))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Select (single field limit)</th>
		<td>
			<?php
				if($sdbc->select('operations_test', 'value'))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Select (multi field limit)</th>
		<td>
			<?php
				if($sdbc->select('operations_test', array('id', 'value')))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Select (simple where clause)</th>
		<td>
			<?php
				if($sdbc->select('operations_test', array('id', 'value'), 'id = '.$lastid))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Count returned records</th>
		<td>
			<?php
				if($sdbc->rowCount() != 1)
					die("<font color='#ff0000'>NOK: wrong row count: ".$sdbc->rowCount()."</font>");
				echo "1";
			?>
		</td>
	</tr>
	<tr>
		<th>Accessing returned data</th>
		<td>
			<?php
				$data = $sdbc->nextData();
				if(!$data || ($data && ($data["id"] != $lastid || $data["value"] != 'testdata')))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Update (all)</th>
		<td>
			<?php
				if($sdbc->update('operations_test', array('value' => 'newdata')))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Update (with where)</th>
		<td>
			<?php
				if($sdbc->update('operations_test', array('value' => 'newdata2'), 'id = '.$lastid))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Delete (with where)</th>
		<td>
			<?php
				if($sdbc->delete('operations_test', 'id = '.$lastid))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Delete (all)</th>
		<td>
			<?php
				if($sdbc->delete('operations_test'))
					die("<font color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Dropping test environment</th>
		<td>
			<?php
				if($sdbc->execute("DROP TABLE operations_test"))
					die("<font color='#ff0000'>: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
</table>