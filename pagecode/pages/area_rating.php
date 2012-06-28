<?php
class area_rating_php extends CPageCodeHandler
{
	public $pageSize = 100;
	
	public function area_rating_php()
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
		$av_rwParams = array("page");
		CURLHandler::CheckRewriteParams($av_rwParams);  
    $app = CApplicationContext::GetInstance();
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
		$items = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
                                            from tbl__userlike ul
                                            join tbl__area_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='area'
                                            where c.active = 1
                                            group by ul.to_resident_id, c.title
                                            order by voted desc, tbl_obj_id limit $rp,$this->pageSize");
		$user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        foreach ($items as $key=>&$item) {
			$item["num"] = $rp+$key+1;
			$item["resident_type"] = 'area';
            if (!$user->authorized)
                $item["msg_vote"] = "onclick=\"javascript: ShowLikeMessage(); return false;\"";
              else
                $item["msg_vote"] = "";
		}
		
		$catList = $this->GetControl("rateList");
		$catList->dataSource = $items;
		
		//setting pager
		$pager = $this->GetControl("pager");
		$pager->currentPage = $page;
		$pager->totalPages = $pages;
		$pager->rewriteParams = $rewriteParams;
		
		//groups
		$groups =  SQLProvider::ExecuteQuery("select
					  sg.`tbl_obj_id` AS `child_id`,
					  sg.`parent_id`,
					  sg.`title`,
					  sg.`title_url`,
					  sg.`priority`
					from
					  `tbl__area_subtypes` sg
					union
					select
					  g.`tbl_obj_id` AS `child_id`,
					  0 AS `parent_id`,
					  g.`title` AS `title`,
					  g.`title_url`,
					  g.`priority`
					from
					  `tbl__area_types` g
					ORDER by priority desc");
		foreach ($groups as $key => $value) {
			$groups[$key]["selected"] = "";
			$groups[$key]["blue"] = "blue";
			$groups[$key]["link"] = "/area/".$value['title_url'];
		}
		$groupList= $this->GetControl("typeList");
		$groupList->dataSource = $groups;
		
		
		// && всего резидентов
		$counts = SQLProvider::ExecuteQuery("select vm.`login_type`, COUNT(*) as `count` from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`<>'user'
												group by vm.`login_type`
												order by vm.`login_type` desc
												");
		
		$chart = array();
		
		$chart["count"] = $counts[0]["count"]+$counts[1]["count"]+$counts[2]["count"]+$counts[3]["count"];
		$chart["cont_count"] = $counts[0]["count"];
		$chart["area_count"] = $counts[2]["count"];
		$chart["arti_count"] = $counts[1]["count"];
		$chart["agen_count"] = $counts[3]["count"];
		$max = max($counts[0]["count"],$counts[1]["count"],$counts[2]["count"],$counts[3]["count"]);
		$h = 70;
		$k = $h/$max;;
		$chart["cont_height"] = floor($counts[0]["count"]*$k);
		$chart["area_height"] = floor($counts[2]["count"]*$k);
		$chart["arti_height"] = floor($counts[1]["count"]*$k);
		$chart["agen_height"] = floor($counts[3]["count"]*$k);
		
		$chart["cont_percent"] = floor($counts[0]["count"]/$chart["count"]*100);
		$chart["area_percent"] = floor($counts[2]["count"]/$chart["count"]*100);
		$chart["arti_percent"] = floor($counts[1]["count"]/$chart["count"]*100);
		$chart["agen_percent"] = floor($counts[3]["count"]/$chart["count"]*100);		
		
		$chartObj = $this->GetControl("chart");
		$chartObj->dataSource = $chart;


	}
}
?>
