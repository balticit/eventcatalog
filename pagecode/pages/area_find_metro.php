<?php
class area_find_metro_php extends CPageCodeHandler
{
  public $stationsByLine = "";
  public $stations = "";
	public $reg_dlg = null;
	public $fa = 0;

	public function area_find_metro_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		header('Content-type: text/html;charset=windows-1251');
    $city = 204;
    if (isset($city))
    {
			$this->reg_dlg = GP("reg",0);
			$this->fa = GP("fa",0);
			if ($this->reg_dlg != 1) {
				$lines = SQLProvider::ExecuteQuery("
					select * from `tbl__metro_lines` 
					where city=$city 
						and area_type is not null 
						and area_coords is not null 
					order by order_num");
				$lll = array();
				foreach ($lines as $line)
					if ($line['area_type'] != 'circle') {
						$line['circle'] = '';
						array_unshift($lll,$line);
					}
					else {
						$line['circle'] = '<area alt="" shape="circle" coords="288,317,130" nohref="">';
						array_push($lll,$line);
					}
				$areas_lines = $this->GetControl("areas_lines");
				$areas_lines->dataSource = $lll;

				$allstations = array();
				foreach ($lines as $line) {				
					$stations = SQLProvider::ExecuteQuery("
						select * from `tbl__metro_stations` 
						where area_type is not null 
							and area_coords is not null 
							and metro_line = ".$line['tbl_obj_id']."
						order by order_num");
					if (sizeof($stations)) {
						$allstations = array_merge($allstations,$stations);
						$stations_str = "";
						foreach($stations as $st) {
							if ($stations_str)
								$stations_str .= ", ";
							$stations_str .= $st['tbl_obj_id'];
							$this->stations .= 'fmStations['.$st['tbl_obj_id'].'] = {"title":"'.$st['title'].'","line":'.$st['metro_line']."};\n";
						}
						$this->stationsByLine .= "fmStationsByLine[".$line['tbl_obj_id']."] = new Array($stations_str);\n";
					}
				}
				$areas = $this->GetControl("areas");
				$areas->dataSource = $allstations;
				$areas = $this->GetControl("markers");
				$areas->dataSource = $allstations;

				$mdistricts = SQLProvider::ExecuteQuery("
					select * from `tbl__moscow_districts` 
					order by order_num");
				
				$moscow_districts = $this->GetControl("districts");
				$moscow_districts->dataSource = $mdistricts;

				$metro_lines = $this->GetControl("metro_lines");
				$metro_lines->dataSource = $lines;
      }

      $mways = SQLProvider::ExecuteQuery("
			  select * from `tbl__moscow_ways`");
      $this->GetControl("mways_maps")->dataSource = $mways;
      $this->GetControl("mways_areas")->dataSource = $mways;
      
      $c_highways = $this->GetControl("mways_highways");
      $c_cities = $this->GetControl("mways_cities");
      foreach ($mways as &$mw) {
        $highways = SQLProvider::ExecuteQuery("
			  select * from `tbl__moscow_highways` 
				where way_id = ".$mw['tbl_obj_id']);
        $cities = SQLProvider::ExecuteQuery("
			  select * from `tbl__moscow_cities` 
				where way_id = ".$mw['tbl_obj_id']);
        $c_highways->dataSource = $highways;
        $c_cities->dataSource = $cities;
        $mw['highways'] = $c_highways->Render();
        $mw['cities'] = $c_cities->Render();
        $mw['cities_height'] = max(sizeof($highways)*20,100);
      }
      $this->GetControl("mways_windows")->dataSource = $mways;

    }
	}
}
?>
