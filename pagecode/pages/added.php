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
		
		
		
		$newAreasweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='area' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		$newAreasweek2 = $this->GetControl("newAreasweek");
		$newAreasweek2->dataSource = $newAreasweek;	
		
		$areaCount = array();
		$areaCount["count"] = sizeOf($newAreasweek);
		$areaCountObj = $this->GetControl("areacount");
		$areaCountObj->dataSource = $areaCount;

		$newContractorweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='contractor' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		$newContractorsweek = $this->GetControl("newContractorsweek");
		$newContractorsweek->dataSource = $newContractorweek;
		
		$contractorCount = array();
		$contractorCount["count"] = sizeOf($newContractorweek);
		$contractorCountObj = $this->GetControl("contractorcount");
		$contractorCountObj->dataSource = $contractorCount;

		$newArtistweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='artist' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		$newArtistsweek = $this->GetControl("newArtistsweek");
		$newArtistsweek->dataSource = $newArtistweek;
		
		$artistCount = array();
		$artistCount["count"] = sizeOf($newArtistweek);
		$artistCountObj = $this->GetControl("artistcount");
		$artistCountObj->dataSource = $artistCount;
		
		$newAgencyweek = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='agency' and
												vm.`registration_date`>=SUBDATE(NOW(),7)
												order by vm.`registration_date` DESC
												");
		$newAgenciesweek = $this->GetControl("newAgenciesweek");
		$newAgenciesweek->dataSource = $newAgencyweek;
		
		$agencyCount = array();
		$agencyCount["count"] = sizeOf($newAgencyweek);
		$agencyCountObj = $this->GetControl("agencycount");
		$agencyCountObj->dataSource = $agencyCount;
	
		
		$newAreasmonth = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='area' and
												vm.`registration_date`>=SUBDATE(NOW(),30)
												order by vm.`registration_date` DESC
												");
		$newAreasmonth2 = $this->GetControl("newAreasmonth");
		$newAreasmonth2->dataSource = $newAreasmonth;	
		
		$areaCountmonth = array();
		$areaCountmonth["count"] = sizeOf($newAreasmonth);
		$areaCountmonthObj = $this->GetControl("areacountmonth");
		$areaCountmonthObj->dataSource = $areaCountmonth;

		$newContractormonth = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='contractor' and
												vm.`registration_date`>=SUBDATE(NOW(),30)
												order by vm.`registration_date` DESC
												");
		$newContractorsmonth = $this->GetControl("newContractorsmonth");
		$newContractorsmonth->dataSource = $newContractormonth;
		
		
		$contractorCountmonth = array();
		$contractorCountmonth["count"] = sizeOf($newContractormonth);
		$contractorCountmonthObj = $this->GetControl("contractorcountmonth");
		$contractorCountmonthObj->dataSource = $contractorCountmonth;

		$newArtistmonth = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='artist' and
												vm.`registration_date`>=SUBDATE(NOW(),30)
												order by vm.`registration_date` DESC
												");
		$newArtistsmonth = $this->GetControl("newArtistsmonth");
		$newArtistsmonth->dataSource = $newArtistmonth;
		
		$artistCountmonth = array();
		$artistCountmonth["count"] = sizeOf($newArtistmonth);
		$artistCountmonthObj = $this->GetControl("artistcountmonth");
		$artistCountmonthObj->dataSource = $artistCountmonth;
		
		$newAgencymonth = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`='agency' and
												vm.`registration_date`>=SUBDATE(NOW(),30)
												order by vm.`registration_date` DESC
												");
		$newAgenciesmonth = $this->GetControl("newAgenciesmonth");
		$newAgenciesmonth->dataSource = $newAgencymonth;
		
		$agencyCountmonth = array();
		$agencyCountmonth["count"] = sizeOf($newAgencymonth);
		$agencyCountmonthObj = $this->GetControl("agencycountmonth");
		$agencyCountmonthObj->dataSource = $agencyCountmonth;
		
		
		
		
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
