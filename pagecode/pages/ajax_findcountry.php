<?php
	class ajax_findcountry_php extends CPageCodeHandler 
	{

		public function ajax_findcountry_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{	
			$query = GP("query");
            header('Content-type: text/html;charset=utf-8');
            $query1251 = mb_convert_encoding($query,"WINDOWS-1251","UTF-8");
            
            
            
            $countrs = SQLProvider::ExecuteQuery("select title from `tbl__countries`
            where LOWER(`title`) LIKE LOWER('$query1251%')            
            order by region,title asc limit 15");
			
			$result = '';
           
            foreach ($countrs as $row) {
                $result .= ",'".mb_convert_encoding($row['title'],"UTF-8","WINDOWS-1251")."'";
            }
           
           $result = "{query:'$query',suggestions:[".substr($result,1)."]}";
           echo($result);
			
			

		}
	}
?>
