<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\Views;



interface ParserViewIface
{
	public function doLandingPage();

	public function doOutput();

	public function setReportOutput(ParserReportsIface $reports);
}
