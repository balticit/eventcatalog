<?php
class registration_activate_php extends CPageCodeHandler
{
	public function registration_activate_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$code = GP("code",0);
		$confirm = $this->GetControl("confirm");
		$user = SQLProvider::ExecuteQuery("SELECT
			  `tbl_obj_id`,
			  `login_type`
			FROM 
			  `vw__all_users` us 
			  where us.registration_confirmed=0 and us.registration_confirm_code='$code'");
		if (sizeof($user)==0)
		{
			return ;
		}
		$user = $user[0];
		$table = null;
		switch ($user["login_type"]) {
			case "user":
			$table = 'tbl__registered_user';
			break;
			case "agency":
			$table = 'tbl__agency_doc';
			break;
			case "area":
			$table = 'tbl__area_doc';
			break;
			case "contractor":
			$table = 'tbl__contractor_doc';
			break;
			case "artist":
			$table = 'tbl__artist_doc';
			break;
		}
		if (is_null($table))
		{
			return ;
		}
        $date = date("Y-m-d");
		SQLProvider::ExecuteNonReturnQuery("update $table set registration_confirmed=1, registration_date='$date' where registration_confirm_code='$code'");
		$affRows = SQLProvider::GetLastAffectedRows();
		if ($affRows==0)
		{
			return ;
		}
		$confirm->key = 'valid';
	}

}
?>
