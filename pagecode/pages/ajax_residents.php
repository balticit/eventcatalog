<?php
	class ajax_residents_php extends CPageCodeHandler
	{

		public function ajax_residents_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
			$app = CApplicationContext::GetInstance();
      header('Content-type: text/html;charset=windows-1251');  
      $type = GP('type');
			$search = iconv('utf-8',$app->appEncoding,GP('search'));
			$search = strtoupper($search);
			$list = $this->GetControl("list");
			SQLProvider::OpenConnection();
			$list->dataSource = SQLProvider::ExecuteQuery("select * from tbl__".$type."_doc 
			                                               where upper(title) like '%".mysql_real_escape_string($search)."%'
																										 order by title");
      $body = $this->GetControl("body");        
      $body->html = $list->RenderHTML();
		}
	}
?>
