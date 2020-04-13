<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace LibHelpers\LibSanitize;

interface SanitizeIface
{

	/**
	* iterate through the array and provide very basic string sanitization
	*
	* @param (array[string])  probably a $_REQUEST array, but not necessarily
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function sanitize_array_stringinput($arr);




	/**
	* apply an indicated ruleset (alphanum / numeric / alphasymb / striptags) to a string, and remove disallowed characters
	*
	 * @param (string)  the string to be sanitized
	 * @param (string)  the sanitization ruleset to apply
	* @return (string)  sanitized version of the input string
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function sanitize_str($str, $ruleset);

}

