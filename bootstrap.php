<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

use LibHelpers\LibLog\impl\LogStdOut;
use App\LogicServices\impl\HTTPLogParser;
use \App\LogicServices\impl\BasicParserConfig;
use App\HTTP\Controllers\ParserEventHandler;
use App\Views\impl\ParserViewSimple;
use App\Models\impl\ParserRecStoreMemoryHog;
use LibHelpers\LibSanitize\impl\SanitizeWebInput;
use App\Views\impl\ParserReportsJSON;


// bootstrap.php
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('SRC', ROOT . 'HTTPLogParser' . DIRECTORY_SEPARATOR);
define('LIB', ROOT . '' . DIRECTORY_SEPARATOR);

spl_autoload_register(function ($class) {
	$file = SRC . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require $file;
	}

	$file = LIB . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require $file;
	}
});


//basic driver code
$logger = new LogStdOut();
$sanitizer = new SanitizeWebInput($logger);
$parserRecs = new ParserRecStoreMemoryHog();
$config = createParseConfiguration();
$reports = new ParserReportsJSON();
$parser = new HTTPLogParser($config, $parserRecs, $reports);

    /*
  			$testLine = "83.149.9.216 - - [17/May/2015:10:05:03 +0000] \"GET /presentations/logstash-monitorama-2013/images/kibana-search.png HTTP/1.1\" 200 203023 \"http://semicomplete.com/presentations/logstash-monitorama-2013/\" \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.77 Safari/537.36\"";
			$cols = $parser->parseLogLine($testLine);
			print "<br>".nl2br(print_r($cols, true));

// */
$view = new ParserViewSimple();
$evnthndl = new ParserEventHandler($parser, $view, $sanitizer);

//send raw input to controller for processing
$evnthndl->processAction($_REQUEST);



 function createParseConfiguration() {

	/*
	  total number of entries found, how many of them were errors or success
	  (based on the HTTP return code), what files were visited more often and
	  the most popular referers (and their %'s too).


	 83.149.9.216 - - [17/May/2015:10:05:03 +0000]
			"GET /presentations/logstash-monitorama-2013/images/kibana-search.png HTTP/1.1"
			200 203023 "http://semicomplete.com/presentations/logstash-monitorama-2013/"
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.77 Safari/537.36"



	 the combined log format.

	LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\"" combined

	%h is the remote host (ie the client IP)
	%l is the identity of the user determined by identd (not usually used since not reliable)
	%u is the user name determined by HTTP authentication
	%t is the time the request was received.
	%r is the request line from the client. ("GET / HTTP/1.0")
	%>s is the status code sent from the server to the client (200, 404 etc.)
	%b is the size of the response to the client (in bytes)
	Referer is the Referer header of the HTTP request (containing the URL of the page from which this request was initiated) if any is present, and "-" otherwise.
	User-agent is the browser identification string.

	*/

	$conf = new BasicParserConfig();

	$conf->setColumnSeparatorRegex("@\s+@");

	//adding file format configurations with function having this signature: 
        //    addConfig($reportName, $columnIndex, $colTitle, $desiredFunc, $filterOperator, $filterOperand1)
	$conf->addConfig("Number of Records", -1, null, "COUNT");
	$conf->addConfig("Failures", 5, "HTTP RESPONSE CODE", "COUNT", "GTE", 400);
	$conf->addConfig("Most Popular Pages", 4, "REQUESTED RESOURCE", "TOP");
	$conf->addConfig("Most Common Referrers", 7, "REFERER", "TOP");
	$conf->addConfig("Most common User Agents", 8, "USER AGENT", "TOP");

	//adding groupings with function having this signature: addGrouping($delimStart, $delimEnd, $delimEscapeSeq)
	$conf->addGrouping('"', '"', '\\');
	$conf->addGrouping('[', ']', '\\');

	return $conf;
}
