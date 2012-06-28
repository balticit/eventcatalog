<?php
	class banner_php extends CPageCodeHandler 
	{
		public function banner_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$locID = GP("location");
			if (!IsNullOrEmpty($locID))
			{
				$banner = SQLProvider::ExecuteQuery("select banner from tbl__banners_locations where tbl_obj_id='$locID'");
				if (sizeof($banner)>0)
				{
					$banner = $banner[0]["banner"];
					if (is_numeric($banner))
					{
						$bannerData = SQLProvider::ExecuteQuery("select * from tbl__bans_doc where tbl_obj_id=$banner");
						if (sizeof($bannerData)>0)
						{
							$bannerData[0]["link"] = "/banner?banner=$banner";
							$banrender = $this->GetControl("banner");
							$banrender->key = $bannerData[0]["type"];
							$banrender->dataSource = $bannerData[0];
							$html = str_replace('"','\"',$banrender->Render());
							$banHTML = $this->GetControl("banHTML");
							$banHTML->template = $html;
							$shows = $bannerData[0]["shown"]+1;
							SQLProvider::ExecuteNonReturnQuery("update tbl__bans_doc set `shown`=$shows where tbl_obj_id=$banner");
						}
					}
				}
			}
			$banID = GP("banner");
			if (is_numeric($banID))
			{
				$bannerData = SQLProvider::ExecuteQuery("select * from tbl__bans_doc where tbl_obj_id=$banID");
				if (sizeof($bannerData)>0)
				{		
					$clicks = $bannerData[0]["clicks"]+1;
					SQLProvider::ExecuteNonReturnQuery("update tbl__bans_doc set `clicks`=$clicks where tbl_obj_id=$banID");		
					CURLHandler::Redirect($bannerData[0]["link"]);		
				}
			}
		}
	}
?>
