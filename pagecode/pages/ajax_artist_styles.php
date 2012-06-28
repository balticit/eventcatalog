<?php
	class ajax_artist_styles_php extends CPageCodeHandler
	{

		public function ajax_artist_styles_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
      header('Content-type: text/html;charset=windows-1251');  
      $styles_q = SQLProvider::ExecuteQuery(
        "select * from `tbl__styles` order by title");				
      $styles = array();
      foreach ($styles_q as $stl) {
        if (!isset($styles[$stl['style_group']]))
          $styles[$stl['style_group']] = array();
        array_push($styles[$stl['style_group']],$stl);
      }                 
      
      $btns_q = SQLProvider::ExecuteQuery(
        "select * from tbl__styles_groups order by title");				      
      $groups_list = "";
      $st_list = $this->GetControl("list");
      foreach ($btns_q as $key => &$btn) {
        if ($key == 0) {
          $btn['class'] = 'art_s_btn_active';
          $vis = "";
        }  
        else {
          $btn['class'] = 'art_s_btn_noactive';
          $vis = "display: none;";
        }  
        
        $st_list->dataSource = $styles[$btn['tbl_obj_id']];
        $groups_list .= '<div id = "art_s_style_g'.$btn['tbl_obj_id'].'" style="'.$vis.'">'.$st_list->RenderHTML().'</div>';
      }
      $btns = $this->GetControl("btns");
      $btns->dataSource = $btns_q;
      $body = $this->GetControl("body");        
      $body->dataSource = array("btns"=>$btns->RenderHTML(),
                                "groups_list"=>$groups_list);
		}
	}
?>
