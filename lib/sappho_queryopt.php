<?php
/*
    This file is part of the Sappho Database Connector (SDBC) library.

    Foobar is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    SDBC is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \class SapphoQueryOptions
 * \brief This class is used to set any statement options like WHERE clauses or ORDER BY etc.
 *        
 *        This class represents any kind of options that aplly to a statement. This may range from
 *        WHERE clauses to ORDER BY columns or subselects and joins.
 *
 *
 * \author Daniel Eder
 * \version 0.1
 * \date 2011-10-11
 * \copyright GNU Public License Version 3
 */

// we need the dbc for database types
require_once('sappho_dbc.php');

// we also need our own syntax optimizer
 
class SapphoQueryOptions{

	// database type of the options
	private $db_type;
	
	// where clause
	private $where;
	
	// syntax optimizer
	private $synopt;
	
	// parent connection
	private $sdbc;
	
	// table cache
	private $tablecache;
	
	// order by clause
	private $order;
	
	// match operations
	const LOWER          = 'lo'; /**< match operation for #where() meaning `a is lower than b`*/
	const LO             = 'lo'; /**< shortcut for SapphoQueryOptions::LOWER */
	
	const LOWER_EQUALS   = 'le'; /**< match operation for #where() meaning `a is lower than or equal to b`*/
	const LEQ            = 'le'; /**< shortcut for SapphoQueryOptions::LOWER_EQUALS */
	
	const EQUALS         = 'eq'; /**< match operation for #where() meaning `a is equal to b`*/
	const EQ             = 'eq'; /**< shortcut for SapphoQueryOptions::EQUALS */
	
	const GREATER_EQUALS = 'ge'; /**< match operation for #where() meaning `a is greater than or equal to b`*/
	const GEQ            = 'ge'; /**< shortcut for SapphoQueryOptions::GREATER_EQUALS */

	const GREATER        = 'gr'; /**< match operation for #where() meaning `a is gerather than b`*/
	const GR             = 'gr'; /**< shortcut for SapphoQueryOptions::GREATER */
	
	const LIKE           = 'lk'; /**< match operation for #where() for string matching with wildcards */
	const LK             = 'lk'; /**< shortcut for SapphoQueryOptions::LIKE */
	
	const IN             = 'in'; /**< match operation for #where() to check if `a is one of these: b, c, d, e` */
	
	const NOT_EQUALS     = 'ne'; /**< match operation for #where() to check if `a is not b` */
	const NE             = 'ne'; /**< shortcut for SapphoQueryOptions::NOT_EQUALS */
	
	const NOT_IN         = 'nin'; /**< match operation for #where() to check if `a is not one of these: b, c, d, e` */
	const NIN            = 'nin'; /**< shortcut for SapphoQueryOptions::NOT_IN */
	
	// types of where parts
	const WHERE_INITIAL = 'init';
	const WHERE_AND     = 'and';
	const WHERE_OR      = 'or';
	const WHERE_SUB_AND = 'sand';
	const WHERE_SUB_OR  = 'sor';
	
	// order by sortings
	const ASC        = 'ascending';
	const ASCENDING  = 'ascending';
	const DESC       = 'descending';
	const DESCENDING = 'descending';
	
	/**
	 * \brief Creates a new instance.
	 *
	 * \param $type database type as set in SapphoDatabaseConnection
	 */
	function __construct($connection){
		$this->db_type    = $connection->getType();
		$this->where      = array();
		$this->order      = array();
		$this->sdbc       = $connection;
		$this->synopt     = &$connection->getSyntaxOptimizer();
		$this->tablecache = &$connection->getTableCache();
	}
	
	/**
	 * \brief Executed on destruction of the object.
	 *
	 * Currently does nothing.
	 */
	function __destruct(){
	}
	
	/**
	 * \brief Add a \c WHERE clause to the query.
	 *
	 * This method initiates a \c WHERE clause. It's parameters set the first condition
	 * of the clause. Any further \c WHERE parameters can be set with the #add() and
	 * #or() functions.
	 *
	 * \param $field the field that has to be tested
	 * \param $operation which condition has to match
	 * \param $value the value the field hast to match
	 *
	 * \returns modified object
	 */
	function where($field, $operation, $value)
	{
		// set initial condition
		$this->where[0] = array(self::WHERE_INITIAL, $field, $operation, $value);
		
		return $this;
	}
	
