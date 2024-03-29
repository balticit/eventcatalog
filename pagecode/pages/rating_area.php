<?php
class rating_area_php extends CPageCodeHandler
{
	public $pageSize = 100;

	public function rating_area_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$app = CApplicationContext::GetInstance();
    $av_rwParams = array("page");
		CURLHandler::CheckRewriteParams($av_rwParams);  
		$rewriteParams = array();
		$page = GP("page",1);
		if (!is_numeric($page)||($page<1))
		{
			$page=1;
		}
		if ($page>1)
		{
			$rewriteParams["page"] = $page;
		}		
		$count = SQLProvider::ExecuteQuery("select count(1) quan from (
																				select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted
                                            from tbl__userlike ul
                                            join tbl__area_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='area'
                                            where c.active = 1
                                            group by ul.to_resident_id, c.title) rate");
		$count = $count[0]["quan"];
		$pages = floor($count/$this->pageSize)+(($count%$this->pageSize==0)?0:1);
		if (($page>$pages)&&($pages>0))
		{
			$page = $pages;
			$rewriteParams["page"] = $page;
			CURLHandler::Redirect(CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($rewriteParams));
		}
		if ($page==1)
		{
			unset($rewriteParams["page"]);
		}
		
		$rp = ($page-1)*$this->pageSize;
		
	
		// Areas list
		$areas = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
                                            from tbl__userlike ul
                                            join tbl__area_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='area'
                                            where c.active = 1
                                            group by ul.to_resident_id, c.title
                                            order by voted desc, tbl_obj_id limit $rp,$this->pageSize");
		$user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        foreach ($areas as $key=>&$area) {
			$area["num"] = $rp+$key+1;
			$area["resident_type"] = 'area';
            if (!$user->authorized)
                $area["msg_vote"] = "onclick=\"javascript: ShowLikeMessage(); return false;\"";
              else
                $area["msg_vote"] = "";
		}
		
		$catList = $this->GetControl("rateList");
		$catList->dataSource = $areas;
		
		/*
		$mainMenu = $this->GetControl("menu");
            $mainMenu->dataSource["redevent"] =
              array("link"=>"http://redevent.ru/",
                    "imgname"=>"redevent",
                    "title"=>"",
                    "ads_class"=>"reklama",
                    "target"=>'target="_blank"');
		*/
		
		//setting pager
		$pager = $this->GetControl("pager");
		$pager->currentPage = $page;
		$pager->totalPages = $pages;
		$pager->rewriteParams = $rewriteParams;
		
		
	}
}
?>
