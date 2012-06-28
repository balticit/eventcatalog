<?php
class CActivateForumGridViewField extends CXMLLookupGridDataField 
{
	public $customTemplate = "<a href=\"{link}\">{text}</a>";
	
	public $visibleOnEdit = false;
	
	public $visibleOnAdd = false;

	public function CActivateForumGridViewField()
	{
		$this->CXMLLookupGridDataField();
	}

	protected function CustomRender($value)
	{
		$params = array();
		CopyArray(&$_GET,&$params);
		$params[$this->parentId]["mode"] = "customaction";
		$params[$this->parentId]["action"] = $this->customAction;
		$params[$this->parentId]["id"] = $value;
		$params[$this->parentId]["idField"] = $this->dbField;
		$params[$this->parentId]["idType"] = $this->fieldType;
		$act = isset($this->dataSource["user_active"])?$this->dataSource["user_active"]:"";
		$text = isset($this->lookupTable[$act])?$this->lookupTable[$act]:"";
		if (IsNullOrEmpty($text))
		{
			if (isset( $params[$this->parentId]["args"]["value"]))
			{
				unset($params[$this->parentId]["args"]["value"]);
			}
		}
		else
		{
			$params[$this->parentId]["args"]["value"] = $act==0?1:0;
		}
		$link = CURLHandler::BuildFullLink($params);
		return CStringFormatter::Format($this->customTemplate,
		array("text"=>$text,"link"=>$link));
	}

	public function CustomAction(&$sender,&$args = array())
	{
		$table =new CNativeDataTable($sender->tableName);
		$user = $table->SelectUnique(new CEqFilter(&$table->fields["user_id"],$args["id"]));
		if (!is_null($user))
		{
			
			
			if ($args["args"]["value"]==1)
			{
				$app = CApplicationContext::GetInstance();
				$user->user_active = 1;
				$table->UpdateObject(&$user);
				$title = iconv($app->appEncoding,"utf-8",$this->messageHeader);
				$mbody = file_get_contents(RealFile($this->messageFile));
				SendHTMLMail($user->user_email,$mbody,$title);
			}
			else
			{
				$app = CApplicationContext::GetInstance();
				$user->user_active = 0;
				$table->UpdateObject(&$user);
				$title = iconv($app->appEncoding,"utf-8",$this->messageHeader);
				$mbody = file_get_contents(RealFile($this->messageFileDeactivate));
				SendHTMLMail($user->user_email,$mbody,$title);
			}
		}
		$sender->mode = "list";
	}
	
	public function RenderItem()
	{
		$value = $this->GetValue();
		$cellContent = $this->CustomRender($value);
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}
}
?>