	/**
	 * \brief Add an \c AND condition to the \c WHERE clause.
	 *
	 * The use of this method extends the defined \c WHERE clause with a \c AND clause.
	 * So before calling this method you need to initialize a \c WHERE clause with #where().
	 *
	 * You may either give all three parameters to define a simple condition like
	 * <tt>AND column = 'value'</tt> or the like.
	 *
	 * If the first parameter is another SapphoQueryOptions object a new subcondition like
	 * <tt>AND (column = 'value' OR column_b = 1 ...)</tt> will be added. So this is a
	 * convinient way to bild nested \c WHERE conditions.
	 * \param $field the column that has to match
	 * \param $operation a match operation
	 * \param $value the value the column has to match
	 * 
	 * \returns modified object
	 */
	function andWhere($field, $operation='', $value='')
	{
		if(count($this->where) < 1) return false;
		
		$data = array();
		
		if(is_a($field, "SapphoQueryOptions"))
			$data = array(self::WHERE_SUB_AND, $field);
		else
			$data = array(self::WHERE_AND, $field, $operation, $value);
		array_push($this->where, $data);
		return $this;
	}
	
	/**
	 * \brief Add an \c OR condition to the \c WHERE clause.
	 *
	 * The use of this method extends the defined \c WHERE clause with a \c OR clause.
	 * So before calling this method you need to initialize a \c WHERE clause with #where().
	 *
	 * You may either give all three parameters to define a simple condition like
	 * <tt>OR column = 'value'</tt> or the like.
	 *
	 * If the first parameter is another SapphoQueryOptions object a new subcondition like
	 * <tt>OR (column = 'value' OR column_b = 1 ...)</tt> will be added. So this is a
	 * convinient way to bild nested \c WHERE conditions.
	 * 
	 * \param $field the column that has to match or another SapphoQueryOptions object
	 * \param $operation a match operation
	 * \param $value the value the column has to match
	 * 
	 * \returns modified object
	 */
	function orWhere($field, $operation='', $value='')
	{
		if(count($this->where) < 1) return false;
		
		$data = array();
		
		if(is_a($field, "SapphoQueryOptions"))
			$data = array(self::WHERE_SUB_OR, $field);
		else
			$data = array(self::WHERE_OR, $field, $operation, $value);
			
		array_push($this->where, $data);
		return $this;
	}
	
