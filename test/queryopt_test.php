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
		<th>Building simple WHERE</th>
		<td>
			<?php
				$where->where('qopt_id', SapphoQueryOptions::EQUALS, 1);
				$cl = $where->getWhereClause('queryopt_test');
				if($cl != "WHERE qopt_id = 1")
					die("<font color='#ff0000'>NOK: $cl</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Building more complex WHERE</th>
		<td>
			<?php
				$where->where('qopt_id', SapphoQueryOptions::EQUALS, 1)
				      ->andWhere('qopt_id', SapphoQueryOptions::GREATER, 1)
					  ->orWhere('qopt_text', SapphoQueryOptions::EQUALS, 'abcd');
				$cl = $where->getWhereClause('queryopt_test');
				if($cl != "WHERE qopt_id = 1 AND qopt_id > 1 OR qopt_text = 'abcd'")
					die("<font color='#ff0000'>NOK: $cl</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Building nested WHERE</th>
		<td>
			<?php
				$subwhere = $sdbc->queryOptions();
				$subwhere->where('qopt_id', SapphoQueryOptions::EQUALS, 1)
				      ->andWhere('qopt_id', SapphoQueryOptions::GREATER, 1)
					  ->orWhere('qopt_text', SapphoQueryOptions::EQUALS, 'abcd');
					  
				$where = $sdbc->queryOptions();
				$where->where('qopt_id', SapphoQueryOptions::LOWER, 1)
				      ->andWhere($subwhere);
				
				$subwhere = $sdbc->queryOptions();
				$subwhere->where('qopt_id', SapphoQueryOptions::EQUALS, 2)
				         ->orWhere('qopt_id', SapphoQueryOptions::GREATER, 0);
				
				$where->orWhere($subwhere);
				
				$cl = $where->getWhereClause('queryopt_test');
				if($cl != 
				   "WHERE qopt_id < 1 AND (qopt_id = 1 AND qopt_id > 1 OR qopt_text = 'abcd') OR (qopt_id = 2 OR qopt_id > 0)")
					die("<font color='#ff0000'>NOK: $cl</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Building multi nested WHERE</th>
		<td>
			<?php
				$subsubwhere = $sdbc->queryOptions();
				$subsubwhere->where('qopt_text', SapphoQueryOptions::LIKE, 'abcd')
				            ->andWhere('qopt_id', SapphoQueryOptions::IN, array(1, 2, 3));
				
				$subwhere = $sdbc->queryOptions();
				$subwhere->where('qopt_id', SapphoQueryOptions::EQUALS, 1)
				      ->andWhere('qopt_id', SapphoQueryOptions::GREATER, 1)
					  ->orWhere('qopt_text', SapphoQueryOptions::EQUALS, 'abcd')
					  ->orWhere($subsubwhere);
					  
				$where = $sdbc->queryOptions();
				$where->where('qopt_id', SapphoQueryOptions::LOWER, 1)
				      ->andWhere($subwhere);
				
				$subwhere = $sdbc->queryOptions();
				$subwhere->where('qopt_id', SapphoQueryOptions::EQUALS, 2)
				         ->orWhere('qopt_id', SapphoQueryOptions::GREATER, 0);
				
				$where->orWhere($subwhere);
				
				$cl = $where->getWhereClause('queryopt_test');
				if($cl != 
				   "WHERE qopt_id < 1 AND (qopt_id = 1 AND qopt_id > 1 OR qopt_text = 'abcd' OR (qopt_text LIKE 'abcd' AND qopt_id IN (1, 2, 3))) OR (qopt_id = 2 OR qopt_id > 0)")
					die("<font color='#ff0000'>NOK: $cl</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Building ORDER BY</th>
		<td>
			<?php
				$options = $sdbc->queryOptions();
				$options->orderBy('qopt_id', SapphoQueryOptions::ASC);
				$cl = $options->getOrderByClause();
				if($cl != "ORDER BY qopt_id ASC")
					die("<font color='#ff0000'>NOK: $cl</font>");
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