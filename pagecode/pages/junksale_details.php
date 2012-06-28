<?php
class junksale_details_php extends CPageCodeHandler
{
	public function junksale_details_php()
	{
		$this->CPageCodeHandler();
	}

	public function BuildFilter($filter,$name,$value,$cond="=",$type="numeric")
	{
		switch ($type) {
			case "numeric":
			{
				if (is_numeric($value))
				{
					if (strlen($filter)>0)
					{
						$filter.=" and ";
					}
					$filter.=" $name $cond $value ";
				}
			}
			break;
			case "string":
			if (is_string($value))
			{
				if (strlen($filter)>0)
				{
					$filter.=" and ";
				}
				$filter.=" $name $cond '$value' ";
			}
			break;
		}
		return $filter;
	}

	public function PreRender()
	{
        $app = CApplicationContext::GetInstance();
        $city = GP("city");
		$type = GP("type","sell");
		$id = GPT("id","int",0);
		$fliter = " active=1 ";
		$fliter = $this->BuildFilter($fliter,"tbl_obj_id",$id);
		//item list
		$junks = array();
		if ($type=="sell")
		{
			$junks = SQLProvider::ExecuteQuery( "select * from `tbl__baraholka_sell` where $fliter ");
		}
		if ($type=="search")
		{
			$junks = SQLProvider::ExecuteQuery( "select * from `tbl__baraholka_search` where $fliter ");
		}
		if (sizeof($junks)==0)
		{
			CURLHandler::Redirect("/");
		}
		$title = $this->GetControl("title");
		$title->text = $junks[0]["title"];
		$junks[0]["title2"] = " ";
		if ($type=="sell")
		{
			$cpars = CPArray($rewriteParams,array("type","city","section"));
			$junks[0]["js_photos"] = "";
			$junks[0]["item_photos"] = "";
			$junks[0]["medi_photo"] = "";
			$photos =  SQLProvider::ExecuteQuery( "select * from `vw__baraholka_photo` where junk_id =$id limit 3 ");
			$photo_count = sizeof($photos);
			$junks[0]["photo_count"] = $photo_count;
			if ($photo_count>0)
			{
				$photos[0]["url"] = $_SERVER['HTTP_HOST'];
				for ($i=0;$i<$photo_count;$i++)
				{
					$photos[$i]["style"] = ($i==0)?"style=\"color:red;\"":"";
					$photos[$i]["number"] = $i+1;
				}
				$jsImageList = $this->GetControl("jsImageList");
				$jsImageList->dataSource = $photos;
				$photoList = $this->GetControl("photoList");
				$photoList->dataSource = $photos;
				$imageNav = $this->GetControl("imageNav");
				$imageNav->dataSource = $photos[0];
				$junks[0]["js_photos"] = $jsImageList->Render();
				$junks[0]["item_photos"] = $photoList->Render();
				$junks[0]["medi_photo"] =  $imageNav->Render();
				$junks[0]["title2"] = $junks[0]["title"];
			}
		}
		$junks[0]["type"] = $type;
		$junks[0]["date"] = date("d.m.Y", strtotime($junks[0]["date"]));;
		$junks[0]["event_date"] = date("d.m.Y", strtotime($junks[0]["event_date"]));;
		$details = $this->GetControl("details");
		$details->dataSource = $junks;

		//type selectors
		$sellPars = CPArray($rewriteParams,array("city","section"));
		$sellPars["type"] = "sell";
		$sellLink = $this->GetControl("sellLink");
		$sellLink->dataSource = array("link"=>CURLHandler::BuildRewriteParams($sellPars),
		"title"=>"Распродажа вещей с мероприятия",
		"selected"=>($type=="sell")?"id = \"selected\"":"");
		$sellPars = CPArray($rewriteParams,array("city","section"));
		$sellPars["type"] = "search";
		$sellLink = $this->GetControl("searchLink");
		$sellLink->dataSource = array("link"=>CURLHandler::BuildRewriteParams($sellPars),
		"title"=>"Поиск вещей для мероприятия",
		"selected"=>($type=="search")?"id = \"selected\"":"");

		//kind of activity
        $types  = array();
        $typeItem = array(
            'title'=>'Распродажа вещей',
            'link'=>CURLHandler::$currentPath.CURLHandler::BuildRewriteParams(array("type"=>"sell")),
            'selected'=> GP("type","sell") == 'sell'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typeItem = array(
            'title'=>'Поиск вещей',
            'link'=>CURLHandler::$currentPath.CURLHandler::BuildRewriteParams(array("type"=>"search")),
            'selected'=> GP("type","sell") == 'search'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typesList = $this->GetControl("junkTypeList");
        $typesList->dataSource = $types;

	}
}
?>
