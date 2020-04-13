<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.

namespace App\Views;

interface ParserReportsIface {

	public function addReportRow( $data );

	public function getRowByIndex( $index );

	public function getAllReports();

}