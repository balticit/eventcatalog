<?php
	class star_calendar_day_php extends CPageCodeHandler 
	{
		public function star_calendar_day_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender() {
			
			$day = (int)GP("d",strtotime(date("Y-m-d")));
			$day_of_month = date("d",$day);
			$year = date("Y",$day);
			$month = date("m",$day);
			$today = strtotime(date("Y-m-d"));
			
			$week_day = "";
			switch (date("w",$day)) {
				case "1" :
					$week_day = "��";
				break;
				case "2" :
					$week_day = "��";
				break;
				case "3" :
					$week_day = "��";
				break;
				case "4" :
					$week_day = "��";
				break;
				case "5" :
					$week_day = "��";
				break;
				case "6" :
					$week_day = "��";
				break;
				case "0" :
					$week_day = "��";
				break;

			}
			
			$month_title = "";
			switch ($month) {
				case "01" : 
					$month_title = "������";
				break;	
				case "02" : 
					$month_title = "�������";
				break;
				case "03" : 
					$month_title = "�����";
				break;
				case "04" : 
					$month_title = "������";
				break;
				case "05" : 
					$month_title = "���";
				break;
				case "06" : 
					$month_title = "����";
				break;
				case "07" : 
					$month_title = "����";
				break;
				case "08" : 
					$month_title = "�������";
				break;
				case "09" : 
					$month_title = "��������";
				break;
				case "10" : 
					$month_title = "�������";
				break;
				case "11" : 
					$month_title = "������";
				break;
				case "12" : 
					$month_title = "�������";
				break;
			}
			
			$calendar = array();
			$calendar["year"] = $year;
			$calendar["month"] = $month;
			$calendar["day_title"] = "$day_of_month $month_title / $week_day";
			
			$gastr =  SQLProvider::ExecuteQuery("select * from `vw__star_calendar` where DATE_FORMAT(date,'%y-%m-%d')='".date("y-m-d",$day)."'");
			$calendar["calendar"] = "";
			foreach ($gastr as $key=>$elem) {
				if ($key%2==0) 
					$calendar["calendar"] .= "<tr style=\"background-color: #cccccc;\">";
				else 
					$calendar["calendar"] .= "<tr>";
				
				if ($elem["is_resident"]==1)
					$calendar["calendar"] .= "<td width=190><a href=/artist/details/id/".$elem["artist_id"]." style=\"color: magenta;\">".$elem["title"]."</a></td>";
				else
					$calendar["calendar"] .= "<td width=190><span style=\"color: magenta;\">".$elem["title"]."</span></td>";
					
				$calendar["calendar"] .= "<td width=290>".$elem["city"]."</td>";
					
				$calendar["calendar"] .= "</tr>";
			}

			$month_calendar = $this->GetControl("day_calendar");
			$month_calendar->dataSource = $calendar;
			
		}
		
	} 
?>