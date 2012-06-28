<?php
    function allToUpperCase(&$value, $key) {
        $value = strtoupper($value);
    }

	class search_php extends CPageCodeHandler 
	{
//		public $max_per_page = 10;
		
		public $pageSize = 20;
		
		public $searchtext = "";
		
		public $hide_msg = true;
		
		public function search_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{

   		}
}
