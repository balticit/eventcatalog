<?php
	class ajax_etv_type_php extends CPageCodeHandler 
	{

		public function ajax_etv_type_php()
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
					$_SESSION['etv_type'] = 1;// setcookie("etv_type",1,time()+60*60*24*30,"/"); //for 30-days					
				}
				else
				{
					$_SESSION['etv_type'] = 0;//setcookie("etv_type",0,time()-3600,"/");
				}
				echo("OK");
			}

		}
	}
?>
