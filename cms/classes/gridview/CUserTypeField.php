<?php
class CUserTypeField extends CGridDataField
{
	
	public $preAddEvent;
	
	public $preEditEvent;

	public static $_preAddHandler = "__preAddEvent";
	
	public static $_preEditHandler = "__preEditEvent";
	
	protected $subParams = array();
	
	protected $utdata = array();
	protected $utdataset = false;

	public function CUserTypeField()
	{
		$this->CGridDataField();
		$this->utdata = array(
			"user_type_1"=>false,
			"user_type_2"=>false,
			"user_type_3"=>false,
			"user_type_4"=>false,
			"user_type_5"=>false,
			"user_type_6"=>false,
			"user_type_7"=>false,
			"user_typeID_3"=>"",
			"user_typeID_4"=>"",
			"user_typeID_5"=>"",
			"user_typeID_6"=>"",
			"ut_other"=>"",
			"err"=>array());
	}

	public function __set($name,$value)
	{
		$this->subParams[$name] = $value;
	}

	public function __get($name)
	{
		return isset($this->subParams[$name])?$this->subParams[$name]:null;
	}

	public function PostInit()
	{
		if (!IsNullOrEmpty($this->preAddEvent))
		{
			$this->AddEvent(CGridDataField::$_preAddHandler,$this->preAddEvent,$this->ownerPage);
		}
		if (!IsNullOrEmpty($this->preEditEvent))
		{
			$this->AddEvent(CGridDataField::$$_preEditHandler,$this->preEditEvent,$this->ownerPage);
		}
	}
	
	protected function GetValue()
	{
		if ($this->utdataset) return;
		$user_id = 0;
		if (is_object($this->dataSource))
		{
			$user_id = $this->dataSource->tbl_obj_id;
		}
		elseif (is_array($this->dataSource))
		{
			$user_id = $this->dataSource['tbl_obj_id'];
		}
		else
		{
			$user_id = $this->dataSource;
		}
		
		$user_types = SQLProvider::ExecuteQuery(
			"select * from tbl__registered_user_types where user_id = ".$user_id);
		foreach($user_types as $key => $ut)
		{
			switch($ut["user_type"])
			{
			case "заказчик мероприятий" : 
				$this->utdata["user_type_1"] = true;
			break;
			case "организатор мероприятий" : 
				$this->utdata["user_type_2"] = true;
			break;
			case "представитель подрядчика" : 
				$this->utdata["user_type_3"] = true;
				$r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = $user_id and resident_type = 'contractor'");
				$this->utdata["user_typeID_3"] = $r_id[0]["rids"];
			break;
			case "представитель площадки" : 
				$this->utdata["user_type_4"] = true;
				$r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = $user_id and resident_type = 'area'");
				$this->utdata["user_typeID_4"] = $r_id[0]["rids"];
			break;
			case "представитель артиста" : 
				$this->utdata["user_type_5"] = true;
				$r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = $user_id and resident_type = 'artist'");
				$this->utdata["user_typeID_5"] = $r_id[0]["rids"];
			break;
			case "представитель агентства" : 
				$this->utdata["user_type_6"] = true;
				$r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = $user_id and resident_type = 'agency'");
				$this->utdata["user_typeID_6"] = $r_id[0]["rids"];
			break;
			default : 
				$this->utdata["user_type_7"] = true;
				$this->utdata["ut_other"] = $ut["user_type"];
			}
		}
		$this->utdataset = true;
		return 1;
	}

	protected function SetValue($value)
	{
		return;
	}

	protected function SetKeyValue($key,$value)
	{
		return;
	}
	
	public function RenderHead()
	{
		return "";
	}
	
	public function RenderItem()
	{
		return "";
	}

	protected function RenderLabel()
	{
		return "<td class=\"itemLabel\">Тип пользователя:</td>";
	}

	public function RenderAddLabel()
	{
		return $this->RenderLabel();
	}

	public function RenderEditLabel()
	{
		return $this->RenderLabel();
	}

	public function RenderAddItem()
	{
		return "";		
	}

	public function RenderEditItem()
	{
		$this->GetValue();
		$err_html = "";
		if (sizeof($this->utdata["err"])) {
			$err_html = '<span style="color: red;">';
			foreach($this->utdata["err"] as $key =>$err)
				$err_html .= "$err<br>";
			$err_html .= '</span><br>';
		}
		return '
		<td style="text-align: left; font-size:10pt;">'.$err_html.'
		<input type="checkbox" class="user_links_chb" name="user_type[1]" '.($this->utdata["user_type_1"]?'checked':'').' value="1">заказчик мероприятий<br>
		<input type="checkbox" class="user_links_chb" name="user_type[2]" '.($this->utdata["user_type_2"]?'checked':'').' value="2">организатор мероприятий<br>
		<input type="checkbox" class="user_links_chb" name="user_type[3]" '.($this->utdata["user_type_3"]?'checked':'').' value="3"/>представитель подрядчика&nbsp;&nbsp;
		ID: <input type="text" name="user_typeID[3]" size="20" value="'.$this->utdata["user_typeID_3"].'"><br>
		<input type="checkbox" class="user_links_chb" name="user_type[4]" '.($this->utdata["user_type_4"]?'checked':'').' value="4"/>представитель площадки&nbsp;&nbsp;
		ID: <input type="text" name="user_typeID[4]" size="20" value="'.$this->utdata["user_typeID_4"].'"><br>
		<input type="checkbox" class="user_links_chb" name="user_type[5]" '.($this->utdata["user_type_5"]?'checked':'').' value="5"/>представитель артиста&nbsp;&nbsp;
		ID: <input type="text" name="user_typeID[5]" size="20" value="'.$this->utdata["user_typeID_5"].'"><br>
		<input type="checkbox" class="user_links_chb" name="user_type[6]" '.($this->utdata["user_type_6"]?'checked':'').' value="6"/>представитель агентства&nbsp;&nbsp;
		ID: <input type="text" name="user_typeID[6]" size="20" value="'.$this->utdata["user_typeID_6"].'"><br>
		<input type="checkbox" class="user_links_chb" name="user_type[7]" '.($this->utdata["user_type_7"]?'checked':'').' value="7"/>другое&nbsp;&nbsp;
		<input type="text" name="user_typeID[7]" style="width: 250px;" value="'.$this->utdata["ut_other"].'"><br>
		</td>';
	}

