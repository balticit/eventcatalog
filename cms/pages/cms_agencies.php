<?php
	class cms_agencies_php extends CCMSPageCodeHandler 
	{
		public function cms_agencies_php()
		{
			$this->CCMSPageCodeHandler();
		}
		
		public function ValRec($sender,$args)
		{
			if ($args["data"]->recommended>0)
				return;
			$obj_id = $args["data"]->tbl_obj_id;
			if (!($obj_id>0))
				$obj_id=0;
			$res = SQLProvider::ExecuteQuery("select count(*) cn  from tbl__agency_doc where not recommended is null and recommended>0");	
			$args["canMark"] = ($res[0]["cn"]<5);

		}
		public function PreRender()
		{

		}
	}
?>