	/**
	 * \brief Construct a valid WHERE clause.
	 *
	 * By using this method the stored options are processed and a valid \c WHERE clause
	 * is built and returned. It is already correctly escaped for use with the assigned
	 * connection. This method is mainly designed to be used internally but you may call
	 * it to get the \c WHERE clause separatly.
	 *
	 * It is necessary to provide the table that the query will be executed on to do
	 * correct datatype escaping by using the tablecache of the assigned connection.
	 *
	 * \param $table the table the statement will be executed on
	 * \param $subcontition set to true if used during #getWhereClause() if used in nested conditions
	 * \returns the correctly escaped \c WHERE clause as string
	 */
	function getWhereClause($table, $subcondition=false)
	{
		$clause = "";
		if(count($this->where) < 1) return "";
		
		if(!$subcondition)
			$clause = "WHERE ";
		
		foreach($this->where as $condition)
		{
			switch($condition[0])
			{
				case self::WHERE_INITIAL: break;
				case self::WHERE_AND:     $clause .= " AND ";
                                          break;
				case self::WHERE_OR:      $clause .= " OR ";
				                           break;
				case self::WHERE_SUB_AND: $clause .= " AND (";
				                           break;
				case self::WHERE_SUB_OR:  $clause .= " OR (";
				                           break;
			}
			
			// subcondition
			if($condition[0] == self::WHERE_SUB_AND || 
			   $condition[0] == self::WHERE_SUB_OR)
			{
				$clause .= $condition[1]->getWhereClause($table, true);
				$clause .= ")";
			}
			// normal condition
			else
			{
				$clause .= $condition[1];
			
				switch($condition[2])
				{
					case self::EQUALS:
					case self::EQ:
										$clause .= " = ";
										 break;
					case self::GREATER: 
					case self::GR:
										$clause .= " > ";
										 break;
					case self::LOWER:   
					case self::LO:
										$clause .= " < ";
										 break;
					case self::LOWER_EQUALS:
					case self::LEQ:
										$clause .= " <= ";
										break;
					case self::GREATER_EQUALS:
					case self::GEQ:
										$clause .= " >= ";
										break;
					case self::LIKE:
					case self::LK:
										$clause .= " LIKE ";
										break;
					case self::IN:
										$clause .= " IN ";
										break;
					case self::NOT_EQUALS:
					case self::NE:
										$clause .= " <> ";
										break;
					case self::NOT_IN:
					case self::NIN:
										$clause .= " NOT IN ";
										break;
				}
			
				
				// if it's an IN condition there's special treatment
				if($condition[2] == self::IN || $condition[2] == self::NOT_IN)
				{
					// if the check value is no array we make it one
					if(!is_array($condition[3]))
						$condition[3] = array($condition[3]);
					
					$clause .= '(';
					for($i=0; $i<count($condition[3]); $i++)
					{
						$clause .= $this->formatField($condition[3][$i], $condition[1], $table);
						
						if($i != count($condition[3])-1)
							$clause .= ', ';
					}
					$clause .= ')';
				}
				else
					$clause .= $this->formatField($condition[3], $condition[1], $table);
			}
		}
		
		return $clause;
	}
	
	// shortcut function to avoid typing
	private function formatField($field, $dtype, $table)
	{
		return $this->synopt->formatField($this->sdbc->escape($field), 
										  $this->tablecache[$table]->getType($dtype));
	}
	
	/**
	 * \brief Get all clauses set with this options.
	 *
	 * By using this method all conditions and options defined with this object are evaluated
     * and all set clauses are generated. There is a fixed order for the generated clauses:
	 * -# \c WHERE
	 * -# \c ORDER BY
	 *
	 * It is necessary to provide the table that the query will be executed on to do
	 * correct datatype escaping by using the tablecache of the assigned connection.
	 *
	 * \param $table the table the query will be executed on
	 * \returns all conditions and clauses in a string
	 */
	function getClause($table)
	{
		$clause = "";
		
		// where
		if(count($this->where) > 0)
			$clause .= $this->getWhereClause($table);
		
		// order by
		if(count($this->order) > 0)
			$clause .= ' '.$this->getOrderByClause();
		
		return $clause;
	}
	
	/**
	 * \brief Adds a new column to an \c ORDER \c BY clause
	 *
	 * \param $column the column that indicates the order
	 * \param $how sort ascending (SapphoQueryOptions::ASC or SapphoQueryOptions::ASCENDING) or
	 *             descending (SapphoQueryOptions::DESC or SapphoQueryOptions::DESCENDING)
	 * \returns modified instance
	 */
	function orderBy($column, $how=SapphoQueryOptions::ASC)
	{
		if($how != self::ASC &&
		   $how != self::DESC) return false;
		   
		$data = array($column, $how);
		array_push($this->order, $data);
		return $this;
	}
	
	/**
	 * \brief Construct a valid ORDER BY clause.
	 *
	 * By using this method the stored options are processed and a valid \c ORDER BY clause
	 * is built and returned.
	 *
	 * \returns \c ORDER \c BY clause
	 */
	function getOrderByClause()
	{
		$clause = '';
		
		if(count($this->order) < 1) return 'empty';
		
		$clause = 'ORDER BY ';
		for($i=0; $i<count($this->order); $i++)
		{
			$clause .= $this->order[$i][0];
			$clause .= ' ';
			switch($this->order[$i][1])
			{
				case self::ASC:
				case self::ASCENDING:
					$clause .= 'ASC';
					break;
				case self::DESC:
				case self::DESCENDING:
					$clause .= 'DESC';
					break;
				default: break;
			}
			
			if($i != count($this->order)-1)
				$clause .= ', ';
		}
		return $clause;
	}
}
?>