	public function RenderAddPair()
	{
		return $this->RenderAddLabel().$this->RenderAddItem();
	}

	public function RenderEditPair()
	{
		return $this->RenderEditLabel().$this->RenderEditItem();
	}

	
	public function PreAdd()
	{
		return false;
	}
	public function PreEdit()
	{
		return false;
	}
	public function PostAdd()
	{
		return false;
	}
	public function PostEdit()
	{
		$user_id = 0;
		if (is_object($this->dataSource))
		{
			$user_id = $this->dataSource->tbl_obj_id;
		}
		elseif (is_array($this->dataSource))
		{
			$user_id = $this->dataSource['tbl_obj_id'];
		}
		else
		{
			$user_id = $this->dataSource;
		}
		$user_types = GP("user_type");
		$user_typesIDs = GP("user_typeID");
		$this->utdata = array(
			"user_type_1"=>false,
			"user_type_2"=>false,
			"user_type_3"=>false,
			"user_type_4"=>false,
			"user_type_5"=>false,
			"user_type_6"=>false,
			"user_type_7"=>false,
			"user_typeID_3"=>"",
			"user_typeID_4"=>"",
			"user_typeID_5"=>"",
			"user_typeID_6"=>"",
			"ut_other"=>"",
			"err"=>array());
		$cnt = 0;
		foreach( $user_types as $key=>$val)
		{
			$cnt++;
			$err_ut = "";
			$this->utdata["user_type_".$key] = true;
			if ($key > 2 && $key < 7)
			{
				$this->utdata["user_typeID_".$key] = $user_typesIDs[$key]; 
			}
			if ($key == 7)
			{
				
				$this->utdata["ut_other"] = $user_typesIDs[$key]; 
			}
			
			if ($key > 2 && $key < 7)
			{
				if (IsNullOrEmpty($user_typesIDs[$key]))
				{
					$err_ut = "не задан ID";
				} 
				else
				{
					$ids = preg_split("/[\s,]+/",$user_typesIDs[$key]);
					foreach($ids as $num => $rid)
					{
						if (!IsNullOrEmpty($rid) && !is_numeric($rid))
							$err_ut = "не верно задан ID";
					}
				}
				if (!IsNullOrEmpty($err_ut))
				{
					switch($key)
					{
						case 3 : $err_ut .= " подрядчика"; break;
						case 4 : $err_ut .= " площадки"; break;
						case 5 : $err_ut .= " артиста"; break;
						case 6 : $err_ut .= " агентства"; break;
					}
				}
			}
			elseif ($key == 7 && IsNullOrEmpty($user_typesIDs[$key]))
			{
				$err_ut = "не задан другой тип пользователя";
			}
			if (!IsNullOrEmpty($err_ut))
				array_push($this->utdata["err"],$err_ut);
		}
		if ($cnt > 3) array_push($this->utdata["err"],"количество типов не может быть больше трех");
		$this->utdataset = true;
		if (sizeof($this->utdata["err"]))
			return false;
		SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_types where user_id = ".$user_id);
		SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_link_resident where user_id = ".$user_id);
		foreach($user_types as $key)
		{
			$resident_type = "";
			switch($key)
			{
				case 3 : $resident_type = "contractor"; break;
				case 4 : $resident_type = "area"; break;
				case 5 : $resident_type = "artist"; break;
				case 6 : $resident_type = "agency"; break;
			}
			if (!IsNullOrEmpty($resident_type)) 
			{
				$ids = preg_split("/[\s,]+/",$user_typesIDs[$key]);
				foreach ($ids as $num => $r_id)
				{
					if (is_numeric($r_id))
					SQLProvider::ExecuteNonReturnQuery("insert into tbl__registered_user_link_resident(user_id,resident_type,resident_id)
													values (".$user_id.", '".$resident_type."', ".intval($r_id).")");
				}
				
			}
			$ut = "";
			switch ($key)
			{
				case 1 : $ut = "заказчик мероприятий"; break;
				case 2 : $ut = "организатор мероприятий"; break;
				case 3 : $ut = "представитель подрядчика"; break;
				case 4 : $ut = "представитель площадки"; break;
				case 5 : $ut = "представитель артиста"; break;
				case 6 : $ut = "представитель агентства"; break;
				case 7 : $ut = $user_typesIDs[$key]; break;
			}
			SQLProvider::ExecuteNonReturnQuery("insert into tbl__registered_user_types(user_id,user_type)
													values (".$user_id.", '".$ut."')");
		}
		
		return false;
	}

}
?>