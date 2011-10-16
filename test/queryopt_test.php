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
		<th>Building crazy nested WHERE</th>
		<td>
			<?php
				$subwhere = $sdbc->queryOptions()->where('qopt_text', SapphoQueryOptions::IN, array('a', 'ab'))
				                                 ->orWhere('qopt_id', SapphoQueryOptions::EQUALS, 5);
												 
				$subsubwhere = $sdbc->queryOptions()->where('qopt_text', SapphoQueryOptions::LIKE, '%BC%')
				                                    ->andWhere('qopt_id', SapphoQueryOptions::EQUALS, 4711);
												 
				$subwhere2 = $sdbc->queryOptions()->where('qopt_id', SapphoQueryOptions::GEQ, 2)
				                                  ->orWhere('qopt_id', SapphoQueryOptions::IN, array(5, 4, 3))
												  ->andWhere($subsubwhere);
												  
				$where = $sdbc->queryOptions()->where('qopt_id', SapphoQueryOptions::EQUALS, 1)
				                              ->andWhere($subwhere)
											  ->orWhere($subwhere2);
				
				$cl = $where->getWhereClause('queryopt_test');
				if($cl != 
				   "WHERE qopt_id = 1 AND (qopt_text IN ('a', 'ab') OR qopt_id = 5) OR (qopt_id >= 2 OR qopt_id IN (5, 4, 3) AND (qopt_text LIKE '%BC%' AND qopt_id = 4711))")
					die("<font color='#ff0000'>NOK: $cl</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Building not equals WHERE</th>
		<td>
			<?php
				$where = $sdbc->queryOptions()->where('qopt_id', SapphoQueryOptions::NOT_EQUALS, 777);
				
				$cl = $where->getWhereClause('queryopt_test');
				if($cl != 
				   "WHERE qopt_id <> 777")
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
	</tr
	<tr>
		<th>Building multi column ORDER BY</th>
		<td>
			<?php
				$options = $sdbc->queryOptions();
				$options->orderBy('qopt_id', SapphoQueryOptions::ASC)
				        ->orderBy('qopt_text', SapphoQueryOptions::DESC);
				$cl = $options->getOrderByClause();
				if($cl != "ORDER BY qopt_id ASC, qopt_text DESC")
					die("<font color='#ff0000'>NOK: $cl</font>");
				echo "<font color='#00ff00'>OK</font>";
			?>
		</td>
	</tr>
	<tr>
		<th>Building mixed clause</th>
		<td>
			<?php
				$options = $sdbc->queryOptions();
				$options->orderBy('qopt_id', SapphoQueryOptions::ASC)
				        ->orderBy('qopt_text', SapphoQueryOptions::DESC)
						->where('qopt_id', SapphoQueryOptions::GEQ, 1);
				$cl = $options->getClause('queryopt_test');
				if($cl != "WHERE qopt_id >= 1 ORDER BY qopt_id ASC, qopt_text DESC")
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