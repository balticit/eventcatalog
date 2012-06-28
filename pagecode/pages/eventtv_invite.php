<?php
class eventtv_invite_php extends CPageCodeHandler
{
	public function eventtv_invite_php()
	{
		$this->CPageCodeHandler();
	}
	
	public function PreRender()
	{
		$av_rwParams = array();
		CURLHandler::CheckRewriteParams($av_rwParams); 
		$topics = SQLProvider::ExecuteQuery("select * from tbl__eventtv_topics order by order_num");
        array_unshift($topics,array("tbl_obj_id"=>"0","title"=>"Все репортажи"));
        foreach ($topics as &$item) {
            if ($item['tbl_obj_id'] == 0)
                $item['link'] = "/eventtv";
            else
                $item['link'] = "/eventtv/topic/".$item['tbl_obj_id'];
            $item['gray'] = "gray";
            $item['selected'] = "";
        }
		$topicList = $this->GetControl("topicList");
		$topicList->dataSource = $topics;
		
		
	}
}
?>
