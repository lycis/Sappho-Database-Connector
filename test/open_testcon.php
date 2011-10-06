<table>
	<tr>
		<th>Connecting to database</th>
		<td>
			<?php
				$sdbc = new SapphoDatabaseConnection($dbtype, $dbhost, $dbname, $dbuser);
				$sdbc->setDebug(0);
				if($sdbc->connect($dbpassword) != 0)
					die("<cont color='#ff0000'>NOK: ".$sdbc->lastError()."</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
</table>