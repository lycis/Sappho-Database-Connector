<?php
/**
 * \class SapphoSyntaxOptimizer
 * \brief The Syntax Optimizer offers functions to optimze statements for different database systems.
 *        
 *        The SyntaxOptimizer is used to adapt statements or strings for the usage on special database systems.
 *        It may be used to test if a special string is a reserved word or to escape reserved words.
 *
 *        \warning This class is designed to be used internally so it might be a pain to use it :-)
 *
 * \author Daniel Eder
 * \version 0.1
 * \date 2011-09-22
 * \copyright GNU Public License Version 3
 */
require_once("sappho_tabstruct.php");

class SapphoSyntaxOptimizer{
?>