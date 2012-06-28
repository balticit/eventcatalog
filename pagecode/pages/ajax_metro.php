<?php
	class ajax_metro_php extends CPageCodeHandler
	{

		public function ajax_metro_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
      header('Content-type: text/html;charset=windows-1251');
      $city = GP("forcity");
      $html = "";
			if (isset($city))
			{
				$lines = SQLProvider::ExecuteQuery("select * from `tbl__metro_lines` where city=$city order by order_num");
				$html = '<div style="width: 300px; height: 500px; overflow: auto;"><div id="metrolines">';
				foreach ($lines as $key=>$line) {
          if ($key > 0)
            $html .= '<br>';
          $html .= '<div class="metro-line" style="color: #'.$line['color'].'">'.$line['title'].' линия</div>';
          $stations = SQLProvider::ExecuteQuery("select * from `tbl__metro_stations`
                                           where metro_line=".$line['tbl_obj_id']." order by order_num");
          foreach ($stations as $st) {
            $html .= '<input type="checkbox" class="metro-station" id="ms'.$st['tbl_obj_id'].
                     '" value="'.$st['tbl_obj_id'].'" onchange="ChangeCheck(this, \''.$st['title'].'\')">
              <label for="ms'.$st['tbl_obj_id'].'">'.$st['title'].'</label><br>';
          }

				}
				$html.="</div></div>";
      }
      $body = $this->GetControl("body");
			$body->dataSource = array("list"=>$html);
		}
	}
?>
