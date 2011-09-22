<?php
/**
 * \class SapphoSyntaxOptimizer
 * \brief The Syntax Optimizer offers functions to optimze statements for different database systems.
 *        
 *        The SyntaxOptimizer is used to adapt statements or strings for the usage on special database systems.
 *        It may be used to test if a special string is a reserved word or to escape reserved words.
 *
 *        \b Beware: This class is designed to be used internally so it might be a pain to use it :-)
 *		  \warning At the moment the lists of reserved words are incomplete!
 *
 * \author Daniel Eder
 * \version 0.1
 * \date 2011-09-22
 * \copyright GNU Public License Version 3
 */

class SapphoSyntaxOptimizer{
	private $reserved_words_mysql = array();
	private $reserved_words_postgre = array("user");
	
	// database types
	const db_type_mysql     = 'mysql'; /**< database type MySQL */
	const db_type_postgre   = 'postgresql'; /**< database type postgreSQL */
	
	private $db_type;
	
	/**
	 * \brief Constructor.
	 *
	 * \param $type the database type the optimizer has to work for
	 */
	 function __construct($type)
	 {
		$this->db_type = $type;
	 }
	
	/**
	 * \brief Escape reserved words in the string.
	 *
	 * It is checked if the given string contains words that are reserved in the used database. Such words
	 * are returned correctly quoted so that the returned string may be used in a command for that database.
	 *
	 * \param $string the string that has to bechecked
	 *
	 * \returns an escaped version of the string
	 */
	function escape_reserved_words($string)
	{
		$words = explode(" ", $string);
		
		for($i=0; $i<count($words); $i++)
		{
			$res_words = array();
			if($this->db_type == self::db_type_mysql)
				$res_words = $this->reserved_words_mysql;
			else if($this->db_type == self::db_type_postgre)
				$res_words = $this->reserved_words_postgre;
				
			if(in_array($words[$i], $res_words))
			{
				switch($this->db_type)
				{
					case self::db_type_mysql:
						$words[$i] = "`$words[$i]`";
						break;
					case self::db_type_postgre:
						$words[$i] = "\"".$words[$i]."\"";
						break;
				}
			}
		}
		
		$string = implode($words, " ");
		return $string;
	}
}