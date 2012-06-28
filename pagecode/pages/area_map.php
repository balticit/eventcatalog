<?php
class area_map_php extends CPageCodeHandler
{
	public function area_map_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$app = CApplicationContext::GetInstance();
		$rewriteParams = array();
		$id = GP("id");
		if (!is_numeric($id))
		{
			$id =0;
		}
		$unit = SQLProvider::ExecuteQuery("select 
    replace(replace(`a`.`title`,_cp1251'\"',_cp1251''),_cp1251'\'',_cp1251'') AS `title`,
	  `a`.`coords`   
  from tbl__area_doc a
  where a.tbl_obj_id=$id
    and `a`.`active` = 1");
   
    $map_data = array(
      "key"=>GOOGLEMAPKEY,
      "coords"=>$unit[0]['coords'],
      "text"=>$unit[0]['title']
    );
    $map = $this->GetControl("googlemap");
    $map->dataSource = $map_data;
	}
}
?>
