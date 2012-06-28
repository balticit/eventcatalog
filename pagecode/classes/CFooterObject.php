<?php
	class CFooterObject extends CHTMLObject
	{
		public $template;

    public $additionalText;

		public function CFooterObject($template = "",$data = array())
		{
			$contractor_html = "";
            $items = SQLProvider::ExecuteQuery("
                select tl1.title parent_title, tl2.parent_id, tl2.tbl_obj_id, tl2.title
                from tbl__activity_type tl1
                left join tbl__activity_type tl2 on tl2.parent_id = tl1.tbl_obj_id
                where tl1.parent_id = 0 order by tl1.priority desc, tl2.priority desc");
            $old_parent = 0;
            foreach ($items as $item) {
                if ($item['parent_id'] != $old_parent) {
                    if ($old_parent)
                        $contractor_html .= '<br>';
                    $contractor_html .=
                        '<a href="/contractor/?activity='.$item['parent_id'].'" class="common_footer"><b>'.$item['parent_title'].':</b></a> ';
                }
                else {
                    $contractor_html .= ', ';
                }
                $contractor_html .=
                    '<a href="/contractor/?activity='.$item['tbl_obj_id'].'" class="common_footer">'.$item['title'].'</a>';
                $old_parent = $item['parent_id'];
            }

            $area_html = "";
            $items = SQLProvider::ExecuteQuery("
                SELECT tl1.title parent_title, tl2.parent_id, tl2.tbl_obj_id, tl2.title
                FROM tbl__area_types tl1
                left join tbl__area_subtypes tl2 on tl2.parent_id = tl1.tbl_obj_id
                order by tl1.priority desc, tl1.tbl_obj_id, tl2.priority desc");
            $old_parent = 0;
            foreach ($items as $item) {
                if ($item['parent_id'] != $old_parent) {
                    if ($old_parent)
                        $area_html .= '<br>';
                    $area_html .=
                        '<a href="/area/?type='.$item['parent_id'].'" class="common_footer"><b>'.$item['parent_title'].':</b></a> ';
                }
                else {
                    $area_html .= ', ';
                }
                $area_html .=
                    '<a href="/area/?type='.$item['parent_id'].'&subtype='.$item['tbl_obj_id'].'" class="common_footer">'.$item['title'].'</a>';
                $old_parent = $item['parent_id'];
            }

            $artist_html = "";
            $items = SQLProvider::ExecuteQuery("
                SELECT tl1.title parent_title, tl2.parent_id, tl2.tbl_obj_id, tl2.title
                FROM tbl__artist_group tl1
                left join tbl__artist_subgroup tl2 on tl2.parent_id = tl1.tbl_obj_id
                order by tl1.priority desc, tl1.tbl_obj_id, tl2.priority desc");
            $old_parent = 0;
            foreach ($items as $item) {
                if ($item['parent_id'] != $old_parent) {
                    if ($old_parent)
                        $artist_html .= '<br>';
                    $artist_html .=
                        '<a href="/artist/?group='.$item['parent_id'].'" class="common_footer"><b>'.$item['parent_title'].':</b></a> ';
                }
                else {
                    $artist_html .= ', ';
                }
                $artist_html .=
                    '<a href="/artist/?group='.$item['parent_id'].'&subgroup='.$item['tbl_obj_id'].'" class="common_footer">'.$item['title'].'</a>';
                $old_parent = $item['parent_id'];
            }

            $agency_html = "";
            $items = SQLProvider::ExecuteQuery("
                select tbl_obj_id, title from  tbl__agency_type order by priority desc");
            foreach ($items as $item) {
                if ($agency_html)
                    $agency_html .= ', ';
                $agency_html .=
                    '<a href="/agency/?activity='.$item['tbl_obj_id'].'" class="common_footer">'.$item['title'].'</a>';
            }
            $this->CHTMLObject();
			$this->template = $template;
			$footer_mini = GetParam('footer_type',"cs",1) == 1;
			$this->dataSource = array("contractor"=>$contractor_html,
                                "area"=>$area_html,
                                "artist"=>$artist_html,
                                "agency"=>$agency_html,
                                "btn_text"=>($footer_mini?"развернуть":"свернуть"),
                                "btn_event"=>($footer_mini?"ShowSublevel()":"HideSublevel()"),
                                "sublevel_vis"=>($footer_mini?"display:none":""),
								"year"=>date('Y'));
		}

		public function RenderHTML()
		{
			$this->dataSource = array_merge($this->dataSource,array("additionalText"=>$this->additionalText));
			return CStringFormatter::Format($this->template,$this->GetDataSourceData());
		}
	}
?>
