<?php
	class replies_add_php extends CPageCodeHandler 
	{
		public function replies_add_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$su = new CSessionUser("user");
			CAuthorizer::RestoreUserFromSession(&$su);
			if ($su->authorized)
			{
				$addReply = $this->GetControl("addReply");
				if (!$this->IsPostBack)
				{
					$addReply->key="post";
				}
				else 
				{
					$id = GP("id","");
					$type=GP("type","");
					$obj = SQLProvider::ExecuteQuery("select * from `vw__identity` where `tbl_obj_id`=$id and login_type='$type' ");
					$sender = SQLProvider::ExecuteQuery("select * from `vw__identity` where `tbl_obj_id`=$su->id and login_type='$su->type' ");
					if ((sizeof($obj)==1)&&(sizeof($sender)==1))
					{
						$table = new CNativeDataTable("tbl__replies");
						$replyRow = $table->CreateNewRow();
						$replyRow->date = date("Y-m-d H:i:s ");
						$mess = htmlspecialchars(GP("reply",""));
						$replyRow->message = strlen($mess)>255?substr($mess,0,255):$mess; 
						$replyRow->parent_id = $obj[0]["tbl_obj_uid"];
						$replyRow->sender_id = $sender[0]["tbl_obj_uid"];
						$replyRow->active = 0;
						$table->InsertObject($replyRow,false);
						$addReply->key="thank";
					}
				}
			}
		}
	}
?>
