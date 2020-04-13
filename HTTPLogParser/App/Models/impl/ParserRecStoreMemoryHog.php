<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.

namespace App\Models\impl;

use App\Models\ParserRecStorageIface;

class ParserRecStoreMemoryHog implements ParserRecStorageIface {

	private $records = array();

	public function addDataRow($data){
		$this->records[] = $data;
	}

	public function getRowByIndex($index) {
		return $this->records[$index];
	}

	public function getNumberOfRows() {
		return sizeof($this->records);
	}

}