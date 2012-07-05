<?php
class res_news_news_php extends CPageCodeHandler
{

	public function res_news_news_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{		
		$id = GP('id',0); 
		
		// $newAreas = SQLProvider::ExecuteQuery("select rn.*, res.title as resident_name, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn 
												// LEFT JOIN tbl__contractor_doc res ON res.tbl_obj_id = rn.resident_id 
												// where rn.active=1 and rn.resident_type='area'
												// order by rn.date DESC limit 0,3
												// ");
		// if (!empty($newAreas)) {
			// foreach($newAreas as $i => &$item) {
				// $item['short_text'] = htmlspecialchars_decode(substr(strip_tags($item['text']), 0, 200));
			// } 
		// } 	
		

		// $newContractor = SQLProvider::ExecuteQuery("select rn.*, res.title as resident_name, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn 
													// LEFT JOIN tbl__contractor_doc res ON res.tbl_obj_id = rn.resident_id  
													// where rn.active=1 and rn.resident_type='contractor'  
													// order by rn.date DESC limit 0,3
													// ");
		// if (!empty($newContractor)) {
			// foreach($newContractor as $i => &$item) {
				// $item['short_text'] = htmlspecialchars_decode(substr(strip_tags($item['text']), 0, 200));
			// }
		// } 		
		
		

		// $newArtist = SQLProvider::ExecuteQuery("select rn.*, res.title as resident_name, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn 
												// LEFT JOIN tbl__artist_doc res ON res.tbl_obj_id = rn.resident_id 
												// where rn.active=1 and rn.resident_type='artist' 
												// order by rn.date DESC limit 0,3
												// ");
		// if (!empty($newArtist)) {
			// foreach($newArtist as $i => &$item) { 
				// $item['short_text'] = htmlspecialchars_decode(substr(strip_tags($item['text']), 0, 200));
			// }
		// } 	
		

		// $newAgency = SQLProvider::ExecuteQuery("select rn.*, res.title as resident_name, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn 
												// LEFT JOIN tbl__artist_doc res ON res.tbl_obj_id = rn.resident_id 
												// where rn.active=1 and rn.resident_type='agency'
												// order by rn.date DESC limit 0,3
												// ");
		// if (!empty($newAgency)) {
			// foreach($newAgency as $i => &$item) { 
				// $item['short_text'] = htmlspecialchars_decode(substr(strip_tags($item['text']), 0, 200));
			// }
		// } 		
		
		// $newContractors = $this->GetControl("newContractors");  
		// $newContractors->dataSource = $newContractor;
		// $newAreas2 = $this->GetControl("newAreas");
		// $newAreas2->dataSource = $newAreas;
		// $newArtists = $this->GetControl("newArtists");
		// $newArtists->dataSource = $newArtist;				
		// $newAgencies = $this->GetControl("newAgencies");
		// $newAgencies->dataSource = $newAgency;
		
		// менюшка
		$newsdetails = SQLProvider::ExecuteQuery("select *, DATE_FORMAT(date, '%d.%m.%Y %H:%i:%S') as date from tbl__resident_news where tbl_obj_id=$id");
		//$residentTitle = SQLProvider::ExecuteQuery('SELECT * FROM ');
		//var_dump($newsdetails);
		$user = array();	
		//$newsdetails[0]['show_agency'] = $newsdetails[0]['show_contractor'] = $newsdetails[0]['show_area'] = $newsdetails[0]['show_artist'] = 'class="noDisplay"';
		switch ($newsdetails[0]["resident_type"]) {
			case "contractor" :
				$user = SQLProvider::ExecuteQuery("select * from tbl__contractor_doc where tbl_obj_id=".$newsdetails[0]["resident_id"]);
				$newsdetails[0]["type"] = "<span style=\"color: #f05620;\">Новость подрядчика: ".$user[0]['title']."</span>";
				$newsdetails[0]["logo"] = $newsdetails[0]['logo_image'];
				$newsdetails[0]["title_url"] = $user[0]['title_url'];	
				//var_dump($newsdetails[0]['date']);
				$diffDate = strtotime(date('Y-m-d H:i:s')) - strtotime($newsdetails[0]['date']);
				$newsdetails[0]["newsBoard"] = ($diffDate > 3600) ? ($diffDate < 72000) ? 'Новость добавлена<br />'.ceil($diffDate/3600).' часов назад' : 'Новость добавлена<br />'.substr($newsdetails[0]['date'], 0, 10).' г.' : 'Новость добавлена<br />'.ceil($diffDate/60).' минут назад';
				//$newsdetails[0]['show_contractor'] = '';
			break;
			case "area" :
				$user = SQLProvider::ExecuteQuery("select * from tbl__area_doc where tbl_obj_id=".$newsdetails[0]["resident_id"]);
				$newsdetails[0]["type"] = "<span style=\"color: #3399ff;\">Новость площадки: ".$user[0]['title']."</span>";
				$newsdetails[0]["logo"] = $newsdetails[0]['logo'];
				$newsdetails[0]["title_url"] = $user[0]['title_url'];				
				$diffDate = strtotime(date('Y-m-d H:i:s')) - strtotime($newsdetails[0]['date']);
				$newsdetails[0]["newsBoard"] = ($diffDate > 3600) ? ($diffDate < 72000) ? 'Новость добавлена<br />'.ceil($diffDate/3600).' часов назад' : 'Новость добавлена<br />'.substr($newsdetails[0]['date'], 0, 10).' г.' : 'Новость добавлена<br />'.ceil($diffDate/60).' минут назад';
				//$newsdetails[0]['show_area'] = '';
			break;
			case "artist" :
				$user = SQLProvider::ExecuteQuery("select * from tbl__artist_doc where tbl_obj_id=".$newsdetails[0]["resident_id"]);
				$newsdetails[0]["type"] = "<span style=\"color: #ff0066;\">Новость артиста: ".$user[0]['title']."</span>";
				$newsdetails[0]["logo"] = $newsdetails[0]['logo'];
				$newsdetails[0]["title_url"] = $user[0]['title_url'];				
				$diffDate = strtotime(date('Y-m-d H:i:s')) - strtotime($newsdetails[0]['date']);
				$newsdetails[0]["newsBoard"] = ($diffDate > 3600) ? ($diffDate < 72000) ? 'Новость добавлена<br />'.ceil($diffDate/3600).' часов назад' : 'Новость добавлена<br />'.substr($newsdetails[0]['date'], 0, 10).' г.' : 'Новость добавлена<br />'.ceil($diffDate/60).' минут назад';
				//$newsdetails[0]['show_artist'] = '';
			break;
			case "agency" :
				$user = SQLProvider::ExecuteQuery("select * from tbl__agency_doc where tbl_obj_id=".$newsdetails[0]["resident_id"]);
				$newsdetails[0]["type"] = "<span style=\"color: #99cc00;\">Новость агентства: ".$user[0]['title']."</span>";
				$newsdetails[0]["logo"] = $newsdetails[0]['logo_image'];
				$newsdetails[0]["title_url"] = $user[0]['title_url'];				
				$diffDate = strtotime(date('Y-m-d H:i:s')) - strtotime($newsdetails[0]['date']);
				$newsdetails[0]["newsBoard"] = ($diffDate > 3600) ? ($diffDate < 72000) ? 'Новость добавлена<br />'.ceil($diffDate/3600).' часов назад' : 'Новость добавлена<br />'.substr($newsdetails[0]['date'], 0, 10).' г.' : 'Новость добавлена<br />'.ceil($diffDate/60).' минут назад';
				//$newsdetails[0]['show_agency'] = '';
			break;
		}
		$resident = $newsdetails[0]["resident_type"];
		$menus = array(
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'contractor')),'title'=>'Новости подрядчиков','selected'=>($resident=='contractor'?'class="selected"':''),'gray'=>'gray','color'=>'F05620;'),
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'area')),'title'=>'Новости площадок','parent_id'=>0,'priority'=>4,'child_id'=>4,'selected'=>($resident=='area'?'class="selected"':''),'gray'=>'gray','color'=>'3399ff;'),
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'artist')),'title'=>'Новости артистов','parent_id'=>0,'priority'=>2,'child_id'=>2,'selected'=>($resident=='artist'?'class="selected"':''),'gray'=>'gray','color'=>'ff0066;'),
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'agency')),'title'=>'Новости агентств','parent_id'=>0,'priority'=>1,'child_id'=>1,'selected'=>($resident=='agency'?'class="selected"':''),'gray'=>'gray','color'=>'99cc00;'),
        );
		$groupList = $this->GetControl("typeList");		
        $groupList->dataSource = $menus;
		
		
		
		//Другие новости
		$otherNews = SQLProvider::ExecuteQuery("select rn.*, res.title as resident_name, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn 
												LEFT JOIN tbl__".$newsdetails[0]["resident_type"]."_doc res ON res.tbl_obj_id = rn.resident_id 
												where rn.active=1 and rn.resident_type='".$newsdetails[0]["resident_type"]."' 
												AND rn.resident_id = ".$newsdetails[0]["resident_id"]." 
												order by rn.date DESC limit 10
												");
		// echo "select rn.*, res.title as resident_name, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn 
												// LEFT JOIN tbl__".$newsdetails[0]["resident_type"]."_doc res ON res.tbl_obj_id = rn.resident_id 
												// where rn.active=1 and rn.resident_type='".$newsdetails[0]["resident_type"]."' 
												// AND rn.resident_id = ".$newsdetails[0]["resident_id"]." 
												// order by rn.date DESC limit 10
												// "; die('!');
		if (!empty($otherNews)) {
			foreach($otherNews as $i => &$item) { 
				$item['short_text'] = htmlspecialchars_decode(substr(strip_tags($item['text']), 0, 200));
			}
		}
		$otherNews2 = $this->GetControl("otherNews");
		$otherNews2->dataSource = $otherNews;
				
				
		$newsdetails[0]["company_name"] = $user[0]['title'];
		$photos = SQLProvider::ExecuteQuery(
      "select p.*
			from `tbl__resident_news2photo`  ap
			join `tbl__photo` p on ap.photo_id = p.tbl_obj_id
			where news_id=$id limit 8");
    $newsdetails[0]["photo_visible"] = !empty($photos)?'':'display:none;';
    $photosObj = $this->GetControl("photos");
    $photosObj->dataSource = $photos;
		$newsdetails[0]["photos"] = $photosObj->Render();
		//if(sizeof($photos)) die('!');
		$details = $this->GetControl("details");
		$details->dataSource = $newsdetails[0];
		
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