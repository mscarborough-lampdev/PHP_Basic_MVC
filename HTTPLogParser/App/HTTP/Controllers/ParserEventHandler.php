<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
namespace App\HTTP\Controllers;

use App\LogicServices\LogParserIface;
use App\Views\ParserViewIface;
use LibHelpers\LibSanitize\SanitizeIface;
use App\Views\ParserReportsIface;


class ParserEventHandler
{

	private $parser;
	private $view;
	private $sanitizer;

	/**
	* constructor for controller for Lorem Project
	*
	* @param (LoremData) $model	 hook to the logic module for the lorem project
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function __construct(
		LogParserIface $parser,
		ParserViewIface $view,
		SanitizeIface $sanitizer
	) {
		$this->parser = $parser;
		$this->view = $view;
		$this->sanitizer = $sanitizer;
	}



	/**
	* process a click event
	*
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	protected function onClick()
	{
		$this->loremdat->changeText(0);
	}



	/**
	* determine nature of event notification, and route to appropriate function
	*
	* @param (array[string])  probably a $_REQUEST array, but not necessarily.  Array contains values to indicate events
	* @return (NULL)  no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function processAction($vals)
	{
		if (!isset($vals['action'])) {
			$this->view->doLandingPage();
		} else {
			$this->sanitizeInputBasic($vals);

			$action = $this->sanitizer->sanitize_str($vals['action'], 'alpha');


			switch ($action)
			{

				case 'setFile':
					$fname = $this->sanitizer->sanitize_str($vals['accesslogfile'], 'alphasymb');
					$fsize = filesize($fname);
					$fileHandle = fopen($fname, 'r');
					$this->parser->setLogInputStream($fileHandle);
					$this->parser->parseLog();
					$this->parser->doConfiguredReports();
					$reports = $this->parser->getReports();
					$this->view->setReportOutput($reports);

					$this->view->setMetaData($fname, $fsize);

					break;
			}

			//display UI
			$output = $this->view->doOutput();

		}

	}


	private function sanitizeInputBasic($arr) {
		return $this->sanitizer->sanitize_array_stringinput($arr);
	}
}
