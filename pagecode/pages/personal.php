<?php
class personal_php extends CPageCodeHandler
{

	public $pagesize = 20;

	public function personal_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$page = GP("page",1);
		$type = GP("type","vacancy");
		$city = GP("city");
		$tableName = 'tbl__personal_organizator_vacancy_doc';
		if ($type=="cv")
		{
			$tableName='tbl__personal_organizator_cv_doc';
		}
		$ptype = GP("ptype",0);
		$ptypeFilter = "";
		if ($ptype>0)
		{
			$ptypeFilter = " and tn.personal_type in (select tbl_obj_id from tbl__personal_types where tbl_obj_id=$ptype or parent_id=$ptype ) ";
		}
		if ($city>0)
		{
			$ptypeFilter = " and tn.city=$city";
		}
		$deadDate = date("d-m-Y", time()-mktime(0,0,0,3));

		$count = SQLProvider::ExecuteQuery("SELECT count(*) as counter
												FROM 
												  `$tableName` tn inner join tbl__personal_types jt on tn.personal_type=jt.tbl_obj_id
												  left join tbl__city ct on tn.city = ct.tbl_obj_id
												where closed=0 and registration_date > $deadDate $ptypeFilter order by registration_date desc");
		$count = $count[0]["counter"];
		$pages = floor($count/$this->pagesize)+(($count%$this->pagesize==0)?0:1);

		$data = SQLProvider::ExecuteQuery("SELECT tn.*,ct.title as city_title,jt.title as personal_type_title
												FROM 
												  `$tableName` tn left join tbl__personal_types jt on tn.personal_type=jt.tbl_obj_id
												  left join tbl__city ct on tn.city = ct.tbl_obj_id
												where closed=0 and registration_date > $deadDate $ptypeFilter order by registration_date desc limit ".(($page-1)*$this->pagesize).", ".($this->pagesize));
		$k="";
		foreach ($data as $key=>$value) {
			$data[$key]['link'] = "/personal/details/type/$type/id/".$data[$key]['tbl_obj_id'];
			$data[$key]["registration_date"] = date("d.m.Y", strtotime($data[$key]["registration_date"]));
			$data[$key]['personal_type_title'] = preg_replace("/ы$/i","",$data[$key]['personal_type_title']);
			$j = substr($data[$key]['personal_type_title'],0,1);
			$data[$key]['height'] = "0";
			if ($k!=$j)
			{
				$data[$key]['height'] = "10";
				$k=$j;
			}

			if ($data[$key]['personal_type']==-1)
			{
				$data[$key]['personal_type_title'] = $data[$key]['other_personal_type'];
			}
			
			if ($type=="vacancy")
			{
				$data[$key]['text'] = CutString($data[$key]['text'],40);
			}
			
		}

		//setting pager
		$pager = $this->GetControl("pager");
		$pager->currentPage = $page;
		$pager->totalPages = $pages;
		$pager->rewriteParams = CURLHandler::$rewriteParams;

		$personal = $this->GetControl($type."List");
		$personal->dataSource = $data;
		$typemenu = $this->GetControl("typemenu");

		$tkeys = array_keys($typemenu->dataSource);
		foreach ($tkeys as $tkey) {
			if ($tkey==$type)
			{
				$typemenu->dataSource[$tkey]["selected"] = 1;
			}
			$params = array();
			$params["type"] = $tkey;
			$typemenu->dataSource[$tkey]["link"] = CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($params);
		}
		$typeList = $this->GetControl("typeList");
		$ptypes = SQLProvider::ExecuteQuery("select * from tbl__personal_types");
		$pkeys = array_keys($ptypes);
		foreach ($pkeys as $pkey) {
			$params = CURLHandler::$rewriteParams;
			$params["ptype"] = $ptypes[$pkey]["tbl_obj_id"];
			$ptypes[$pkey]["selected"] = $ptypes[$pkey]["tbl_obj_id"]==$ptype?"id=\"selectGray\"":"";
			$ptypes[$pkey]["gray"] = $ptypes[$pkey]["tbl_obj_id"]==$ptype?"":"gray";
			$ptypes[$pkey]["link"] = CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($params);
		}
		$typeList->dataSource = $ptypes;
		$app = CApplicationContext::GetInstance();
		$rewriteParams = CURLHandler::$rewriteParams;

        /*setting city list*/
        $cities = SQLProvider::ExecuteQuery("select * from `tbl__city` where `active`=1 order by priority desc,title asc");
        $cityLabe =  "Все города";
        foreach ($cities as $key=>$value)
        {
            $cpars = $rewriteParams;
            $cpars["city"] = $value["tbl_obj_id"];
            $cities[$key]["link"] = $app->currentPath.CURLHandler::BuildRewriteParams($cpars);

            if ($value["tbl_obj_id"]==$city) {
                $cities[$key]["selected"] = 'id="selectGray"';
                $cityLabe =  $value["title"];
            }
            else {
                $cities[$key]["selected"] = '';
            }
        }
        $cityList = $this->GetControl("citySelector");
        $cityList->dataSource = $cities;
        $cityList->current = $cityLabe;

            //kind of activity
        $types  = array();
        $typeItem = array(
            'title'=>'Резюме',
            'link'=>CURLHandler::$currentPath.CURLHandler::BuildRewriteParams(array("type"=>"cv")),
            'selected'=> GP("type","cv") == 'cv'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typeItem = array(
            'title'=>'Вакансии',
            'link'=>CURLHandler::$currentPath.CURLHandler::BuildRewriteParams(array("type"=>"vacancy")),
            'selected'=> GP("type","cv") == 'vacancy'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typesList = $this->GetControl("personalTypeList");
        $typesList->dataSource = $types;

	}
}
?>
