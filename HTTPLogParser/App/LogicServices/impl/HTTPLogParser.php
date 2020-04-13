<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\LogicServices\impl;

use App\LogicServices\LogParserIface;
use App\Models\ParserRecStorageIface;
use App\LogicServices\ParserConfigIface;
use App\Views\ParserReportsIface;

/**
* HTTPLogParser parses a logfile and generates report output
*
* HTTPLogParser accepts a configuration and uses it to parse
*     a logfile.  The configuration also determines what
*     reports will be run on the parsed logfile
*
* @package  Example
* @author   Michael Scarborough
* @version  1.0
* @access   public
* @see      https://github.com/mscarborough-lampdev/taskmanager/blob/PARS-00002/add_readme/other_samples/basic_mvc/README.md
*/
class HTTPLogParser implements LogParserIface
{
	private $GROUPINGKEY = "##SCARBM_";

	private $config = null;
	private $records;
	private $reportData;
	private $logInputStream = null;
	private $groupedText = array();
	private $currGroupIndex = 0;
	private $isParsed = false;


	/**
	* constructor for the Parser
	*
	* @param (ParserConfigIface)  $config	 Descriptor for the columns making up the
	*       the logfile format (input) and the reports that will be emitted (output)
	* @param (ParserRecStorageIface) $records  the persistence object which will
	*       store parsing results
	* @param (ParserReportsIface)  $records	 the data passing object which
	*       holds the results of the requested reports
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function __construct(
		ParserConfigIface $config,
		ParserRecStorageIface $records,
		ParserReportsIface $reports
	) {
		$this->config = $config;
		$this->records = $records;
		$this->reportData = $reports;
	}


	/**
	* function gathers project data and outputs in JSON format
	*
	* @param $openInputStream  a data input stream that has already been opened
	* @return (string)  project data in JSON format
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function setLogInputStream($openInputStream)
	{
		$this->logInputStream = $openInputStream;
	}



	/**
	 * the primary method to parse a log file
	 *
	 * @return (boolean)  true if parsing is successful
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function parseLog() {
		if (null == $this->config OR null == $this->logInputStream) {
			throw new \Exception("Missing Resource Exception when".
			                     " trying to parse logs");
		} else {
			$hSource = $this->logInputStream;
			while (($line = fgets($hSource)) !== false) {
				$arrColumns = $this->parseLogLine($line);
				$this->records->addDataRow($arrColumns);
			}
			fclose($hSource);
		}

		$this->isParsed = true;
		return true;
	}


	/**
	 * method that parses a single line into data columns
	 *
	 * @param (string) $line  the single line of the file to be parsed
	 * @return (array)  each data column as an array
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function parseLogLine($line) {
		unset($this->groupedText);
		$this->groupedText = array();

		//Find data that is grouped together within delimiters such as
		//  quotation marks or square brackets
		//That data will be temporarily replaced to maintain its integrity
		//  during the following string split operation
		$newLine = $this->doDelimitedGroupings($line);
		$newLine = trim($newLine);

		//Retrieve the configured column separator.  Default is whitespace
		$regex = $this->config->getColumnSeparatorRegex();

		//Break line into pieces using column separator
		$columnsSubst = preg_split($regex, $newLine);

		//Restore the data that was earlier grouped by delimiters such as
		//  quotation marks or square brackets
		$columns = $this->removeDelimiterSubstitutions($columnsSubst);

		return $columns;
	}


	/**
	 * method that identifies multiple word blocks as a single data column and
	 *      replaces them with a Key value
	 *
	 * @param (string) $line  the single line of the file to be parsed
	 * @return (string)  the single line with grouped word blocks replaced by a
	 *         key value
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function doDelimitedGroupings($line) {
		//retrieve the delimiter configurations
		$groupings = $this->config->getAllGroupings();

		$newLine = $line;
		foreach ($groupings AS $ind => $data) {
			$start = $data["start"];
			$end = $data["end"];
			$escapeSeq = $data["esc"];
			$newLine = $this->groupByDelimiter($newLine, $start, $end, $escapeSeq);
		}
		return $newLine;
	}


	/**
	 * the primary method to run requested reports
	 *
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function doConfiguredReports() {
		$json = "";

		if (!$this->isParsed) {
			$json .= '{"Error": "No data parsed yet."}';
		} else {
			$requests = $this->config->getAllConfigs();

//			print "<br>Number of requests =".sizeof($requests);

			foreach ($requests AS $ind => $req) {
				$this->doReport($req);
			}
		}

	}


	/**
	 * method returns the report storage object
	 *
	 * @return (ParserReportsIface)  the object containing report results
	 * @author     Mike Scarborough
	 * @version    1.0
	 */
	public function getReports()
	{
		return $this->reportData;
	}

