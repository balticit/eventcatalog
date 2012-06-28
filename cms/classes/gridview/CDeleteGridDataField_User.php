<?php
class CDeleteGridDataField_User extends CGridDataField 
{
	public $deleteTemplate = "<a href=\"{link}\" {confirm}>{deleteText}</a>";

	public $deleteText = "";

	public $deleteConfirmFunction;
	
	public $visibleOnEdit = false;
	
	public $visibleOnAdd = false;
	
	public $sortable = false;
	
	public function CDeleteGridDataField_User()
	{
		$this->CGridDataField();
	}
	
	protected  function BuildDeleteValue($value)
	{
		$params = array();
		CopyArray(&$_GET,&$params);
		//$params[$this->parentId]["mode"] = "delete";
		$params[$this->parentId]["mode"] = "customaction";
		$params[$this->parentId]["action"] = $this->customAction;
		$params[$this->parentId]["id"] = $value;
		$params[$this->parentId]["idField"] = $this->dbField;
		$params[$this->parentId]["idType"] = $this->fieldType;
		$link = CURLHandler::BuildFullLink($params);
		$confirm = IsNullOrEmpty($this->deleteConfirmFunction)?"":"onclick=\"javascript:return $this->deleteConfirmFunction;\"";
		return CStringFormatter::Format($this->deleteTemplate,array("deleteText"=>$this->deleteText,"link"=>$link,"confirm"=>$confirm));
	}
	
	public function CustomAction(&$sender,&$args = array())
	{
		$uinfo = SQLProvider::ExecuteQuery("select * from tbl__registered_user where tbl_obj_id=".$args["id"]);
		$uinfo = $uinfo[0];
		SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user where tbl_obj_id=".$args["id"]);
		SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_link_resident where user_id=".$args["id"]);
		SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_types where user_id=".$args["id"]);
		
		$app = CApplicationContext::GetInstance();
		$title = iconv($app->appEncoding,"utf-8","¬аша учетна€ запись пользовател€ была удалена");
		$mbody = CStringFormatter::Format(file_get_contents(RealFile("/cms/settings/misc/del_mail.htm")),array("link"=>"http://".$_SERVER['HTTP_HOST']."/registration/type/user/"));
		SendHTMLMail($uinfo["email"],$mbody,$title);
		/*$table =new CNativeDataTable($sender->tableName);
		$user = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$args["id"]));
		if (!is_null($user))
		{
			if ($args["args"]["value"]==1)
			{
				$app = CApplicationContext::GetInstance();
				$user->registration_confirmed = 1;
				$user->active = 1;
				$table->UpdateObject(&$user);
				$title = iconv($app->appEncoding,"utf-8",$this->messageHeader);
				$mbody = file_get_contents(RealFile($this->messageFile));
				SendHTMLMail($user->email,$mbody,$title);
			}
			else
			{
				$app = CApplicationContext::GetInstance();
				$user->active = 0;
				$table->UpdateObject(&$user);
				$title = iconv($app->appEncoding,"utf-8",$this->messageHeader);
				$mbody = file_get_contents(RealFile($this->messageFileDeactivate));
				SendHTMLMail($user->email,$mbody,$title);
			}
		}*/
		$sender->mode = "list";			
	}
	
	public function RenderItem()
	{
		$value = $this->GetValue();
		$cellContent = $this->BuildDeleteValue($value);
		return CStringFormatter::Format($this->itemTemplate,array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}
}
?>