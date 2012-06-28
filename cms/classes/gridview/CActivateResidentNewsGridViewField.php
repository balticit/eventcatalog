<?php
class CActivateResidentNewsGridViewField extends CXMLLookupGridDataField 
{
	public $customTemplate = "<a href=\"{link}\">{text}</a>";
	
	public $visibleOnEdit = false;
	
	public $visibleOnAdd = false;

	public function CActivateGridViewField()
	{
		$this->CXMLLookupGridDataField();
	}

	protected function CustomRender($value)
	{
		//die('!');
		$params = array();
		CopyArray(&$_GET,&$params);
		$params[$this->parentId]["mode"] = "customaction";
		$params[$this->parentId]["action"] = $this->customAction;
		$params[$this->parentId]["id"] = $value;
		$params[$this->parentId]["idField"] = $this->dbField;
		$params[$this->parentId]["idType"] = $this->fieldType;
		$act = isset($this->dataSource["active"])?$this->dataSource["active"]:"";
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
		$user = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$args["id"]));
		if (!is_null($user))
		{
			if ($args["args"]["value"]==1)
			{
				$app = CApplicationContext::GetInstance();
				$user->registration_confirmed = 1;
				$user->active = 1;
				$table->UpdateObject(&$user);
				$title = iconv($app->appEncoding,"utf-8","Размещение новости резидента");
				$mbody = iconv($app->appEncoding,"utf-8","
Спасибо за размещенную Вами новость!<br>
Можете просмотреть <a href=http://eventcatalog.ru/resident_news/news/id/".$user->tbl_obj_id."/>публикацию</a> и если у Вас возникнут какие-либо замечания или пожелания, пожалуйста пишите на адрес: <a href=mailto:catalog@eventcatalog.ru>catalog@eventcatalog.ru</a> или звоните по телефону (495) 740-31-88.<br>
<br>
До встречи на страницах ЕVENT КАТАЛОГА!<br>
<br>
С уважением,<br>
<a href=http://www.eventcatalog.ru>www.evencatalog.ru</a> – ежедневный помощник организатора мероприятий.

				");
				SendHTMLMail($user->email,$mbody,$title);
			}
			else
			{
				$app = CApplicationContext::GetInstance();
				$user->active = 0;
				$table->UpdateObject(&$user);
				$title = iconv($app->appEncoding,"utf-8",$this->messageHeader);
				$mbody = file_get_contents(RealFile($this->messageFileDeactivate));
				//SendHTMLMail($user->email,$mbody,$title);
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