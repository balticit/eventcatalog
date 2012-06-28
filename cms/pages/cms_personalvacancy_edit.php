<?php
	class cms_personalvacancy_edit_php extends CCMSPageCodeHandler 
	{
		public function cms_personalvacancy_edit_php()
		{
			$this->CCMSPageCodeHandler();
		}
		
			public function PreRender()
	{
		$tableName = "tbl__personal_organizator_vacancy_doc";
		$table = new CNativeDataTable($tableName);
		$id = GP("id",0);
		$error = "";
		$persRow = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$id));
		$personal = $this->GetControl("account");
		if (is_null($persRow))
		{
			$personal->key = "notfound";
		}
		else 
		{
			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$vName = "vacancyValidator";
				$persValidator = $this->GetControl($vName);
				$errorsData = $persValidator->Validate(&$props);
				$persRow->FromHashMap($props);
				if (sizeof($errorsData)>0)
				{
					$error = $errorsData[0];
				}
				else
				{
					$persRow->registration_date = date("d-m-Y");
					$table->UpdateObject(&$persRow);
				}
			}
		}
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>$error);
		$personal = $this->GetControl("account");
		$personal->key = "vacancy";
		$personal->dataSource = $persRow;
	}
	}
?>
