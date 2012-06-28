<?php
class random_resident_php extends CPageCodeHandler
{
	private $decsriptionSize = 100;
	private $limit_rows = 7;
	
	public function random_resident_php()
	{
		$this->CPageCodeHandler();
	}
	
	private function ProcessDescription(&$data)
	{
		foreach($data as $key => &$val) {			
			$val['description'] = strip_tags($val['description']);
		}
	}

	public function PreRender()
	{
		$av_rwParams = array("type");
		CURLHandler::CheckRewriteParams($av_rwParams);  
    $resident_type = GP('type');
		$data = array();
		switch($resident_type) {
			case "contractor":
				$data = SQLProvider::ExecuteQuery("
				  select title, ifnull(nullif(short_description,''),description) as description, CONCAT('/".$resident_type."/',title_url) link
					from tbl__".$resident_type."_doc order by rand() limit ".$this->limit_rows);
				break;
			case "area":	
			case "artist":
			case "agency":
				$data = SQLProvider::ExecuteQuery("
				  select title, description, CONCAT('/".$resident_type."/',title_url) link
					from tbl__".$resident_type."_doc order by rand() limit ".$this->limit_rows);
				break;
		}
		$this->ProcessDescription($data);
		$this->GetControl("residents")->dataSource = $data;
	}
}
?>