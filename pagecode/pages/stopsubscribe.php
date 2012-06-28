<?php
class stopsubscribe_php extends CPageCodeHandler
{

	public function stopsubscribe_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$data = GP("data");
		
		$dedata = base64_decode($data);
		$dmas = explode("|",$dedata);
		$logintype = $dmas[0];
		$id = $dmas[1];
		
		switch ($logintype) {
			case "user" : 
				SQLProvider::ExecuteQuery("update tbl__registered_user set subscribe=0 where tbl_obj_id=$id");
			break;
			case "agency" : 
				SQLProvider::ExecuteQuery("update tbl__agency_doc set subscribe=0 where tbl_obj_id=$id");
			break;
			case "area" : 
				SQLProvider::ExecuteQuery("update tbl__area_doc set subscribe=0 where tbl_obj_id=$id");
			break;
			case "conctractor" : 
				SQLProvider::ExecuteQuery("update tbl__contractor_doc set subscribe=0 where tbl_obj_id=$id");
			break;
			case "artist" : 
				SQLProvider::ExecuteQuery("update tbl__artist_doc set subscribe=0 where tbl_obj_id=$id");
			break;

		}

	}
}
?>
