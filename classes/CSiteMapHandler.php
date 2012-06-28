<?php
	class CSiteNode
	{
		public $id;
		public $node;
		public $configfile;
		public $encoding = null;
		public $cmsfile;
		public $id_translit;
		public $hidden;
    
		public $parent;
		public $childs;
		public $childsHidden;
    public $name;

		function __set($data, $value)
		{
		  echo "<pre>";
			echo "Error assigning value '$value' to undefined attribute '$data' of CSiteNode";
			echo "<pre>";
		}
	}


	class CSiteMapHandler
	{
		public static $configPath;
		
		public static $appEncoding = "utf-8";
		
		public static $defaultPath = "/";
		
		private static $current;

		private static $root;
		
	  private static function startTag($parser, $name, $attrs)
		{
			$el = new CSiteNode();
			$el->parent =& CSiteMapHandler::$current;
			$el->name = $name;
			foreach($attrs as $key=>$value)
			  $el->$key = $value;
		  if (is_null($el->encoding))
			  $el->encoding = CSiteMapHandler::$root->encoding;
			if ($name == 'sitenode')
				if (isset($attrs['node']))
					if (isset($attrs['hidden']) && $attrs['hidden'] == 1)
					  CSiteMapHandler::$current->childsHidden[$attrs['node']] = $el;
					else
					  CSiteMapHandler::$current->childs[$attrs['node']] = $el;
				else
					die("Error in tag 'sitenode', attribute 'node' not found");
			else
				CSiteMapHandler::$current->childs[$name] = $el;	
			CSiteMapHandler::$current =& $el;
			if (is_null(CSiteMapHandler::$root))
				CSiteMapHandler::$root =& CSiteMapHandler::$current;
		}

		private static function endTag($parser, $name)
		{
		  CSiteMapHandler::$current =& CSiteMapHandler::$current->parent;
		}

		private static function BuildPageList()
		{
			$xml_parser = xml_parser_create();
			xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
			xml_set_element_handler($xml_parser, array('CSiteMapHandler',"startTag"), array('CSiteMapHandler',"endTag"));
			
			if (!($fp = fopen(CSiteMapHandler::$configPath, "r"))) {
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
      
			CSiteMapHandler::$appEncoding = CSiteMapHandler::$root->encoding;
		}
		
		public static function GetRootNode()
		{
			if (is_null(CSiteMapHandler::$root))
			{
				CSiteMapHandler::BuildPageList();
			}
			if (CSiteMapHandler::$root->name =! 'sitemap' || 
			    !is_array(CSiteMapHandler::$root->childs) || 
					!isset(CSiteMapHandler::$root->childs["/"]))
			  die("Error in sitemap.xml");
			return CSiteMapHandler::$root->childs["/"];
		}
	}
?>
