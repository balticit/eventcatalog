<?php
class rating_php extends CPageCodeHandler
{
	public $pageSize = 50;

	public function rating_php()
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
                                            join tbl__contractor_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='contractor'
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
		
		// Contractors list
		$contractors = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
                                            from tbl__userlike ul
                                            join tbl__contractor_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='contractor'
                                            where c.active = 1
                                            group by ul.to_resident_id, c.title
                                            order by voted desc, tbl_obj_id limit $rp,$this->pageSize");
		$user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        foreach ($contractors as $key=>&$contractor) {
			$contractor["num"] = $rp+$key+1;
			$contractor["resident_type"] = 'contractor';
            if (!$user->authorized)
                $contractor["msg_vote"] = "onclick=\"javascript: ShowLikeMessage(); return false;\"";
              else
                $contractor["msg_vote"] = "";
		}
		
		$catList = $this->GetControl("rateListCont");
		$catList->dataSource = $contractors;
		
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
		
		$catList = $this->GetControl("rateListArea");
		$catList->dataSource = $areas;
		
		// Artists list
		$artists = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
                                            from tbl__userlike ul
                                            join tbl__artist_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='artist'
                                            where c.active = 1
                                            group by ul.to_resident_id, c.title
                                            order by voted desc, tbl_obj_id limit $rp,$this->pageSize");
		$user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        foreach ($artists as $key=>&$artist) {
			$artist["num"] = $rp+$key+1;
			$artist["resident_type"] = 'artist';
            if (!$user->authorized)
                $artist["msg_vote"] = "onclick=\"javascript: ShowLikeMessage(); return false;\"";
              else
                $artist["msg_vote"] = "";
		}
		
		$catList = $this->GetControl("rateListArtist");
		$catList->dataSource = $artists;
		
		// Agencys list
		$agencys = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
                                            from tbl__userlike ul
                                            join tbl__agency_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='agency'
                                            where c.active = 1
                                            group by ul.to_resident_id, c.title
                                            order by voted desc, tbl_obj_id limit $rp,$this->pageSize");
		$user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        foreach ($agencys as $key=>&$agency) {
			$agency["num"] = $rp+$key+1;
			$agency["resident_type"] = 'agency';
            if (!$user->authorized)
                $agency["msg_vote"] = "onclick=\"javascript: ShowLikeMessage(); return false;\"";
              else
                $agency["msg_vote"] = "";
		}
		
		$catList = $this->GetControl("rateListAgency");
		$catList->dataSource = $agencys;
		
		
		$mainMenu = $this->GetControl("menu");
            $mainMenu->dataSource["redevent"] =
              array("link"=>"http://redevent.ru/",
                    "imgname"=>"redevent",
                    "title"=>"",
                    "ads_class"=>"reklama",
                    "target"=>'target="_blank"');
		
		//setting pager
		$pager = $this->GetControl("pager");
		$pager->currentPage = $page;
		$pager->totalPages = $pages;
		$pager->rewriteParams = $rewriteParams;
		
		
	}
}
?>
