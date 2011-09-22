<?php
/**
 * \class SapphoDatabaseConnection
 * \brief This class handles connections to different database types.
 *        
 *        This class was developed during the Sappho project (a Document Management System).
 *        It is able to manage a database connection to MySQL and postgreSQL databases
 *        without the user worrying about the correct syntax of the different statments.
 *
 */

class SapphoDatabaseConnection{
	// status can be:
	//	connected or unconnected
	private $status;
		
	// connection information
	private $db_type = '';
	private $db_host = '';
	private $db_user = '';
	private $db_name = '';
	
	// connection handle
	private $db_handle = 0;
	
	// error message
	private $error_message;
	
	// debug fields
	private $debug_level;
	private $last_query;
	
	// return codes for connect()
	const db_connect_already_connected = 1; /**< returned by #connect(...) if the connector is already connected to a database */
	const db_connect_missing_data      = 2; /**< returned by #connect(...) if not all necessary connection data is given */
	const db_connect_declined          = 3; /**< returned by #connect(...) if the connection was declined */
	const db_connect_db_notexist       = 4; /**< returned by #connect(...) if the database does not exist */
	
	// return codes for select()
	const db_select_error  = 1; /**< returned by #select(...) if an error occured */
	const db_select_nodata = 2; /**< returned by #select(...) if the statement resulted in no data */
	
	// return codes for insert()
	const db_insert_error = 1; /**< returned by #insert(...) if an error occured */
	
	// return codes for update()
	const db_update_error = 1; /**< returned by #update(...) if an error occured */
	
	// error codes for nextData()
	const db_next_nodata = false; /**< returned by #nextData() if there is no next data record */
	
	// error codes for close()
	const db_close_not_connected = 1; /**< returned by #close() if the connector was never connected */
	const db_close_not_closed    = 2; /**< returned by #close() if the closing was not successfull */
	
	// general error codes
	const db_error_wrong_dtype = 255; /**< a general return code if a wrong datatype was passed to a function */
	
	// database types
	const db_type_mysql        = 'mysql'; /**< database type MySQL */
	const db_type_postgresql   = 'postgresql'; /**< database type postgreSQL */
	
	// data of the last query
	private $lastResult;
	
	
	/**
	* \brief Constructor of the class.
	*
	*        A new database connector instance is created. The
	*        connection <strong>is not</strong> started automatically!
	*
	* \param $type the type of the database (use any db_type_*)
	* \param $host databast host
	* \param $db name of target database
	* \param $user username to access the database
	*/
	function __construct($type, $host, $db, $user)
	{
		$this->status  = 'unconnected';
		if(isset($type)) $this->db_type = $type;
		if(isset($host)) $this->db_host = $host;
		if(isset($user)) $this->db_user = $user;
		if(isset($db))   $this->db_name = $db;
	}
	
	/**
	 * \brief Deconstructor.
	 *
	 *        If the sdbc was connected to a database the connection is terminated.
	 */
	function __destruct()
	{
		if($this->status == 'connected')
			$this->close();
	}
		
	/**
	 * \brief Start connection to the database
	 *        
	 *        The connection to the given database is established.
	 *
	 * \param $password the password of the user used to connect
	 *
	 * \return <table>
	 *           <tr><td>0</td><td>connection established</td></tr>
	 *           <tr><td>#db_connect_already_connected</td><td>this connector is already connected to a database</td></tr>
	 *           <tr><td>#db_connect_missing_data</td><td>not all connection data is provided</td>
	 *           <tr><td>#db_connect_declined</td><td>the connection was declined by the remote host</td></tr>
	 *           <tr><td>#db_connect_db_notexist</td><td>the given database does not exist</td></tr>
	 *         </table>
	 */
	function connect($password)
	{
		if($this->status != 'unconnected') return self::db_connect_already_connected;
		if($this->db_type == '' ||
		   $this->db_host == '' ||
		   $this->db_user == '' ||
		   $this->db_name == ''  ) return self::db_connect_missing_data;
		   
		$this->db_handle = 0;
		if($this->typeIs(self::db_type_mysql))
			$this->db_handle = mysql_connect($this->db_host,$this->db_user,$password);
		
		if(!$this->db_handle)
		{
			$this->error_message = $this->getSQLError();
			return self::db_connect_declined;
		}
		
		$this->status = 'connected';
		 
		if(!mysql_select_db($this->db_name))
		{
			$this->error_message = $this->getSQLError();
			return self::db_connect_db_notexist;
		}
		
		return 0;
	}
	
