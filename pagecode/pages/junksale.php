<?php
class junksale_php extends CPageCodeHandler
    {

    public $pagesize = 25;

    public function junksale_php()
    {
        $this->CPageCodeHandler();
    }

    public function BuildFilter($filter,$name,$value,$cond="=",$type="numeric")
    {
        switch ($type) {
            case "numeric":
                {
                    if (is_numeric($value))
                    {
                        if (strlen($filter)>0)
                        {
                            $filter.=" and ";
                        }
                        $filter.=" $name $cond $value ";
                    }
                }
                break;
            case "string":
                if (is_string($value))
                {
                    if (strlen($filter)>0)
                    {
                        $filter.=" and ";
                    }
                    $filter.=" $name $cond '$value' ";
                }
                break;
        }
        return $filter;
    }

    public function PreRender()
    {
        $app = CApplicationContext::GetInstance();
        $rewriteParams = CURLHandler::$rewriteParams;
        $type = GP("type","sell");
        $city = GP("city");
        $section = GP("section");
        $page = GP("page",1);
            //pagesize = 20;
        $fliter = " active=1 ";
        $fliter = $this->BuildFilter($fliter,"city",$city);
        $fliter = $this->BuildFilter($fliter,"section",$section);
            //$fliter .= " limit ".(($page-1)*$this->$pagesize).", ".(($page-1)*$this->$pagesize);
            //item list
        $junks = array();
        if ($type=="sell")
        {
            $count = SQLProvider::ExecuteQuery("select count(*) as counter from  `tbl__baraholka_sell` where $fliter");
            $count = $count[0]["counter"];
            $pages = floor($count/$this->pagesize)+(($count%$this->pagesize==0)?0:1);

            $junks = SQLProvider::ExecuteQuery( "select * from `tbl__baraholka_sell` where $fliter order by str_to_date(`date`,'%d.%m.%Y') desc limit ".(($page-1)*$this->pagesize).", ".($this->pagesize));
            $keys = array_keys($junks);
            foreach ($keys as $key) {

                $junks[$key]["date"] = date("d.m.Y",strtotime($junks[$key]['date']));
                $junks[$key]["event_date"] = date("d.m.Y",strtotime($junks[$key]['event_date']));
                $junks[$key]["type"] = $type;
                $junks[$key]["long_description"] = CutString($junks[$key]["long_description"],40);
            }
        }
        if ($type=="search")
        {
            $count = SQLProvider::ExecuteQuery("select count(*) as counter from  `tbl__baraholka_sell` where $fliter");
            $count = $count[0]["counter"];
            $pages = floor($count/$this->pagesize)+(($count%$this->pagesize==0)?0:1);

            $junks = SQLProvider::ExecuteQuery( "select * from `tbl__baraholka_search` where $fliter order by str_to_date(`date`,'%d.%m.%Y') desc limit ".(($page-1)*$this->pagesize).", ".($this->pagesize));
            $keys = array_keys($junks);
            foreach ($keys as $key) {
                $junks[$key]["date"] = date("d.m.Y",strtotime($junks[$key]['date']));
                $junks[$key]["event_date"] = date("d.m.Y",strtotime($junks[$key]['event_date']));
                $junks[$key]["type"] = $type;
                $junks[$key]["description"] = CutString($junks[$key]["description"],40);
            }
        }
        $junkList = $this->GetControl("junkList");
        $junkList->dataSource = $junks;

            //type selectors
        $sellPars = CPArray($rewriteParams,array("city","section"));
        $sellPars["type"] = "sell";
        $sellLink = $this->GetControl("sellLink");
        $sellLink->dataSource = array("link"=>CURLHandler::BuildRewriteParams($sellPars),
            "title"=>"Распродажа вещей с мероприятия",
            "selected"=>($type=="sell")?"id = \"selected\"":"");
        $sellPars = CPArray($rewriteParams,array("city","section"));
        $sellPars["type"] = "search";
        $sellLink = $this->GetControl("searchLink");
        $sellLink->dataSource = array("link"=>CURLHandler::BuildRewriteParams($sellPars),
            "title"=>"Поиск вещей для мероприятия",
            "selected"=>($type=="search")?"id = \"selected\"":"");

        
            //kind of activity
        $types  = array();
        $typeItem = array(
            'title'=>'Распродажа вещей',
            'link'=>CURLHandler::$currentPath.CURLHandler::BuildRewriteParams(array("type"=>"sell")),
            'selected'=> GP("type","sell") == 'sell'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typeItem = array(
            'title'=>'Поиск вещей',
            'link'=>CURLHandler::$currentPath.CURLHandler::BuildRewriteParams(array("type"=>"search")),
            'selected'=> GP("type","sell") == 'search'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typesList = $this->GetControl("junkTypeList");
        $typesList->dataSource = $types;

            //setting pager
        $pager = $this->GetControl("pager");
        $pager->currentPage = $page;
        $pager->totalPages = $pages;
        $pager->rewriteParams = $rewriteParams;
    }
}

?>
