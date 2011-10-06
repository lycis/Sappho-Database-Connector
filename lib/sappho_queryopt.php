<?php
/**
 * \class SapphoQueryOptions
 * \brief This class is used to set any statement options like WHERE clauses or ORDER BY etc.
 *        
 *        This class represents any kind of options that aplly to a statement. This may range from
 *        WHERE clauses to ORDER BY columns or subselects and joins.
 *
 *        \warning Currently only simple where clauses are implemented!
 *
 * \author Daniel Eder
 * \version 0.1
 * \date 2011-10-06
 * \copyright GNU Public License Version 3
 */

// wee nedd the dbc for database types
require_once('sappho_dbc.php');
 
class SapphoQueryOptions{
	// database type of the options
	private $db_type;
	
	/**
	 * \brief Creates a new instance.
	 *
	 * \params $type database type as set in SapphoDatabaseConnection
	 */
	function __construct($dbtype){
		$this->db_type = $dbtype;
	}
	
	/**
	 * \brief Executed on destruction of the object.
	 *
	 * Currently does nothing.
	 */
	function __destruct(){
	}
	
}
?>