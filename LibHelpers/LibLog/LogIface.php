<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace LibHelpers\LibLog;

interface LogIface
{

	/**
	* accept a log message and output it to a designated stream
	* (default is client browser output as an HTML comment,
	* but can be file or RDBMS row)
	*
	* @param (string) $strLogP  this is the log message to output
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function msg($strLogP);



}


?>