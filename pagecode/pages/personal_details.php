<?php
    class personal_details_php extends CPageCodeHandler
    {

    public function personal_details_php()
    {
        $this->CPageCodeHandler();
    }

    public function PreRender()
    {

        $type = GP("type","vacancy");
        $city = GP("city");
        $ID = GP("id");
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
        $deadDate = date("d-m-Y", time()-mktime(0,0,0,3));


        $data = SQLProvider::ExecuteQuery("SELECT tn.*,ct.title as city_title,jt.title as personal_type_title
												FROM 
											  (SELECT * FROM `$tableName` where tbl_obj_id=$ID and closed=0) tn left join tbl__personal_types jt on tn.personal_type=jt.tbl_obj_id
										      left join tbl__city ct on tn.city = ct.tbl_obj_id" );

        if ($data[0]['personal_type']==-1)
        {
            $data[0]['personal_type_title'] = $data[0]['other_personal_type'];
        }
        $details = $this->GetControl($type=="cv"?"cvdetails":"vacancydetails");
        $edetails = $this->GetControl($type!="cv"?"cvdetails":"vacancydetails");
        $edetails->template = "";
        $data[0]["registration_date"] = date("d.m.Y", strtotime($data[0]["registration_date"]));
        $data[0]['personal_type_title'] = preg_replace("/ы$/i","",$data[0]['personal_type_title']);
        if ($type=="cv")
        {
            $data[0]["birth_date"] = date("d.m.Y", strtotime($data[0]["birth_date"]));
            $data[0]["logo_visible"] = IsNullOrEmpty($data[0]["logo"])?"hidden":"visible";
        }
        $details->dataSource = $data[0];
        $typemenu = $this->GetControl("typemenu");
        $tkeys = array_keys($typemenu->dataSource);
        foreach ($tkeys as $tkey) {
            if ($tkey==$type)
            {
                $typemenu->dataSource[$tkey]["selected"] = 1;
            }
            $params = array();
            $params["type"] = $tkey;
            $typemenu->dataSource[$tkey]["link"] = "/personal/".CURLHandler::BuildRewriteParams($params);
        }
        $typeList = $this->GetControl("typeList");
        $ptypes = SQLProvider::ExecuteQuery("select * from tbl__personal_types");
        $pkeys = array_keys($ptypes);
        foreach ($pkeys as $pkey) {
            $params = CURLHandler::$rewriteParams;
            unset($params['id']);
            $params["ptype"] = $ptypes[$pkey]["tbl_obj_id"];
            $ptypes[$pkey]["selected"] = $ptypes[$pkey]["tbl_obj_id"]==$ptype?"id=\"selectGray\"":"";
            $ptypes[$pkey]["gray"] = $ptypes[$pkey]["tbl_obj_id"]==$ptype?"":"gray";
            $ptypes[$pkey]["link"] = "/personal/".CURLHandler::BuildRewriteParams($params);
        }
        $typeList->dataSource = $ptypes;
        $app = CApplicationContext::GetInstance();
        $rewriteParams = CURLHandler::$rewriteParams;

        //kind of activity
        $types  = array();
        $typeItem = array(
            'title'=>'Резюме',
            'link'=>"/personal/".CURLHandler::BuildRewriteParams(array("type"=>"cv")),
            'selected'=> GP("type","cv") == 'cv'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typeItem = array(
            'title'=>'Вакансии',
            'link'=>"/personal/".CURLHandler::BuildRewriteParams(array("type"=>"vacancy")),
            'selected'=> GP("type","cv") == 'vacancy'?'id="selectGray"':"",
            'green'=>'Gray'
        );
        array_push($types,$typeItem);
        $typesList = $this->GetControl("personalTypeList");
        $typesList->dataSource = $types;
    }
}

?>
