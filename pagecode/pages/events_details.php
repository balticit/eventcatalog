<?php
	class events_details_php extends CPageCodeHandler 
	{
		public function events_details_php()
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
			$unit = SQLProvider::ExecuteQuery("SELECT * from tbl__events where tbl_obj_id = $id");
			if (sizeof($unit)==0)
			{
				CURLHandler::Redirect("/");
			}
			$title = $this->GetControl("title");
			$title->text = $unit[0]["title"];
			$unit[0]['date'] = date("d.m.Y",strtotime($unit[0]['date']));
			//photos
			$photos = SQLProvider::ExecuteQuery("SELECT
						  p.*
						FROM 
						  `tbl__events2photo`  ap
						  inner join `tbl__photo` p 
						  on ap.photo_id = p.tbl_obj_id 
						  where news_id=$id limit 8");
			$unit[0]["photo_list_1"] = "";
			$unit[0]["photo_list_2"] = "";
			$unit[0]["images_count"] = sizeof($photos);
			$unit[0]["js_images"] = "";
			$unit[0]["image_nav"] = "";
			$hasNofotos = true;
			if (sizeof($photos)>0)
			{
				$hasNofotos = false;
				$photos1 = array();
				$photos2 = array();
				for ($i=0;$i<sizeof($photos);$i++)
				{
					$photos[$i]["number"] = $i+1;
					if ($i<4)
					{
						array_push($photos1,$photos[$i]);
					}
					else
					{
						array_push($photos2,$photos[$i]);
					}
				}
				$imageNav = $this->GetControl("imageNav");
				$photos[0]["url"] = $_SERVER['HTTP_HOST'];
				$imageNav->dataSource = $photos[0];
				$unit[0]["image_nav"] = $imageNav->Render();
				$jsImageList = $this->GetControl("jsImageList");
				$jsImageList->dataSource = $photos;
				$unit[0]["js_images"]=$jsImageList->Render();
				while (sizeof($photos1)<4)
				{
					array_push($photos1,array("alt"=>1));
				}
				$photoList1 = $this->GetControl("photoList1");
				$photoList1->dataSource = $photos1;
				$unit[0]["photo_list_1"] = $photoList1->Render();
				if (sizeof($photos)>4)
				{
					while (sizeof($photos2)<4)
					{
						array_push($photos2,array("alt"=>1));
					}
					$photoList2 = $this->GetControl("photoList2");
					$photoList2->dataSource = $photos2;
					$unit[0]["photo_list_2"] = $photoList2->Render();
				}
			}
			$unit[0]["foto_visible"] = $hasNofotos?"hidden":"visible";
			//end photos
			
			$unit[0]["newstype"] = "event";
			$contDetails = $this->GetControl("details");
			$contDetails->dataSource = $unit[0];	
		}
	}
?>
