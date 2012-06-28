<?php
    function allToUpperCase(&$value, $key) {
        $value = strtoupper($value);
    }

	class search_php extends CPageCodeHandler 
	{
//		public $max_per_page = 10;
		
		public $pageSize = 20;
		
		public $searchtext = "";
		
		public $hide_msg = true;
		
		public function search_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
		    $site = $_SERVER['HTTP_HOST'];
		
			$search = trim(GP("search",""));
			$contractor = GP("contractor",0);
			$area = GP("area",0);
			$artist = GP("artist",0);
			$agency = GP("agency",0);
			$personal = GP("personal",0);
			$title = GP("title",0);
			$page = GP("page",1);
      $rewriteParams = $_GET;
			
			if ($search!="")
				$searchResult = SQLProvider::ExecuteQuery("insert into tbl__search_queries (query,date) values ('".addslashes($search)."',NOW())");
			$this->searchtext = $search;
			/*$searchData = Array();
			$searchData["searchtext"] = $search;
			$searchData["contractor_checked"] = $contractor?"checked=\"checked\"":"";
			$searchData["area_checked"] = $area?"checked=\"checked\"":"";
			$searchData["artist_checked"] = $artist?"checked=\"checked\"":"";
			$searchData["agency_checked"] = $agency?"checked=\"checked\"":"";
			$searchData["personal_checked"] = $personal?"checked=\"checked\"":"";
			
			$searchForm = $this->GetControl("searchForm");
			$searchForm->dataSource = $searchData;*/
			
			if (!$contractor and !$area and !$artist and !$agency and !$personal) {
				$contractor = 1;
				$area = 1;
				$artist = 1;
				$agency = 1;
				$personal = 1;			
			}
			
			if ($search!="") {
              $words = array_unique(explode(" ",$search));
              array_walk($words, 'allToUpperCase');
              
              
			  $searchResult = array();
              
              include 'search_by_title.php';
              include 'search_by_category.php';
              include 'search_others.php';
              
              $count = sizeof($searchResult);
              
              include 'search_post_process.php';
              
              if ($count > 0) {
                  $searchForm = $this->GetControl("searchList");
                  $searchForm->dataSource = $searchResult;
                  
                  //Pager
                  $pages = floor($count/$this->pageSize)+(($count%$this->pageSize==0)?0:1);
                  if (($page>$pages)&&($pages>0)) {
                    $page = $pages;
                    $rewriteParams["page"] = $page;
                    CURLHandler::Redirect(CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($rewriteParams));
                  }
                  if ($page==1) {
                    unset($rewriteParams["page"]);
                  }
                                
                  $pager = $this->GetControl("pager");
                  $pager->currentPage = $page;
                  $pager->totalPages = $pages;
                  $pager->rewriteParams = $rewriteParams;
                  $message["message"] = "";
              }
              else {
               $message["message"] = "По Вашему запросу ничего не найдено. Для повышения релевантности выдачи мы производим ручную 
оптимизацию поиска. С Вашей помощью мы сделаем это быстрее. Спасибо. Продолжайте искать.";
              }              
            }
            else {
                $message["message"] = "";
                if (isset($_GET["search"])) {
               $message["message"] = "По Вашему запросу ничего не найдено. Для повышения релевантности выдачи мы производим ручную 
оптимизацию поиска. С Вашей помощью мы сделаем это быстрее. Спасибо. Продолжайте искать.";
                }
            }
            
            $messageForm = $this->GetControl("message");
            $messageForm->dataSource = $message;
			$this->hide_msg = strlen($message["message"]) == 0;
   }
}
