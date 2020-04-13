<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace LibHelpers\LibSanitize\impl;

use LibHelpers\LibLog\LogIface;
use LibHelpers\LibSanitize\SanitizeIface;

class SanitizeWebInput implements SanitizeIface
{

	private $logger;

	/**
	 * constructor for Sanitization utility
	 *
	 * @param (LogIface) $log
	 * @return (NULL)  no return value
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function __construct(
		LogIface $logger
	) {
		$this->logger = $logger;
	}


	/**
	* iterate through the array and provide very basic string sanitization
	*
	* @param (array[string])  probably a $_REQUEST array, but not necessarily
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function sanitize_array_stringinput($arr)
	{
		//TO-DO: implement sanitization

		$arrNew = array();

		foreach ($arr AS $ind => $data) {
			$arrNew[] = $this->sanitize_str($data, "alphasymb");
		}
		return $arrNew;
	}




	/**
	* apply an indicated ruleset (alphanum / numeric / alphasymb / striptags) to a string, and remove disallowed characters
	*
	 * @param (string)  the string to be sanitized
	 * @param (string)  the sanitization ruleset to apply
	* @return (string)  sanitized version of the input string
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function sanitize_str($str, $ruleset)
	{
		//TODO: implement string sanitization
		switch ($ruleset)
		{

			case "alpha":
				//TODO: implement alphabetic regex

				break;



			case "alphasymb":
					//TODO: implement alphanumeric regex with colon, forward slash, backslash, underscore, hyphen, period, and at symbol

					break;


			default:
					$this->logger->msg("Unrecognized ruleset");
		}


		return $str;
	}


}


?>
