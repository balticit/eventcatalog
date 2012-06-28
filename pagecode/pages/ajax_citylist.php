<?php
	class ajax_citylist_php extends CPageCodeHandler 
	{

		public function ajax_citylist_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
		}
		public function Render()
		{
			header('Content-type: text/html;charset=windows-1251');
			$city = GetParam("city","pg");
			if (isset($city))
			{
				if (is_numeric($city))
				{
					setcookie("city",$city,time()+60*60*24*30,"/"); //for 30-days
					
				}
				else
				{
					setcookie("city","",time()-3600,"/");
				}
				echo("OK");
			}
			else
			{
				$cities = SQLProvider::ExecuteQuery("select * from `tbl__city` where `active`=1 order by priority desc,title asc");
				$html = "<div><div id=\"cityselector\">
						   <div class=\"city-selector-item\" style=\"font-weight:bold\"><a href=\"all\">Все города</a></div>";
				foreach ($cities as $key=>$value)
				{

					$html .= "<div class=\"city-selector-item\"";
					if ($value["priority"] > 0) { $html .= "style=\"font-weight:bold\""; }
					$html .= "><a href=\"".$value["tbl_obj_id"]."\">".$value["title"]."</a></div>";
				}		   
				$html.="</div></div>";
				echo($html);
			}
		}
	}
?>
