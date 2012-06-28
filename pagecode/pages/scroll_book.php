<?php
class scroll_book_php extends CPageCodeHandler
{
	public function scroll_book_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		if (isset($_GET["rand"]))
		{
			$lastBookItems = SQLProvider::ExecuteQuery("
				select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__public_doc pd where active = 1
					) t order by rand() limit 12");
		}
		else {
			$lastBookItems = SQLProvider::ExecuteQuery("
				select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'eventtv' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__eventtv_doc pd where active = 1 
					union all
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__public_doc pd where active = 1
					) t
				order by registration_date desc limit 15");
		}
		$last_book = $this->GetControl("last_book");
		for ($i=0;$i<sizeof($lastBookItems);$i++)
		{
			$lastBookItems[$i]["title"] = CutString($lastBookItems[$i]["title"]);
			$lastBookItems[$i]["padding"] = 22-$i;
			$lastBookItems[$i]["num"] = $i + 1;
			$lastBookItems[$i]["count"] = 4;
		}
		$last_book->dataSource = $lastBookItems;
	}
}
?>
