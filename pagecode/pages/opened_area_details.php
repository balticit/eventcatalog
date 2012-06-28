<?php
	class opened_area_details_php extends CPageCodeHandler 
	{
		public function opened_area_details_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$app = CApplicationContext::GetInstance();
			$rewriteParams = array();
			$id = GP("id");
			if (!is_numeric($id))
			{
				$id =0;
			}
			$unit = SQLProvider::ExecuteQuery("SELECT * from tbl__opened_area where tbl_obj_id = $id");
			if (sizeof($unit)==0)
			{
				CURLHandler::Redirect("/");
			}
			$title = $this->GetControl("title");
			$title->text = $unit[0]["title"];
			$unit[0]['date'] = date("d.m.Y",strtotime($unit[0]['date']));
			$id = GP("id",0);
		$details = SQLProvider::ExecuteQuery("select * from vw__opened_areas where tbl_obj_id=$id");
			if ($unit[0]["is_resident"]==1) {
			$unit[0]["link_area"] = "<a href=/area/details/id/".$details[0]["area_id"]." class=\"toplinksarea\" >Подробнее о площадке читайте <br>на её персональной странице</a>";
		}
		else {
			$unit[0]['link_area'] = "";
		}
			
			$unit[0]["newstype"] = "news";
			$contDetails = $this->GetControl("details");
			$contDetails->dataSource = $unit[0];	
		}
	}
?>

<?php
/*class opened_area_details_php extends CPageCodeHandler
{
	public function opened_area_details_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$id = GP("id",0);
		$details = SQLProvider::ExecuteQuery("select * from vw__opened_areas where tbl_obj_id=$id");
		
		if ($details[0]['image']!="") {
			$details[0]['img-display'] = 'block';
		}
		else{
			$details[0]['img-display'] = 'none';
		}
		
		switch ($details[0]["status"]) {
			case 1 :
				$details[0]["status"] ="<span style=\"color: red; font-weight: bold;\">Недавно открылись</span>";
			break;
			case 2 :
				$details[0]["status"] ="<span style=\"color: black; font-weight: bold;\">Скоро открытие</span>";
			break;
			case 3 :
				$details[0]["status"] ="<span style=\"color: black; font-weight: bold;\">Недавно закрылись</span>";
			break;
			case 4 :
				$details[0]["status"] ="<span style=\"color: black; font-weight: bold;\">Скоро закрытие</span>";
			break;
		}
		
		if ($details[0]["is_resident"]==1) {
			$details[0]["link_area"] = "<a href=/area/details/id/".$details[0]["area_id"]." style=\"color: black;\">Подробнее о площадке читайте на её персональной странице</a>";
		}
		else {
			$details[0]['link_area'] = "";
		}
		
		$det = $this->GetControl("details");
		$det->dataSource = $details[0];
	}
}*/
?>
