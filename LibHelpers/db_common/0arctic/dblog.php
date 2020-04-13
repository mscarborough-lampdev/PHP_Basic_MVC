<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.



class DBLog
{

	/**
	* accept a log message and output it to a designated stream
	* (default is client browser output as an HTML comment, cannot be
	* output as an RDBMS row as this class will be used as a helper for
	* classes that manage RDBMS access)
	*
	* @param (string) $strLogP  this is the log message to output
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	static function msg($strLogP)
	{

		print "<!---- ".$strLogP." -->";

	}


}




?>