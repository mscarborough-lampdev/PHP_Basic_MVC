<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\LogicServices;


/**
* ParserConfigIface is an interface for describing a logfile
*
* ParserConfigIface interface outlines the functionality that
*    an implementation should contain to define both the columns
*    in a logfile and the reports that are to be generated after
*    processing the logfile
*
* @package  Example
* @author   Michael Scarborough
* @version  1.0
* @access   public
* @see      https://github.com/mscarborough-lampdev/taskmanager/blob/PARS-00002/add_readme/other_samples/basic_mvc/README.md
*/
interface ParserConfigIface {


	/**
	* This method describes a report to be generated based on a specific column
	*       in the logfile
	*
	* @param (string)  $reportName	 a name for this report
	* @param (int) $columnIndex  the column this report will be be based on
	* @param (string)  $colTitle	 a name for this column
	* @param (string)  $desiredFunc	 the type of report to generate, values
	*        may include "COUNT" and "TOP"
	* @param (string)  $filterOperatorthe operator to use in determining
	*        whether this column should be included.  Values may include
	*        "GTE" (greater than or equal to)
	* @param (string)  $filterOperand1	a value used by the filter
	*        operator
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function addConfig(
		$reportName,
		$columnIndex,
		$colTitle,
		$desiredFunc,
		$filterOperator,
		$filterOperand1
	);

	public function getNumberOfConfigs();

	public function getConfigByIndex($index);

	public function getAllConfigs();

	public function addGrouping($delimStart, $delimEnd, $delimEscapeSeq);

	public function getNumberOfGroupings();

	public function getGroupingsByIndex($index);

	public function getAllGroupings();

	public function getColumnSeparatorRegex();

	public function setColumnSeparatorRegex($regexString);

}
