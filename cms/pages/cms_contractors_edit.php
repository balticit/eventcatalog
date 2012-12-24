<?php
class cms_contractors_edit_php extends CCMSPageCodeHandler
{
	public function cms_contractors_edit_php()
	{
		$this->CCMSPageCodeHandler();
	}

	private function CreateLogo($login,$files,$key="logo",$prefix = IMAGE_LOGO_PREFIX,$sizeLimit =IMAGE_LOGO_SIZE_LIMIT,$overwrite = true)
	{
		if (isset($files["name"][$key]))
		{
			if (($files["error"][$key]==0)&&(is_file($files["tmp_name"][$key]))&&($files["size"][$key]<=$sizeLimit) )
			{
				$login = preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$login));
				$fn =preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$files["name"][$key]));
				$newpath = IMAGES_UPLOAD_DIR.$login.IMAGE_PATH_SEMISECTOR.$prefix.IMAGE_PATH_SEMISECTOR.$fn;
				if (is_file(ROOTDIR.$newpath))
				{
					if ($overwrite)
					{
						unlink(ROOTDIR.$newpath);
					}
					else
					{
						return null;
					}
				}
				return  (move_uploaded_file($files["tmp_name"][$key],ROOTDIR.$newpath))?$newpath:null;
			}
		}
	}

	private function CleanLogo(&$file)
	{
		if (is_file(ROOTDIR.$file))
		{
			unlink(ROOTDIR.$file);
		}
		$file = "";
	}

	public function PreRender()
	{
		$userId = GP("userid",0);
		$account = $this->GetControl("account");
		$account->key = "contractor";
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>"");
		$junksaleSellList = "";
		$junksaleBuyList = "";
		$personalCVList = "";
		$personalVacancyList = "";

		$activities = SQLProvider::ExecuteQuery("select * from `vw__activity_hierarcy2contractors` ");

		$citySelect = new CSelect();
		$citySelect->dataSource = SQLProvider::ExecuteQuery("select * from `tbl__city` ");
		$citySelect->titleName="title";
		$citySelect->valueName="tbl_obj_id";
		$citySelect->name="properties[city]";

		$table = new CNativeDataTable("tbl__contractor_doc");
		$kindList = array();

		$userData = $table->SelectUnique(new CEqFilter($table->fields["tbl_obj_id"],$userId),false);
		if (is_null($userData))
		{
			$account->key = "notfound";
		}
		else
		{
			$kindList1 = SQLProvider::ExecuteQueryReverse("select distinct kind_of_activity from tbl__contractor2activity where tbl_obj_id=$userData->tbl_obj_id");
			$kindList = $kindList1["kind_of_activity"];
			$citySelect->selectedValue = $userData->city;
			$jlist = $this->GetControl("junksaleSellList");
			$jlist->pid = $userId;
			$junksaleSellList = $jlist->RenderHTML();
			$jblist = $this->GetControl("junksaleBuyList");
			$jblist->pid = $userId;
			$junksaleBuyList = $jblist->RenderHTML();
			$jclist = $this->GetControl("personalCVList");
			$jclist->pid = $userId;
			$personalCVList = $jclist->RenderHTML();
			$jvlist = $this->GetControl("personalVacancyList");
			$jvlist->pid = $userId;
			$personalVacancyList = $jvlist->RenderHTML();

			if ($this->IsPostBack)
			{
				$props = GP("properties");
				if (is_array($props))
				{
					$userValidator = $this->GetControl("contractorValidator");
					print_r($userValidator);
					unset($props["login"]);
					unset($userValidator->validationItems["login"]);
					if (IsNullOrEmpty($props["password"])&&IsNullOrEmpty($props["password_confirm"]))
					{
						unset($userValidator->validationItems["password"]);
						unset($userValidator->validationItems["password_confirm"]);
						unset($userValidator->validationItems["password_req"]);
						unset($props["password"]);
						unset($props["password_confirm"]);
					}

					$kindList =GP(array("properties","kind_of_activity"),array());
					$citySelect->selectedValue = $props["city"];
					$errorsData = $userValidator->Validate(&$props);
					$userData->FromHashMap($props);
					
					if($props["priority"] == '0') {
            $userData->priority = null;
          }
					
					if (sizeof($errorsData)>0)
					{
						$errors->dataSource = array("message"=>$errorsData[0]);
					}
					else
					{

						if (isset($props["password"]))
						{
							if (!IsNullOrEmpty($props["password"]))
							{
								$userData->password = md5($props["password"]);
							}
						}

						$cleanlogo = GP("logo_clean");
						if ($cleanlogo=="clean")
						{
							$this->CleanLogo($userData->logo_image);
						}
						$flogo = $_FILES["properties"];
						if (is_array($flogo))
						{
							$logo = $this->CreateLogo($userData->login,$flogo);
							if (!is_null($logo))
							{
								$userData->logo_image = $logo;
							}
						}
						$table->UpdateObject(&$userData);
						SQLProvider::ExecuteNonReturnQuery("delete from tbl__contractor2activity where tbl_obj_id=$userData->tbl_obj_id");
						foreach ($kindList as $kind)
						{
							SQLProvider::ExecuteNonReturnQuery("insert into tbl__contractor2activity values ($userData->tbl_obj_id,$kind)");
						}
						$citySelect->selectedValue = $userData->city;

					}
				}
			}
			$actList = $this->GetControl("actList");
			$akeys = array_keys($activities);
			foreach ($akeys as $akey)
			{
				$activities[$akey]["checked"] = array_search($activities[$akey]["child_id"],$kindList)===false?"": "checked=\"checked\"";
			}
			$actList->dataSource = $activities;
			$logos = preg_split("/\//",$userData->logo);
			$ls = sizeof($logos);
			$udata = array("login_readonly"=>"readonly=\"true\"",
			"submit_text"=>$this->GetMessage("save"),
			"actList"=>$actList->RenderHTML(),
			"city_list"=>$citySelect->RenderHTML(),
			"junksaleSellList"=>$junksaleSellList,
			"junksaleBuyList"=>$junksaleBuyList,
			"personalCVList"=>$personalCVList,
			"personalVacancyList"=>$personalVacancyList);
			$userData->logo_image = ($ls==0)?null:$logos[$ls-1];
			$account->dataSource = array_merge($userData->GetData(),$udata);
		}
	}

}

?>
