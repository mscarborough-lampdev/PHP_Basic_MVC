<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.

namespace App\Views\impl;

use App\Views\ParserReportsIface;

class ParserReportsJSON implements ParserReportsIface {

	private $records = array();

	public function addReportRow($data){
		$this->records[] = $data;
	}

	public function getRowByIndex($index) {
		return json_encode($this->records[$index]);
	}

	public function getAllReports() {
		return json_encode($this->records);
	}
}