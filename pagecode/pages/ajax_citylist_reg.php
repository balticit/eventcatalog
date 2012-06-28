<?php
	class ajax_citylist_reg_php extends CPageCodeHandler 
	{

		public function ajax_citylist_reg_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
		}
		public function Render()
		{
			header('Content-type: text/html;charset=windows-1251');
			$cities = SQLProvider::ExecuteQuery("select * from `tbl__city` where `active`=1 order by priority desc,title asc");
			$html = "<div><div id=\"cityselector\">";
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
?>
