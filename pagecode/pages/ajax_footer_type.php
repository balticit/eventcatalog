<?php
	class ajax_footer_type_php extends CPageCodeHandler 
	{

		public function ajax_footer_type_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
		}
		public function Render()
		{
			header('Content-type: text/html;charset=windows-1251');
			$type = GetParam("type","pg");
			if (isset($type))
			{
				if (is_numeric($type) && $type == 1)
				{
					setcookie('footer_type',1,time()+60*60*24*14,"/");
				}
				else
				{
					setcookie('footer_type',null,0,"/");
				}
				echo("OK");
			}

		}
	}
?>
