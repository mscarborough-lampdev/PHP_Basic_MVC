<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.

namespace App\Models;

interface ParserRecStorageIface {

	public function addDataRow( $data );

	public function getRowByIndex( $index );

	public function getNumberOfRows();
}