<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\Views\impl;

use App\Views\ParserReportsIface;
use App\Views\ParserViewIface;

class ParserViewSimple implements ParserViewIface
{

	private $reports;
	private $strPath;
	private $fileSize;


	public function setReportOutput(ParserReportsIface $reports) {
		$this->reports = $reports;
	}


	public function setMetaData($filepath, $filesize) {
		$this->strPath = $filepath;
		$this->fileSize = $filesize;
	}



	public function doLandingPage() {
		print ' <form action="./index.php">
  Please type the full path to the log file on the webserver<input type="textbox" name="accesslogfile">
  <input type="hidden"  name="action" value="setFile">
  <input type="submit">
</form> ';


	}


	/**
	* retrieves that data from the model and builds the output in HTML format
	*
	* @return (string)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function doOutput()
	{
		$recs = $this->reports->getAllReports();
		$arrRecs = json_decode($recs);

		$out = "";
		$out .= '<HTML><body>';

		$out .= "<br><i>Processed a file located at '".$this->strPath." with a size of ".$this->fileSize."</i><br>";

		foreach($arrRecs AS $ind => $report) {

			$out .= '<TABLE border="1">';
			$outTop = "";
			$outBottom = "";

			foreach ($report AS $title => $value) {

				$outTop .= "<td><b>".$title."</b></td>";

				if (!is_array($value)) {
					$outBottom .= "<td>".$value."</td>";
				} else {
					$outBottom .= "<td>";

					foreach ($value AS $vIndex => $vData) {
						if (is_object($vData) OR is_array($vData)) {
							$sep = "";
							foreach ( $vData AS $dataDesc => $dataValue ) {
								$outBottom .= $sep . $dataDesc . "=" . $dataValue;
								$sep = " &nbsp; ";
							}
						}
						$outBottom .= "<br>";
					}
					$outBottom .= "</td>";
				}
			}
			$out .= "<tr>".$outTop."</tr>";
			$out .= "<tr>".$outBottom."</tr>";

			$out .= '</TABLE>';
		}

		$out .= '</a></body></HTML>';

		print nl2br($out);
	}

}
