<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\LogicServices;

interface LogParserIface
{


	/**
	 * method that parses a single line into data columns
	 *
	 * @param (string) $line  the single line of the file to be parsed
	 * @return (array)  each data column as an array
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function parseLogLine($line);

	/**
	 * sets the input stream which the parser will process to read the logfile
	 *
	 * @param $openInputStream  a data input stream that has already been opened
	 * @return (string)  project data in JSON format
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function setLogInputStream($openInputStream);

	/**
	 * the primary method to parse a log file
	 *
	 * @return (boolean)  true if parsing is successful
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function parseLog();

	/**
	 * the primary method to run requested reports
	 *
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function doConfiguredReports();

	/**
	 * method returns the report storage object
	 *
	 * @return (ParserReportsIface)  the object containing report results
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function getReports();

}

