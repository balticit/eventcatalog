<?php
class added_php extends CPageCodeHandler
{

	public function added_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$av_rwParams = array();
		CURLHandler::CheckRewriteParams($av_rwParams);   
    
    $newRegs = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`<>'user' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		$newRegistered = $this->GetControl("newRegistered");
		$newRegistered->dataSource = $newRegs;
		
		$newRegs2 = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`<>'user' and
												vm.`registration_date`<SUBDATE(NOW(),7) and
												vm.`registration_date`>SUBDATE(NOW(),31)
												order by vm.`registration_date` DESC
												limit 0,20");
		$newRegistered2 = $this->GetControl("newRegistered2");
		
		$newRegistered2->dataSource = $newRegs2;	
		
		$newRegs3 = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`<>'user' and
												vm.`registration_date`<SUBDATE(NOW(),7) and
												vm.`registration_date`>SUBDATE(NOW(),30)
												order by vm.`registration_date` DESC
												");
		$newRegistered3 = $this->GetControl("newRegistered3");
		
		$newRegistered3->dataSource = $newRegs3;
		
		$monthCount = array();
		$monthCount["count"] = sizeOf($newRegs3);
		$monthCountObj = $this->GetControl("monthcount");
		$monthCountObj->dataSource = $monthCount;
		
		$newAreas = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='area' 
												order by vm.`registration_date` DESC limit 0,10
												");
		$newAreas2 = $this->GetControl("newAreas");
		$newAreas2->dataSource = $newAreas;	

		$newContractor = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='contractor' 
												order by vm.`registration_date` DESC limit 0,10
												");
		$newContractors = $this->GetControl("newContractors");
		$newContractors->dataSource = $newContractor;

		$newArtist = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='artist' 
												order by vm.`registration_date` DESC limit 0,10
												");
		$newArtists = $this->GetControl("newArtists");
		$newArtists->dataSource = $newArtist;
		
		$newAgency = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='agency' 
												order by vm.`registration_date` DESC limit 0,10
												");
		$newAgencies = $this->GetControl("newAgencies");
		$newAgencies->dataSource = $newAgency;
		
		
		/* WEEK */
	/*	$newAreasweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='area' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		*/
    $newAreasweek = SQLProvider::ExecuteQuery("select ar.tbl_obj_id, DATE_FORMAT(ar.registration_date,'%d.%m.%y') as strdate, ar.title, ar.description, ar.logo as logo_image, 'area' resident_type, ar.title_url
        from `tbl__area_doc` ar
        where ar.active = 1 and ar.registration_date>=SUBDATE(NOW(),7)
        order by ar.registration_date DESC
    ");
    										
		$newAreasweek2 = $this->GetControl("newAreasweek");
		$newAreasweek2->dataSource = $newAreasweek;	
		
		$areaCount = array();
		$areaCount["count"] = sizeOf($newAreasweek);
		$areaCountObj = $this->GetControl("areacount");
		$areaCountObj->dataSource = $areaCount;
    
		/*$newContractorweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='contractor' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		*/
		$newContractorweek = SQLProvider::ExecuteQuery("select cont.tbl_obj_id, DATE_FORMAT(cont.registration_date,'%d.%m.%y') as strdate, cont.title, cont.description, cont.logo_image as logo_image, 'contractor' resident_type, cont.title_url
        from `tbl__contractor_doc` cont
        where cont.active = 1 and cont.registration_date>=SUBDATE(NOW(),7)
        order by cont.registration_date DESC
    ");
		
		$newContractorsweek = $this->GetControl("newContractorsweek");
		$newContractorsweek->dataSource = $newContractorweek;
		
		$contractorCount = array();
		$contractorCount["count"] = sizeOf($newContractorweek);
		$contractorCountObj = $this->GetControl("contractorcount");
		$contractorCountObj->dataSource = $contractorCount;

		/*$newArtistweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='artist' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		*/										
		$newArtistweek = SQLProvider::ExecuteQuery("select art.tbl_obj_id, DATE_FORMAT(art.registration_date,'%d.%m.%y') as strdate, art.title, art.description, art.logo as logo_image, 'artist' resident_type, art.title_url
        from `tbl__artist_doc` art
        where art.active = 1 and art.registration_date>=SUBDATE(NOW(),7)
        order by art.registration_date DESC
    ");
    
		$newArtistsweek = $this->GetControl("newArtistsweek");
		$newArtistsweek->dataSource = $newArtistweek;
		
		$artistCount = array();
		$artistCount["count"] = sizeOf($newArtistweek);
		$artistCountObj = $this->GetControl("artistcount");
		$artistCountObj->dataSource = $artistCount;
		
		/*$newAgencyweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='agency' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		*/										
		$newAgencyweek = SQLProvider::ExecuteQuery("select ag.tbl_obj_id, DATE_FORMAT(ag.registration_date,'%d.%m.%y') as strdate, ag.title, ag.description, ag.logo_image as logo_image, 'agency' resident_type, ag.title_url
        from `tbl__agency_doc` ag
        where ag.active = 1 and ag.registration_date>=SUBDATE(NOW(),7)
        order by ag.registration_date DESC
    ");
		
		$newAgenciesweek = $this->GetControl("newAgenciesweek");
		$newAgenciesweek->dataSource = $newAgencyweek;
		
		$agencyCount = array();
		$agencyCount["count"] = sizeOf($newAgencyweek);
		$agencyCountObj = $this->GetControl("agencycount");
		$agencyCountObj->dataSource = $agencyCount;
	
		
		$newAreasmonth = SQLProvider::ExecuteQuery("select ar.tbl_obj_id, DATE_FORMAT(ar.registration_date,'%d.%m.%y') as strdate, ar.title, ar.description, ar.logo as logo_image, 'area' resident_type, ar.title_url
        from `tbl__area_doc` ar
        where ar.active = 1 and ar.registration_date>=SUBDATE(NOW(),30)
				order by ar.registration_date DESC
		");
		$newAreasmonth2 = $this->GetControl("newAreasmonth");
		$newAreasmonth2->dataSource = $newAreasmonth;	
		
		$areaCountmonth = array();
		$areaCountmonth["count"] = sizeOf($newAreasmonth);
		$areaCountmonthObj = $this->GetControl("areacountmonth");
		$areaCountmonthObj->dataSource = $areaCountmonth;

		$newContractormonth = SQLProvider::ExecuteQuery("select cont.tbl_obj_id, DATE_FORMAT(cont.registration_date,'%d.%m.%y') as strdate, cont.title, cont.description, cont.logo_image as logo_image, 'contractor' resident_type, cont.title_url
        from `tbl__contractor_doc` cont
        where cont.active = 1 and cont.registration_date>=SUBDATE(NOW(),30)
				order by cont.registration_date DESC
		");
		$newContractorsmonth = $this->GetControl("newContractorsmonth");
		$newContractorsmonth->dataSource = $newContractormonth;
		
		
		$contractorCountmonth = array();
		$contractorCountmonth["count"] = sizeOf($newContractormonth);
		$contractorCountmonthObj = $this->GetControl("contractorcountmonth");
		$contractorCountmonthObj->dataSource = $contractorCountmonth;

		$newArtistmonth = SQLProvider::ExecuteQuery("select art.tbl_obj_id, DATE_FORMAT(art.registration_date,'%d.%m.%y') as strdate, art.title, art.description, art.logo as logo_image, 'artist' resident_type, art.title_url
        from `tbl__artist_doc` art
        where art.active = 1 and art.registration_date>=SUBDATE(NOW(),30)
				order by art.registration_date DESC
		");
		$newArtistsmonth = $this->GetControl("newArtistsmonth");
		$newArtistsmonth->dataSource = $newArtistmonth;
		
		$artistCountmonth = array();
		$artistCountmonth["count"] = sizeOf($newArtistmonth);
		$artistCountmonthObj = $this->GetControl("artistcountmonth");
		$artistCountmonthObj->dataSource = $artistCountmonth;
		
		$newAgencymonth = SQLProvider::ExecuteQuery("select ag.tbl_obj_id, DATE_FORMAT(ag.registration_date,'%d.%m.%y') as strdate, ag.title, ag.description, ag.logo_image as logo_image, 'agency' resident_type, ag.title_url
        from `tbl__agency_doc` ag
        where ag.active = 1 and ag.registration_date>=SUBDATE(NOW(),30)
				order by ag.registration_date DESC
		");
		$newAgenciesmonth = $this->GetControl("newAgenciesmonth");
		$newAgenciesmonth->dataSource = $newAgencymonth;
		
		$agencyCountmonth = array();
		$agencyCountmonth["count"] = sizeOf($newAgencymonth);
		$agencyCountmonthObj = $this->GetControl("agencycountmonth");
		$agencyCountmonthObj->dataSource = $agencyCountmonth;
		
		
		$mainMenu = $this->GetControl("menu");
            $mainMenu->dataSource["redevent"] =
              array("link"=>"http://redevent.ru/",
                    "imgname"=>"redevent",
                    "title"=>"",
                    "ads_class"=>"reklama",
                    "target"=>'target="_blank"');
		
		$counts = SQLProvider::ExecuteQuery("select vm.`login_type`, COUNT(*) as `count` from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`<>'user'
												group by vm.`login_type`
												order by vm.`login_type` desc
												");
		//print_r($counts);
		
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
