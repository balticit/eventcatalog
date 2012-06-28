<?php
	class forsites_php extends CPageCodeHandler 
	{
	
		public $data = array();
		public $fields = array();
		public $table = array();
		public $tablefields = array();

		public function forsites_php()
		{
			$this->CPageCodeHandler();
		}
		
		//Из ассоциативного массива делаем XML-структуру
		public function array2xml($data,$depth=0,$parenttag="") {
			if (is_array($data)) {
				$xml = "";
				foreach ($data as $key=>$value) {
					$xml .= "\n";
					for ($i=0; $i<$depth; $i++) $xml .= "   ";
					if (is_int($key))
						$key = substr($parenttag,0,strlen($parenttag)-1);
					$xml .= "<$key>".$this->array2xml($value,$depth+1,$key)."</$key>";
				}
				$xml .= "\n";
				for ($i=0; $i<$depth-1; $i++) $xml .= "   ";
			}
			else
				$xml = $data;
			return $xml;
		}
		
		public function select($fieldsstr,$tablename,$where="",$order="",$limit="") {
			//вывод данных
			if ($fieldsstr=="") {
				$this->data["page"]["data"]["error"] = "Список выводимых полей не определён!";
				$this->data["page"]["code"] = "112";
				return;
			}
			$elements = SQLProvider::ExecuteQuery("select $fieldsstr from ".$tablename." $where $order $limit");
			$this->data["page"]["data"]["elements"] = array();
			foreach ($elements as $element) {
				$newel = array();
				foreach ($this->fields as $field) {
					if ((isset($this->tablefields[$field]))and($this->tablefields[$field][read]))
						$newel[$field] = "<![CDATA[".$element[$field]."]]>";
				}
				$this->data["page"]["data"]["elements"][] = $newel;
			}
		
		}
		
		public function PreRender()
		{
			iconv_set_encoding("output_encoding", "windows-1251");
			iconv_set_encoding("input_encoding", "windows-1251");
			iconv_set_encoding("internal_encoding", "windows-1251");
			
			$login = GP("login");
			$password = GP("password");
			$datatype = GP("datatype");
			$requesttype = GP("requesttype");
			$condition = GP("condition");
			$orderby = GP("orderby");
			$ordertype = GP("ordertype");
			$limit = GP("limit");
			$userlogin = GP("userlogin");
			$userpassword = GP("userpassword");
			$inputfields = GP("fields");
			$this->fields = explode("|",$inputfields);
			
			$usersite = SQLProvider::ExecuteQuery("select * from tbl__sites where login='$login' and password='".md5($password)."'");
			
			//$data = array();
			$this->data["page"] = array();
			$this->data["page"]["code"] = "100";
			if (sizeof($usersite)>0) {
				$this->data["page"]["usersiteinfo"] = array();
				$this->data["page"]["usersiteinfo"]["site"] = $usersite[0]["address"];
				$this->data["page"]["usersiteinfo"]["login"] = $usersite[0]["login"];
				$this->data["page"]["data"] = array();
				$this->data["page"]["data"]["datatype"] = $datatype;
				$this->data["page"]["data"]["requesttype"] = $requesttype;

				switch ($datatype) {
				
				
					case "contractor" : //Подрядчики
						if ($usersite[0]["contractor"]) {
							$this->table = array(
								"name" => "tbl__contractor_doc",
								"read" => true,
								"write" => true,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"description" => array(
									"select" => "description",
									"where" => "description",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"city" => array(
									"select" => "IF(other_city,other_city,(select title from `tbl__city` where `tbl__city`.`tbl_obj_id`=`".$this->table[name]."`.`city`))",
									"where" => "IF(other_city,other_city,(select title from `tbl__city` where `tbl__city`.`tbl_obj_id`=`".$this->table[name]."`.`city`))",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"city_id" => array(
									"select" => "city",
									"where" => "city",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"address" => array(
									"select" => "address",
									"where" => "address",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"logo_image" => array(
									"select" => "logo_image",
									"where" => "logo_image",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"phone" => array(
									"select" => "phone",
									"where" => "phone",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"site_address" => array(
									"select" => "site_address",
									"where" => "site_address",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"email" => array(
									"select" => "email",
									"where" => "email",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"login" => array(
									"select" => "login",
									"where" => "login",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"password" => array(
									"select" => "password",
									"where" => "password",
									"read" => false,
									"write" => true,
									"multiple" => false
								),
								"logo_image" => array(
									"select" => "logo_image",
									"where" => "logo_image",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"phone2" => array(
									"select" => "phone2",
									"where" => "phone2",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"activity" => array(
									"select" => "(select GROUP_CONCAT(`tbl__activity_type`.`title` SEPARATOR '|') from `tbl__activity_type`,`tbl__contractor2activity` where `tbl__activity_type`.`tbl_obj_id`=`tbl__contractor2activity`.`kind_of_activity` and `tbl__contractor2activity`.`tbl_obj_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__activity_type`.`title`) from `tbl__activity_type`,`tbl__contractor2activity` where `tbl__activity_type`.`tbl_obj_id`=`tbl__contractor2activity`.`kind_of_activity` and `tbl__contractor2activity`.`tbl_obj_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"activity_id" => array(
									"select" => "(select GROUP_CONCAT(`tbl__contractor2activity`.`kind_of_activity` SEPARATOR '|') from `tbl__contractor2activity` where `tbl__contractor2activity`.`tbl_obj_id`=`".$this->table[name]."`.`tbl_obj_id`)",								
									"where" => "(select `tbl__contractor2activity`.`kind_of_activity` from `tbl__contractor2activity` where `tbl__contractor2activity`.`tbl_obj_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => true,
									"multiple" => true,
									"link" => array(
										"table" => "tbl__contractor2activity",
										"id" => "tbl_obj_id",
										"link_id" => "kind_of_activity"
									)
								),

							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					
					case "activitytype" : //Тип подрядчика
						if ($usersite[0]["contractor"]) {
							$this->table = array(
								"name" => "tbl__activity_type",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"parent_id" => array(
									"select" => "parent_id",
									"where" => "parent_id",
									"read" => true,
									"write" => true,
									"multiple" => false
								)
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					
					case "area" : //Тип подрядчика
						if ($usersite[0]["area"]) {
							$this->table = array(
								"name" => "tbl__area_doc",
								"read" => true,
								"write" => true,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"type" => array(
									"select" => "(select GROUP_CONCAT(`tbl__area_types`.`title` SEPARATOR '|') from `tbl__area_types`,`tbl__area2type` where `tbl__area_types`.`tbl_obj_id`=`tbl__area2type`.`type_id` and `tbl__area2type`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__area_types`.`title` from `tbl__area_types`,`tbl__area2type` where `tbl__area_types`.`tbl_obj_id`=`tbl__area2type`.`type_id` and `tbl__area2type`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"subtype" => array(
									"select" => "(select GROUP_CONCAT(`tbl__area_subtypes`.`title` SEPARATOR '|') from `tbl__area_subtypes`,`tbl__area2subtype` where `tbl__area_subtypes`.`tbl_obj_id`=`tbl__area2subtype`.`subtype_id` and `tbl__area2subtype`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__area_subtypes`.`title` from `tbl__area_subtypes`,`tbl__area2subtype` where `tbl__area_subtypes`.`tbl_obj_id`=`tbl__area2subtype`.`subtype_id` and `tbl__area2subtype`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"type_id" => array(
									"select" => "(select GROUP_CONCAT(`tbl__area2type`.`type_id` SEPARATOR '|') from `tbl__area2type` where `tbl__area2type`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",	
									"where" => "(select `tbl__area2type`.`type_id` from `tbl__area2type` where `tbl__area2type`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => true,
									"multiple" => true,
									"link" => array(
										"table" => "tbl__area2type",
										"id" => "area_id",
										"link_id" => "type_id"
									)
								),
								"subtype_id" => array(
									"select" => "(select GROUP_CONCAT(`tbl__area2subtype`.`subtype_id` SEPARATOR '|') from `tbl__area2subtype` where `tbl__area2subtype`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",	
									"where" => "(select `tbl__area2subtype`.`subtype_id` from `tbl__area2subtype` where `tbl__area2subtype`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => true,
									"multiple" => true,
									"link" => array(
										"table" => "tbl__area2subtype",
										"id" => "area_id",
										"link_id" => "subtype_id"
									)
								),								
								"city" => array(
									"select" => "IF(other_city,other_city,(select title from `tbl__city` where `tbl__city`.`tbl_obj_id`=`".$this->table[name]."`.`city`))",
									"where" => "IF(other_city,other_city,(select title from `tbl__city` where `tbl__city`.`tbl_obj_id`=`".$this->table[name]."`.`city`))",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"city_id" => array(
									"select" => "city",
									"where" => "city",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"photos" => array(
									"select" => "(select GROUP_CONCAT(`tbl__photo`.`l_image` SEPARATOR '|') from `tbl__photo`,`tbl__area_photos` where `tbl__photo`.`tbl_obj_id`=`tbl__area_photos`.`child_id` and `tbl__area_photos`.`parent_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__photo`.`l_image` from `tbl__photo`,`tbl__area_photos` where `tbl__photo`.`tbl_obj_id`=`tbl__area_photos`.`child_id` and `tbl__area_photos`.`parent_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"address" => array(
									"select" => "address",
									"where" => "address",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"logo_image" => array(
									"select" => "logo",
									"where" => "logo",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"phone" => array(
									"select" => "phone",
									"where" => "phone",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"site_address" => array(
									"select" => "site_address",
									"where" => "site_address",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"email" => array(
									"select" => "email",
									"where" => "email",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"login" => array(
									"select" => "login",
									"where" => "login",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"password" => array(
									"select" => "password",
									"where" => "password",
									"read" => false,
									"write" => true,
									"multiple" => false
								),
								"description" => array(
									"select" => "description",
									"where" => "description",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"kitchen" => array(
									"select" => "kitchen",
									"where" => "kitchen",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"parking" => array(
									"select" => "parking",
									"where" => "parking",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"equipment" => array(
									"select" => "equipment",
									"where" => "equipment",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"dancing" => array(
									"select" => "dancing",
									"where" => "dancing",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"style" => array(
									"select" => "style",
									"where" => "style",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"halls" => array(
									"select" => "halls",
									"where" => "halls",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"video" => array(
									"select" => "video",
									"where" => "video",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"farvideo" => array(
									"select" => "farvideo",
									"where" => "farvideo",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"coords" => array(
									"select" => "coords",
									"where" => "coords",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"max_count_man" => array(
									"select" => "max_count_man",
									"where" => "max_count_man",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"date_open" => array(
									"select" => "date_open",
									"where" => "date_open",
									"read" => true,
									"write" => true,
									"multiple" => false
								),								
								"area_cost" => array(
									"select" => "area_cost",
									"where" => "area_cost",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"menu" => array(
									"select" => "menu",
									"where" => "menu",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "areatype" : //Тип подрядчика
						if ($usersite[0]["area"]) {
							$this->table = array(
								"name" => "tbl__area_types",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "areasubtype" : //Тип подрядчика
						if ($usersite[0]["area"]) {
							$this->table = array(
								"name" => "tbl__area_subtypes",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"parent_id" => array(
									"select" => "parent_id",
									"where" => "parent_id",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "artist" : //Артист
						if ($usersite[0]["artist"]) {
							$this->table = array(
								"name" => "tbl__artist_doc",
								"read" => true,
								"write" => true,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"group" => array(
									"select" => "(select title from `tbl__artist_group` where `tbl__artist_group`.`tbl_obj_id`=`".$this->table[name]."`.`group`)",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"group_id" => array(
									"select" => "group",
									"where" => "group",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"subgroup" => array(
									"select" => "(select GROUP_CONCAT(`tbl__artist_subgroup`.`title` SEPARATOR '|') from `tbl__artist_subgroup`,`tbl__artist2subgroup` where `tbl__artist_subgroup`.`tbl_obj_id`=`tbl__artist2subgroup`.`subgroup_id` and `tbl__artist2subgroup`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__artist_subgroup`.`title` from `tbl__artist_subgroup`,`tbl__artist2subgroup` where `tbl__artist_subgroup`.`tbl_obj_id`=`tbl__artist2subgroup`.`subgroup_id` and `tbl__artist2subgroup`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"subgroup_id" => array(
									"select" => "(select GROUP_CONCAT(`tbl__artist2subgroup`.`subgroup_id` SEPARATOR '|') from `tbl__artist2subgroup` where `tbl__artist2subgroup`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",	
									"where" => "(select `tbl__area2type`.`type_id` from `tbl__area2type` where `tbl__area2type`.`area_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => true,
									"multiple" => true,
									"link" => array(
										"table" => "tbl__artist2subgroup",
										"id" => "artist_id",
										"link_id" => "subgroup_id"
									)
								),
								"style" => array(
									"select" => "(select GROUP_CONCAT(`tbl__styles`.`title` SEPARATOR '|') from `tbl__styles`,`tbl__artist2style` where `tbl__styles`.`tbl_obj_id`=`tbl__artist2style`.`style_id` and `tbl__artist2style`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__styles`.`title` from `tbl__styles`,`tbl__artist2style` where `tbl__styles`.`tbl_obj_id`=`tbl__artist2style`.`style_id` and `tbl__artist2style`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"style_id" => array(
									"select" => "(select GROUP_CONCAT(`tbl__artist2style`.`style_id` SEPARATOR '|') from `tbl__artist2style` where `tbl__artist2style`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",	
									"where" => "(select `tbl__artist2style`.`style_id` from `tbl__artist2style` where `tbl__artist2style`.`artist_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => true,
									"multiple" => true,
									"link" => array(
										"table" => "tbl__artist2style",
										"id" => "artist_id",
										"link_id" => "style_id"
									)
								),								
								"country" => array(
									"select" => "IF(other_country,other_country,(select title from `tbl__countries` where `tbl__countries`.`tbl_obj_id`=`".$this->table[name]."`.`country`))",
									"where" => "IF(other_country,other_country,(select title from `tbl__countries` where `tbl__countries`.`tbl_obj_id`=`".$this->table[name]."`.`country`))",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"photos" => array(
									"select" => "(select GROUP_CONCAT(`tbl__photo`.`l_image` SEPARATOR '|') from `tbl__photo`,`tbl__area_photos` where `tbl__photo`.`tbl_obj_id`=`tbl__area_photos`.`child_id` and `tbl__area_photos`.`parent_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"where" => "(select `tbl__photo`.`l_image` from `tbl__photo`,`tbl__area_photos` where `tbl__photo`.`tbl_obj_id`=`tbl__area_photos`.`child_id` and `tbl__area_photos`.`parent_id`=`".$this->table[name]."`.`tbl_obj_id`)",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"country_id" => array(
									"select" => "country",
									"where" => "country",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"logo_image" => array(
									"select" => "logo",
									"where" => "logo",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"phone" => array(
									"select" => "manager_phone",
									"where" => "manager_phone",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"site_address" => array(
									"select" => "site_address",
									"where" => "site_address",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"email" => array(
									"select" => "email",
									"where" => "email",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"login" => array(
									"select" => "login",
									"where" => "login",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"password" => array(
									"select" => "password",
									"where" => "password",
									"read" => false,
									"write" => true,
									"multiple" => false
								),
								"description" => array(
									"select" => "description",
									"where" => "description",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"video" => array(
									"select" => "video",
									"where" => "video",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"farvideo" => array(
									"select" => "farvideo",
									"where" => "farvideo",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "artistsubgroup" : //Тип артиста
						if ($usersite[0]["artist"]) {
							$this->table = array(
								"name" => "tbl__artist_subgroup",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"parent_id" => array(
									"select" => "parent_id",
									"where" => "parent_id",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "artistgroup" : //Тип артиста
						if ($usersite[0]["artist"]) {
							$this->table = array(
								"name" => "tbl__artist_group",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "artiststyle" : //Тип артиста
						if ($usersite[0]["artist"]) {
							$this->table = array(
								"name" => "tbl__styles",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "agency" : //Агентства
						if ($usersite[0]["agency"]) {
							$this->table = array(
								"name" => "tbl__agency_doc",
								"read" => true,
								"write" => true,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"type" => array(
									"select" => "(select title from `tbl__agency_type` where `tbl__agency_type`.`tbl_obj_id`=`".$this->table[name]."`.`kind_of_activity`)",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"type_id" => array(
									"select" => "kind_of_activity",
									"where" => "kind_of_activity",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"logo_image" => array(
									"select" => "logo_image",
									"where" => "logo_image",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"phone" => array(
									"select" => "phone",
									"where" => "phone",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"phone2" => array(
									"select" => "phone2",
									"where" => "phone2",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"site_address" => array(
									"select" => "site_address",
									"where" => "site_address",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"email" => array(
									"select" => "email",
									"where" => "email",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"login" => array(
									"select" => "login",
									"where" => "login",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"password" => array(
									"select" => "password",
									"where" => "password",
									"read" => false,
									"write" => true,
									"multiple" => false
								),
								"description" => array(
									"select" => "description",
									"where" => "description",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"city" => array(
									"select" => "IF(other_city,other_city,(select title from `tbl__city` where `tbl__city`.`tbl_obj_id`=`".$this->table[name]."`.`city`))",
									"where" => "IF(other_city,other_city,(select title from `tbl__city` where `tbl__city`.`tbl_obj_id`=`".$this->table[name]."`.`city`))",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
								"city_id" => array(
									"select" => "city",
									"where" => "city",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					case "agencytype" : //Тип артиста
						if ($usersite[0]["artist"]) {
							$this->table = array(
								"name" => "tbl__agency_type",
								"read" => true,
								"write" => false,
								"id" => "tbl_obj_id"
							);
							$this->tablefields = array(
								"tbl_obj_id" => array(
									"select" => "tbl_obj_id",
									"where" => "tbl_obj_id",
									"read" => true,
									"write" => false,
									"multiple" => false
								),
								"title" => array(
									"select" => "title",
									"where" => "title",
									"read" => true,
									"write" => true,
									"multiple" => false
								),
							);
							
						}
						else {
							$this->data["page"]["data"]["error"] = "Вам недоступен этот раздел данных!";
							$this->data["page"]["code"] = "120";
						}
					break;
					
					default : //error
						$this->data["page"]["data"]["error"] = "Не выбран раздел данных!";
						$this->data["page"]["code"] = "110";
					break;
				}
				if ($this->data["page"]["code"]=="100") {
					switch ($requesttype) { //Выбор типа запроса
					
					
						case "showelements" : //Показать список элементов
						
							if ($this->table["read"]) {
								
								$fieldsstr = "";
								foreach ($this->fields as $field) { //Добавление в запрос SQL запрашиваемые данные
									if (($this->tablefields[$field])and($this->tablefields[$field][read])) {
										$fieldsstr .= ",".$this->tablefields[$field]["select"]." as `$field`";
									}
								}
								$fieldsstr = substr($fieldsstr,1);
								
								//Добавление условия WHERE в SQL-запрос
								$where = "";
								if (ereg("([a-zA-Z0-9_]+[=<>^][a-zA-Z0-9а-яёА-ЯЁ_]+)(\|[a-zA-Z0-9_]+[=<>^][a-zA-Z0-9а-яёА-ЯЁ_]+)*",$condition)) {
									
									$conditions = explode("|",$condition);
									foreach ($conditions as $value) {
										if ((ereg("([a-zA-Z0-9_]+)([=<>^])([a-zA-Z0-9а-яёА-ЯЁ_]+)",$value,$regs)) ) {
											switch ($regs[2]) {
												case "^" : if ($this->tablefields[$regs[1]]['multiple']) $where .= " and ($regs[3] in ".$this->tablefields[$regs[1]]['where'].") "; 
												break;
												case "=" :
												case ">" :
												case "<" : if (!$this->tablefields[$regs[1]]['multiple']) $where .= " and (".$this->tablefields[$regs[1]]['where']."$regs[2]$regs[3]) ";
												break;
											}
										}
									}
									$where = substr($where,4);
									if (strlen($where)>0)
										$where = " WHERE ".$where;
								}
								
								//Добавление упорядочивания запроса
								if (($orderby!="")and(isset($this->tablefields[$orderby])and($this->tablefields[$orderby]))) {
									$order = " ORDER BY $orderby ";
									if (eregi("asc|desc",$ordertype)) {
										$order .= $ordertype." ";
									}
								}
								
								//Добавление ограничения на количество выдаваемых записей
								if (ereg("[0-9]+,[0-9]+",$limit)) {
									$limit = " limit $limit";
								}
								else $limit = "";
								
								//Выполнение запроса
								
								$this->select($fieldsstr,$this->table[name],$where,$order,$limit);
								
							}
							else {//Вывод сообщения об ошибке
								$this->data["page"]["data"]["error"] = "Нет доступа!";
								$this->data["page"]["code"] = "113";
							}

						break;
						
						case "editelement" :
						
							if ($this->table["write"]) {
							
								$params = array();
								foreach ($_GET as $g_key=>$g_value) {
									if (substr($g_key,0,2) == "p_") {
										$key = substr($g_key,2);
										$params[$key] = $g_value;
	
										//$data["page"]["data"]["element"][$key] = $g_value;
									}
								}
								//Проверка, введены ли логин и пароль
								if ((isset($params["login"]))and(isset($params["password"]))) {
								
									//Проверка верные ли логин и пароль
									$r = mysql_query("select * from ".$this->table["name"]." where login='$params[login]' and password='".(md5($params["password"]))."'");
									if (mysql_num_rows($r)>0) {
								
										$tochange = "";
										foreach ($params as $p_key => $p_value) {
											if (($p_key!="login")and($p_key!="password")) {
												if ((!$this->tablefields[$p_key]["multiple"])and($this->tablefields[$p_key]["write"])) {
													$tochange .= ", $p_key='".addslashes(urldecode($p_value))."'";
												}
											}
										}
										$tochange = substr($tochange,2);
										$query = "update ".$this->table[name]." set $tochange where login='$params[login]' and password='".(md5($params["password"]))."'";
										SQLProvider::ExecuteNonReturnQuery($query);
										
										$r = mysql_query("select ".$this->table["id"]." as id from ".$this->table["name"]." where login='$params[login]' and password='".(md5($params["password"]))."'");
										$f = mysql_fetch_array($r);
										$this->current_id = $f["id"];
										
										//Изменение multiple параметров
										foreach ($params as $p_key => $p_value) {
											if (($this->tablefields[$p_key]["multiple"])and($this->tablefields[$p_key]["write"])) {
												SQLProvider::ExecuteNonReturnQuery("delete from ".$this->tablefields[$p_key]["link"]["table"]." where ".$this->tablefields[$p_key]["link"]["id"]."=".$this->current_id);
												$p_values = explode("|",$p_value);
												foreach ($p_values as $link_val) {
													$query = "INSERT INTO ".$this->tablefields[$p_key]["link"]["table"].
																" (".$this->tablefields[$p_key]["link"]["id"].",".
																$this->tablefields[$p_key]["link"]["link_id"].
																") VALUES (".$this->current_id.",$link_val)";
													SQLProvider::ExecuteNonReturnQuery($query);
												}
											}
										}
										
										
										$fieldsstr = "";
										foreach ($this->fields as $field) { //Добавление в запрос SQL запрашиваемые данные
											if (($this->tablefields[$field])and($this->tablefields[$field][read])) {
												$fieldsstr .= ",".$this->tablefields[$field]["select"]." as `$field`";
											}
										}
										$fieldsstr = substr($fieldsstr,1);
										$this->select($fieldsstr,$this->table[name],"where login='$params[login]' and password='".(md5($params["password"]))."'");
									}
									else {//Вывод сообщения об ошибке
										$this->data["page"]["data"]["error"] = "Логин или пароль не верные!";
										$this->data["page"]["code"] = "114";
									}
								}
								else {//Вывод сообщения об ошибке
									$this->data["page"]["data"]["error"] = "Логин или пароль не введён!";
									$this->data["page"]["code"] = "112";
								}								
							}
							else {//Вывод сообщения об ошибке
								$this->data["page"]["data"]["error"] = "Нет доступа!";
								$this->data["page"]["code"] = "113";
							}
							
						break;
						
						case "addelement" :
						
							if ($this->table["write"]) {
						
								$params = array();
								foreach ($_GET as $g_key=>$g_value) {
									if (substr($g_key,0,2) == "p_") {
										$key = substr($g_key,2);
										$params[$key] = $g_value;
									}
								}
								if ((isset($params["login"]))and(isset($params["password"]))) {
									
									$r = mysql_query("select * from `vw__all_users` where `login`='$params[login]'");
									//$f = mysql_fetch_array($r);
									if (mysql_num_rows($r)==0) {
										
										//Добавление в SQL-запрос не multiple параметров
										$fs = "";
										foreach ($params as $p_key => $p_value) {
											if ((!$this->tablefields[$p_key]["multiple"])and($this->tablefields[$p_key]["write"]))
												$fs .=", $p_key";
										}
										$fs = "(".substr($fs,2).")";
										$values = "";
										foreach ($params as $p_key => $p_value) {
											if ((!$this->tablefields[$p_key]["multiple"])and($this->tablefields[$p_key]["write"])) {
												if ($p_key!="password") {
													$values .= ", '".addslashes(urldecode($p_value))."'";
												}
												else
													$values .= ", '".md5(urldecode($p_value))."'";
											}
										}
										$values = "(".substr($values,2).")";
										
										//Вставка параметров для не multiple параметров
										$query = "insert into ".$this->table["name"]." $fs values $values";
										mysql_query($query);
										$r = mysql_query("select LAST_INSERT_ID() as `last_id`");
										$f = mysql_fetch_array($r);
										$this->last_id = $f["last_id"];
										
										//Изменение multiple параметров
										foreach ($params as $p_key => $p_value) {
											if (($this->tablefields[$p_key]["multiple"])and($this->tablefields[$p_key]["write"])) {
												SQLProvider::ExecuteNonReturnQuery("delete from ".$this->tablefields[$p_key]["link"]["table"]." where ".$this->tablefields[$p_key]["link"]["id"]."=".$this->last_id);
												$p_values = explode("|",$p_value);
												foreach ($p_values as $link_val) {
													$query = "INSERT INTO ".$this->tablefields[$p_key]["link"]["table"].
																" (".$this->tablefields[$p_key]["link"]["id"].",".
																$this->tablefields[$p_key]["link"]["link_id"].
																") VALUES (".$this->last_id.",$link_val)";
													SQLProvider::ExecuteNonReturnQuery($query);
												}
											}
										}
										
										
										foreach ($this->fields as $field) { //Добавление в запрос SQL запрашиваемые данные
											if (($this->tablefields[$field])and($this->tablefields[$field][read])) {
												$fieldsstr .= ",".$this->tablefields[$field]["select"]." as `$field`";
											}
										}
										$fieldsstr = substr($fieldsstr,1);
										$this->select($fieldsstr,$this->table["name"],"where login='$params[login]' and password='".(md5($params["password"]))."'");
									}
									else {//Вывод сообщения об ошибке
										$this->data["page"]["data"]["error"] = "Пользователь с таким логином уже существует!";
										$this->data["page"]["code"] = "114";
									}										
								}
								else {//Вывод сообщения об ошибке
									$this->data["page"]["data"]["error"] = "Не введены логин или пароль пользователя!";
									$this->data["page"]["code"] = "112";
								}								
							}
							else {//Вывод сообщения об ошибке
								$this->data["page"]["data"]["error"] = "Нет доступа!";
								$this->data["page"]["code"] = "113";
							}
						break;
						
						default :
							$this->data["page"]["data"]["error"] = "Не выбран тип запроса!";
							$this->data["page"]["code"] = "111";
						break;
					}
				}
			}
			else {
				$this->data["page"]["error"] = "Ошибка логина или пароля!";
				$this->data["page"]["code"] = "010";
			}
			
			header('Content-type: application/xml; charset="windows-1251";',true);
			//header("Content-Transfer-Encoding: binary\n");
			$XmlData["body"] = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n".$this->array2xml($this->data);			
			$body = $this->GetControl("body");
			$body->dataSource = $XmlData;
			
			

		}
	}
?>
