<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\LogicServices\impl;

use App\LogicServices\ParserConfigIface;

class BasicParserConfig implements ParserConfigIface
{

	private $reportName = array();
	private $columnIndices = array();
	private $columnTitles = array();
	private $desiredFunctions = array();
	private $filterOperator = array();
	private $funcOperand1 = array();

	private $delimStart = array();
	private $delimEnd = array();
	private $delimEscapeSeq = array();

	private $columnSeparatorRegex = "@\s+@";


	public function setColumnSeparatorRegex($regexString) {
		$this->columnSeparatorRegex = $regexString;
	}


	public function getColumnSeparatorRegex() {
		return $this->columnSeparatorRegex;
	}


	public function addConfig(
		$reportName,
		$columnIndex,
		$colTitle,
		$desiredFunc,
		$filterOperator = null,
		$filterOperand1 = null
	) {
		$this->reportName[] = $reportName;
		$this->columnIndices[] = $columnIndex;
		$this->columnTitles[] = $colTitle;
		$this->desiredFunctions[] = $desiredFunc;
		$this->filterOperator[] = $filterOperator;
		$this->funcOperand1[] = $filterOperand1;
	}

	public function getNumberOfConfigs() {
		return sizeof($this->columnIndices);
	}

	public function getConfigByIndex($index) {
		if (!is_numeric($index) OR
		        ($index < 0) OR
		        ($index >= sizeof($this->columnIndices))) {
			throw new \Exception("Index out of bounds");
		}

		$config = array();
		$config["reportName"] = $this->reportName[$index];
		$config["columnIndex"] = $this->columnIndices[$index];
		$config["columnTitle"] = $this->columnTitles[$index];
		$config["function"] = $this->desiredFunctions[$index];
		$config["filterOp"] = $this->filterOperator[$index];
		$config["operand1"] = $this->funcOperand1[$index];

		return $config;
	}


	public function getAllConfigs() {
		$size = sizeof($this->columnIndices);

		$allConfigs = array();
		for ($i = 0; $i < $size; $i++) {
			$allConfigs[] = $this->getConfigByIndex($i);
		}

		return $allConfigs;
	}

	public function addGrouping($delimStart, $delimEnd, $delimEscapeSeq) {
		$this->delimStart[] = $delimStart;
		$this->delimEnd[] = $delimEnd;
		$this->delimEscapeSeq[] = $delimEscapeSeq;
	}

	public function getNumberOfGroupings() {
		return sizeof($this->delimStart);
	}

	public function getGroupingsByIndex($index) {
		if (!is_numeric($index) OR
		    ($index < 0) OR
		    ($index >= sizeof($this->delimStart))) {
			throw new \Exception("Index out of bounds");
		}

		$group = array();
		$group["start"] = $this->delimStart[$index];
		$group["end"] = $this->delimEnd[$index];
		$group["esc"] = $this->delimEscapeSeq[$index];

		return $group;
	}


	public function getAllGroupings() {
		$size = sizeof($this->delimStart);

		$allGroupings = array();
		for ($i = 0; $i < $size; $i++) {
			$allGroupings[] = $this->getGroupingsByIndex($i);
		}

		return $allGroupings;
	}

}