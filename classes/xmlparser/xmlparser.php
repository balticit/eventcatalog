<?php
class XMLElement
{
	var $depth = 0;
	var $type;
	var $name;
	var $data;
	var $attrs;
}

class XMLParser
{
		private static $instance;
		
		public static function GetInstance()
		{
			if (is_null(XMLParser::$instance))
			{
				XMLParser::$instance = new XMLParser();
			}
			XMLParser::$instance->sourceEncoding = "utf-8";
			XMLParser::$instance->targerEncoding = "windows-1251";
			XMLParser::$instance->depth = 0;
			XMLParser::$instance->xmlData = array();
			XMLParser::$instance->useConversion = true;
			return XMLParser::$instance;
		}
	
	var $sourceEncoding = "utf-8";
	var $targerEncoding = "windows-1251";
	var $depth =0;
	var $xmlData = array();
	var $useConversion = true;
	
	function XMLParser()
	{
		$this->depth=0;
		$this->xmlData = array();
	}
	
	function startElement($parser, $name, $attrs)
	{
		$element = new XMLElement();
		$element->depth = $this->depth;
		if (($this->useConversion)&&($this->sourceEncoding!=$this->targerEncoding))
		{
			$cattrs = array();
			foreach ($attrs as $key=>$value)
			{
				$cattrs[$key] = iconv($this->sourceEncoding,$this->targerEncoding,$value);
			}
			$attrs = $cattrs;
		}
		$element->attrs = $attrs;
		$element->name = $name;
		$element->type = 0;
		array_push($this->xmlData,$element);
		$this->depth++;
		//print "start<br/>";
	}

	function endElement($parser, $name)
	{
		//echo "<br/>";
		$this->depth--;
		$element = new XMLElement();
		$element->depth = $this->depth;
		$element->name = $name;
		$element->type = 1;
		array_push($this->xmlData,$element);
				//print "end<br/>";

	}

	function characterData($parser, $data)
	{
		$element = array_pop($this->xmlData);
		$element->data .=($this->useConversion)?iconv($this->sourceEncoding,$this->targerEncoding,$data):$data;
		array_push($this->xmlData,$element);
				//print "data $element->data<br/>";

		//echo "<b>".$element->data.$this->useConversion."</b>";
	}

	function ConvertStackToArray(&$result = array(),&$index=0,&$depth=0)
	{
		//print "<br/>";
		$element = $this->xmlData[$index];
		//print_r($element);
		if ($element->type==0)
		{
			if (!isset($result[$element->name]))
			{
				$result[$element->name] = array();
			}
			array_push($result[$element->name],array());
			$place = sizeof($result[$element->name])-1;
			//echo "<b>".$element->data."</b>";
			$result[$element->name][$place]["\\"] = $element->data;
			$result[$element->name][$place]["/"] = $element->attrs;
			$depth = $element->depth;

		}
		else
		{
			$depth = $element->depth;
			$index++;
			return ;
		}
		$index++;
		$element2 = $this->xmlData[$index];
		if ($element2->type==0)
		{
			$depth = $element2->depth;
			$place = sizeof($result[$element->name])-1;
			while ($depth!= $element->depth)
			{
				$this->ConvertStackToArray(&$result[$element->name][$place],&$index,&$depth);
			}
		}
		else
		{
			$depth = $element2->depth;
			$index++;
			return ;
		}
	}
	function GetXMLArray($filename)
	{
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_set_element_handler($xml_parser, array('XMLParser',"startElement"), array('XMLParser',"endElement"));
		xml_set_character_data_handler($xml_parser, array('XMLParser',"characterData"));
		
		if (!($fp = fopen($filename, "r"))) {
			die("could not open XML input");
		}

		while ($data = fread($fp, 4096)) {			
			if (!xml_parse($xml_parser, $data, feof($fp))) {
				die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
			}
		}
		xml_parser_free($xml_parser);
		$res = array();

		$this->ConvertStackToArray(&$res);
		//$this->$depth=0;
		//$this->$xmlData = array();
		return $res;
	}
}
?>