	/**
	 * \brief Issue a select command to the database
	 *
	 *        This method selects data from a given table. You may specify
	 *        limiting clauses (mostly known as WHERE clauses) or lock the
	 *        selected data records.
	 *
	 * \param $table the table you wish to access
	 * \param $fields an array with fieldnames for multiple fields or only one string for one field (or *)
	 * \param $where a where clause
	 * \param $lock set to true if you wish to lock the selected records
	 *
	 * \return <table>
	 *           <tr><td>0</td><td>the statement was issued without any error</tr>
	 *           <tr><td>#db_select_error</td><td>an error occured</td></tr>
	 *         </table>
	 */
	function select($table, $fields = '*', $where = '', $lock=false)
	{
		//if(!is_array($fields)) return self::db_error_wrong_dtype;
		
		$query = '';
		if($this->db_type == self::db_type_mysql)
		{
			$query = 'SELECT ';
			if(is_array($fields))
			{
				for($i=0; $i < count($fields); $i++)
				{
				$f = mysql_real_escape_string($fields[$i]);
				$query .= $f;
			  
				if($i != count($fields)-1)
					$query .= ', ';
				}
			}
			else
				$query .= $fields;
			
			$query .= " FROM $table";
			
			$where = trim($where);
			if($where != '')
				$query .= " WHERE ".mysql_real_escape_string($where);
			
	    }
		$this->setLastQuery($query);
		
		$result = 0;
		if($this->typeIs(self::db_type_mysql))
		{
			$result = mysql_query($query);
		}
			
		if(!$result)
		{
			$this->error_message = $this->getSQLError();
			return self::db_select_error;
		}
		
		$this->lastResult = $result;
		return 0;
	}
	
	/**
	 * \example exec.php
	 * \brief This is how to use the exec(...) method 
	 *
	 */
	
	/**
	 * \brief Executes a statement without further checks.
	 *
	 *        The given statement is executed on the database without any further checking.
	 *        You have to make sure that the synstax is correct for the used database by yourself.
	 *        
	 * \param $stmnt the statement you want to execute
	 *
	 * \return 0 on success, 1 on failure
	 */
	function execute($stmnt)
	{
		if($this->typeIs(self::db_type_mysql))
		{
			$this->setLastQuery($stmnt);
			
			$result = 0;
			$result = mysql_query($stmnt);
			
			if(!$result)
			{
				$this->error_message = $this->getSQLError();
				return 1;
			}
			
			$this->lastResult = $result;
			return 0;
		}
	}
	
	/**
	 * \brief Insert a new record into a table.
	 *
	 * This method is used to insert new data into an existing table of the database.
	 *
	 * \param $table the table you wish to insert into
	 * \param $fields an array with the association of fieldnames (keys) to the values
	 *
	 * \returns <table>
	 *            <tr><td>0</td><td>success</td></tr>
	 *            <tr><td>#db_error_wrong_dtype</td><td>the parameter $field was no valid array</td></tr>
	 *            <tr><td>#db_insert_error</td><td>an error occured</td></tr>
	 *          </table>
	 */
	function insert($table, $fields)
	{
		if(!is_array($fields)) return self::db_error_wrong_dtype;
		
		$query = '';
		
		if($this->db_type == self::db_type_mysql)
		{
			$query = 'INSERT INTO ';
			$query .= mysql_real_escape_string($table);
			
			$query .= '(';
			$fieldnames = array_keys($fields);
			for($i=0; $i<count($fieldnames); $i++){
				$query .= $fieldnames[$i];
				
				if($i != count($fieldnames)-1)
					$query .= ', ';
			}
			
			$query .= ') VALUES(';
			$values = array_values($fields);
			for($i=0; $i<count($values); $i++){
				$query .= $values[$i];
				
				if($i != count($values)-1)
					$query .= ', ';
			}
			$query .= ")";
		}
		
		$this->setLastQuery($query);
		$result = 0;
		
		if($this->db_type = self::db_type_mysql)
			$result = mysql_query($query);
		
		if(!$result)
		{
			$this->error_message = $this->getSQLError();
			return self::db_insert_error;
		}
		
		$this->lastResult = $result;
		return 0;
	}
	
	/**
	 * \brief Update an already existing dataset.
	 *
	 *  Use this function to change already existing data in a table of the database.
	 *
	 * \param $table the table to be upated
	 * \param $data an array that associates the fields to the values
	 * \param $where a where clause to limit the updated datasets
	 *
	 * \returns <table>
	 *           <tr><td>0</td><td>the statement was issued without any error</tr>
	 *           <tr><td>#db_update_error</td><td>an error occured</td></tr>
	 *         </table>
	 */
	function update($table, $data, $where = '')
	{
		if(!is_array($data)) return self::db_update_error;
		$query = '';
		
		if($this->db_type = self::db_type_mysql)
		{
			$query .= 'UPDATE ';
			$query .= mysql_real_escape_string($table);
			
			$query .= ' SET ';
			$keys = array_keys($data);
			for($i=0; $i<count($keys); $i++)
			{
				$query .= mysql_real_escape_string($keys[$i]).' = '.
				          mysql_real_escape_string($data[$keys[$i]]);
				if($i != count($keys)-1)
					$query .= ', ';
			}
			
			$where = trim($where);
			if($where != '')
				$query .= ' WHERE '.mysql_real_escape_string($where);
		}
		
		$this->setLastQuery($query);
		$result = 0;
		
		if($this->db_type = self::db_type_mysql)
			$result = mysql_query($query);
		
		if(!$result)
		{
			$this->error_message = $this->getSQLError();
			return self::db_insert_error;
		}
		
		$this->lastResult = $result;
		return 0;
	}
	
