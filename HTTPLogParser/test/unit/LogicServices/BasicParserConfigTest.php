<?php

namespace Test;

use App\LogicServices\impl\HTTPLogParser;
use App\LogicServices\LogParserIface;

class BasicParserConfigTest extends \PHPUnit\Framework\TestCase
{
	// Test whether the parser will succeed on simple cases without delimiters
	public function testParseLogLineWorksCorrectlyWithoutGroupings()
	{
		//create constructor mocks
		$parserRecs = $this->getMockBuilder('App\Models\ParserRecStorageIface')
		                   ->getMock();
		$config = $this->getMockBuilder('App\LogicServices\ParserConfigIface')
		               ->getMock();
		$config->expects($this->once())
		       ->method('getAllGroupings')
		       ->willReturn(array());
		$config->expects($this->once())
		       ->method('getColumnSeparatorRegex')
		       ->willReturn("@\s+@");

		$reports = $this->getMockBuilder('App\Views\ParserReportsIface')
		                ->getMock();

		//create SUT
		$parser = new HTTPLogParser($config, $parserRecs, $reports);

		$logInput = $this->getStringLogLineTestInputWithoutGrp();

		$expectedOutput = $this->getArrayLogLineExpectedOutputWithoutGrp();
		$actualOutput = $parser->parseLogLine($logInput);

		$this->assertEquals($expectedOutput, $actualOutput);
	}

	// Test whether the parser will succeed on complex cases with delimiters
	public function testParseLogLineWorksCorrectlyWithGroupings()
	{
		//create constructor mocks
		$parserRecs = $this->getMockBuilder('App\Models\ParserRecStorageIface')
		                   ->getMock();
		$config = $this->getMockBuilder('App\LogicServices\ParserConfigIface')
		               ->getMock();
		$groupings = $this->getArrayTestConfigGroupings();
		$config->expects($this->once())
		       ->method('getAllGroupings')
		       ->willReturn($groupings);
		$config->expects($this->once())
		       ->method('getColumnSeparatorRegex')
		       ->willReturn("@\s+@");

		$reports = $this->getMockBuilder('App\Views\ParserReportsIface')
		                ->getMock();

		//create SUT
		$parser = new HTTPLogParser($config, $parserRecs, $reports);

		$logInput = $this->getStringLogLineTestInputWithGrp();

		$expectedOutput = $this->getArrayLogLineExpectedOutputWithGrp();
		$actualOutput = $parser->parseLogLine($logInput);

		$this->assertEquals($expectedOutput, $actualOutput);
	}

	private function getStringLogLineTestInputWithoutGrp() {
		$str = '83.149.9.216 - - [17/May/2015:10:05:03_+0000] "GET_/presentations/logstash-monitorama-2013/images/kibana-search.png_HTTP/1.1" 200 203023 "http://semicomplete.com/presentations/logstash-monitorama-2013/" "Tiny_Tiny_RSS/1.11_(http://tt-rss.org/)"';
		return $str;
	}

	private function getStreamLogLineTestInputWithoutGrp() {
		$str = $this->getStringLogLineTestInputWithoutGrp();
		$stream = fopen('data://text/plain,' . $str,'r');

		return $stream;
	}


	private function getArrayLogLineExpectedOutputWithoutGrp() {
		$str = '["83.149.9.216","-","-","[17\/May\/2015:10:05:03_+0000]","\"GET_\/presentations\/logstash-monitorama-2013\/images\/kibana-search.png_HTTP\/1.1\"","200","203023","\"http:\/\/semicomplete.com\/presentations\/logstash-monitorama-2013\/\"","\"Tiny_Tiny_RSS\/1.11_(http:\/\/tt-rss.org\/)\""]';
		return json_decode($str);
	}


	private function getStringLogLineTestInputWithGrp() {
		$str = '83.149.9.216 - - [17/May/2015:10:05:03 +0000] "GET /presentations/logstash-monitorama-2013/images/kibana-search.png HTTP/1.1" 200 203023 "http://semicomplete.com/presentations/logstash-monitorama-2013/" "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.77 Safari/537.36"';
		return $str;
	}

	private function getArrayLogLineExpectedOutputWithGrp() {
		$str = '["83.149.9.216","-","-","[17\/May\/2015:10:05:03 +0000]","\"GET \/presentations\/logstash-monitorama-2013\/images\/kibana-search.png HTTP\/1.1\"","200","203023","\"http:\/\/semicomplete.com\/presentations\/logstash-monitorama-2013\/\"","\"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/32.0.1700.77 Safari\/537.36\""]';
		return json_decode($str);
	}

	private function getArrayTestConfigGroupings() {
		$str = '[{"start":"\"","end":"\"","esc":"\\\\"},{"start":"[","end":"]","esc":"\\\\"}]';
		$arrGroupObjects = json_decode($str);

		$objToArr = array();
		foreach ($arrGroupObjects AS $ind => $delimObject) {
			$objToArr[] = $this->convertObjectToArray($delimObject);
		}
		return $objToArr;
	}


	private function convertObjectToArray($obj) {
		$arr = array();
		foreach ($obj AS $propertyKey => $propertyValue) {
			$arr[$propertyKey] = $propertyValue;
		}

		return $arr;
	}

}
