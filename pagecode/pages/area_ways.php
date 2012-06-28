<?php
class area_ways_php extends CPageCodeHandler
{
	public function area_ways_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		header('Content-type: text/html;charset=windows-1251');
    
    
		$highways = $this->GetControl("highwayList");
    $highways->dataSource = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__moscow_highways order by title");

		$cities = $this->GetControl("cityList");
    $cities->dataSource = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__moscow_cities order by title");

    $body = $this->GetControl("content");
    $body->dataSource = array(
      "highways_list"=>$highways->RenderHTML(),
      "city_list"=>$cities->RenderHTML()
    );
	}
}
?>