	/**
	 * \brief Get the next data set of the last result set.
	 *
	 *  This statement returns an associated array with the table data that was queried
	 *  by the last select statement that was executed.
	 *
	 * \returns An associated array or #db_next_nodata
	 */
	function nextData(){
		if(!mysql_num_rows($this->lastResult)) return self::db_next_nodata;
		
		if($this->debug_level > 1)
			echo "SDBC: next data -> ";
		
		$data = 0;
		if($this->typeIs(self::db_type_mysql))
			$data = mysql_fetch_assoc($this->lastResult);
			
		if($this->debug_level > 1)
			print_r($data);
			
		if(!$data)
		{
			$this->lastError = $this->getSQLError();
			return self::db_next_nodata;
		}
		
		return $data;
	}
	
	// debug functions
	private function setLastQuery($query)
	{
		$this->last_query = $query;
		if($this->debug_level > 0)
			echo "SDBC: last query was '".$this->last_query."'<br/>";
	}
	
	/**
	 * \brief The error message of the last occured error.
	 *
	 * \returns Error message of the last occured error.
	 */
	function lastError()
	{
		return $this->error_message;
	}
	
	// last error in sql connection
	private function getSQLError(){
		$error = '';
		if($this->typeIs(self::db_type_mysql))
			$error = mysql_error();
		return $error;
	}
	
	/**
	 * \brief Check if the connection is of the given type.
	 *
	 * \param $compare the database type you wish to check
	 * \returns true if the database types equals the given one.
	 */
	function typeIs($compare)
	{
		if($this->db_type == $compare) return true;
		return false;
	}
	
	/**
	 * \brief Set debug level.
	 *
	 * This method sets the debug level. When debug level is set some additional information
	 * that helps finden errors in your code will be displayed. The level determines how verbose
	 * this information will be. A higher level always displayes information that is provided
	 * by lower levels.
	 *
	 * Currently these debug levels are implemented:
	 * <table>
	 *  <tr><th>level</th><th>information</th></tr>
	 *  <tr><td>0</td><td>no debug information</td></tr>
	 *  <tr><td>1</td><td>executed statements</td></tr>
	 *  <tr><td>2</td><td>data retrieved by #nextData()</td></tr>
	 * </table>
	 *
	 * \param $debug the debug level
	 */
	function setDebug($debug)
	{
		$this->debug_level = $debug;
	}
	
	/**
	 * \brief Close the database connection.
	 *
	 *  By using this function you close the SDBC connection. This means that you can not submit any
	 *  requests after this statement. You may, however, use #connect() to reestablish the database
	 *  connection.
	 *
	 * \b Mind \b this: The use of this function is \a optional. It is automatically called by
	 *  #__destruct() when the instance is destructed.
	 *
	 * \returns <table>
	 *           <tr><td>0</td><td>connection was closed</tr>
	 *           <tr><td>#db_close_not_connected</td><td>you tried to close an unconnected SDBC</td></tr>
	 *           <tr><td>#db_close_not_closed</td><td>the SDBC was not able to close the connection</td></tr>
	 *          </table>
	 */
	function close()
	{
		if($this->status != 'connected')
			return self::db_close_not_connected;
			
		if($this->typeIs(self::db_type_mysql))
			if(!mysql_close($this->db_handle))
			{
				$this->error_message = 'Connection remains unclosed: '.mysql_error();
				return self::db_close_not_closed;
			}
		
		$this->status = 'unconnected';
		return 0;
	}
	
	/**
	 * \brief The amount of rows queried by the last executed statement.
	 *
	 * \returns amount of rows held in the last result set
	 */
	function rowCount()
	{
		if(!$this->lastResult) return 0;
		if($this->typeIs(self::db_type_mysql))
			return mysql_num_rows($this->lastResult);
		return 0;
	}
	
	/**
	 * \brief The last result set.
	 *
	 * \returns the result set of the last query
	 */
	function getLastResult()
	{
		return $this->lastResult;
	}
}
?>