<?php
	class interactive_details_php extends CPageCodeHandler 
	{
		public function interactive_details_php()
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
			$unit = SQLProvider::ExecuteQuery("SELECT * from tbl__interactive where tbl_obj_id = $id");
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
					  `tbl__interactive2photo`  ap
					  inner join `tbl__photo` p 
					  on ap.photo_id = p.tbl_obj_id 
					  where news_id=$id limit 8");
		$unit[0]["photo_list_1"] = "";
		$unit[0]["photo_list_2"] = "";
		$unit[0]["images_count"] = sizeof($photos);
		$unit[0]["js_images"] = "";
		$unit[0]["image_nav"] = "";
		$unit[0]["foto_visible"] = "none";
		
		$hasNofotos = true;
		if (sizeof($photos)>0)
		{
		$unit[0]["foto_visible"] = "block";
			$hasNofotos = false;
			$photos1 = array();
			$photos2 = array();
			for ($i=0;$i<sizeof($photos);$i++)
			{
				$photos[$i]["number"] = $i+1;
				/*$photos[$i]["width"] = "z:\home\eventcatalog\www\application\public\upload\\".$photos[$i]["l_image"];
				$im = @imagecreatefromjpeg("z:\home\eventcatalog\www\application\public\upload\\".$photos[$i]["l_image"]);
				if ($im) {
					$photos[$i]["width"] = imagesx($im);
					$photos[$i]["height"] = imagesy($im);
					$imagedestroy($im);
				}*/
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


			//end photos
			
			$unit[0]["newstype"] = "news";
			$contDetails = $this->GetControl("details");
			$contDetails->dataSource = $unit[0];
			
			
			
			$news = SQLProvider::ExecuteQuery( "select * from `tbl__interactive` where active=1 order by display_order desc ");
			$flashs = array();
			foreach ($news as $key=>$value)
			{
				$news[$key]["flash"] = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="200" height="200">
				  <param name="movie" value="/upload/'.$news[$key]['s_flash'].'" />
				  <param name="quality" value="high" />
				  <embed src="/upload/'.$news[$key]['s_flash'].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="200" height="200"></embed>
				</object>';
			}
			
			$newsList = $this->GetControl("newsList");
			$newsList->dataSource = $news;
			
				
		}
	}
?>