	private function groupByDelimiter($originalLine, $start, $end, $escapeSeq) {

		$regex = '@[\\'.$start.'].*?[^\\'.$escapeSeq.'][\\'.$end.']@';
		$matches = array();

		$parseString = $originalLine."";

		//find all data in the line tht matches the specified delimiting
		preg_match_all($regex , $parseString, $matches,PREG_OFFSET_CAPTURE);
		$newLine = $originalLine;
		$offsetCorrection = 0;

		//replace each group with a Special Text Block, this should help prevent
		//  single columns from being broken if whitespace is the column
		//  separator
		foreach ($matches[0] AS $ind => $matchData) {
			$matchString = $matchData[0];
			$offset = $matchData[1];

			$currGroupMarker = $this->GROUPINGKEY.str_pad($this->currGroupIndex, 5, "0", STR_PAD_LEFT);
//			$test_substr = substr($newLine, ($offset - $offsetCorrection), strlen($matchString));
			$newLine = substr_replace($newLine, $currGroupMarker." ", ($offset - $offsetCorrection), strlen($matchString));
			$offsetCorrection += strlen($matchString) - strlen($currGroupMarker) - 1;

			$this->groupedText[$this->currGroupIndex] = $matchString;
			$this->currGroupIndex++;
		}

		return $newLine;
	}


	private function removeDelimiterSubstitutions($columns) {
		$keysize = strlen($this->GROUPINGKEY);

		$newColumns = array();

		foreach ($columns AS $ind => $string) {

			if (substr($string, 0, $keysize) != $this->GROUPINGKEY) {
				$newColumns[] = $string;
			} else {
				$indexStr = substr($string, $keysize + 1);
				$index = $indexStr + 0;
				$newColumns[] = $this->groupedText[$index];
			}
		}
		return $newColumns;
	}

	private function doReport($requestedReport) {
		$title = $requestedReport["reportName"];
		$columnIdx = $requestedReport["columnIndex"];
		$colTitle = $requestedReport["columnTitle"];
		$reqFunc = $requestedReport["function"];
		$filter = $requestedReport["filterOp"];
		$filterOperand1 = $requestedReport["operand1"];

		$report = array();
		$report["Report Title"] = $title;
		$report["Column"] = "All Columns";
		if (null != $colTitle) {
			$report["Column"] = $colTitle;
		}

		switch ($reqFunc) {
			case 'COUNT':
				$cnt = $this->reportGetCount($columnIdx, $filter, $filterOperand1);
				$report["Number of Rows"] = $cnt;

				$this->reportData->addReportRow($report);
				break;

			case "TOP":
				$topItems = $this->reportGetTop($columnIdx);
				$report["Top Items and Percentage"] = $topItems;

				$this->reportData->addReportRow($report);
				break;

			default:

		}
	}


	private function reportGetCount($colIdx, $filter, $filtOperand) {
		$rows = $this->records->getNumberOfRows();

		if ($colIdx < 0) {
			return $rows;
		} else {

			$cnt = 0;
			for ($i = 0; $i < $rows; $i++) {
				$rec = $this->records->getRowByIndex($i);
				$colValue = $rec[$colIdx];

				//TODO: implement other filter operators
				switch ($filter) {
					case 'GTE':
						if ($colValue >= $filtOperand) {
							$cnt++;
						}
						break;

					default:
				}
			}
			return $cnt;
		}
	}

	private function reportGetTop($colIndex) {

		$NUM_OF_TOP = 3;

		$allItems = array();
		$rows     = $this->records->getNumberOfRows();

		$cnt = 0;
		for ( $i = 0; $i < $rows; $i ++ ) {
			$rec      = $this->records->getRowByIndex( $i );
			$colValue = $rec[ $colIndex ];

			if ( isset( $allItems[$colValue] ) ) {
				$allItems[$colValue]++;
			} else {
				$allItems[$colValue] = 1;
			}
		}

		$topData = array();
		if ($rows > 0) {
			arsort($allItems, SORT_NUMERIC);
			$topCol = array();
			$topCnt = array();

			$keys = array_keys($allItems);
			for ($i = 0; $i < $NUM_OF_TOP AND $i < sizeof($keys); $i++) {
				$key = $keys[$i];

				$topCol[] = $key;
				$roundedVal = round(($allItems[$key] / $rows), 6);
				$pct = $roundedVal * 100;
				$topCnt[] = number_format($pct, 3);
			}

			foreach ($topCol AS $ind => $item) {
				$row               = array();
				$row["Item"]       = $topCol[ $ind ];
				$row["Percentage"] = $topCnt[ $ind ];

				$topData[] = $row;
			}
		}

		return $topData;

	}

}

?>
