<?php
class messages_php extends CPageCodeHandler
{
	public $action;
	
	public $reciever_type;
	
	public $reciever_id;
	
	public $reciever_data;
	
	public $reply_id;
	
	public $reply_mess;
	
	public $is_sent;
	
	public function messages_php()
	{
		$this->CPageCodeHandler();
	}
	
	public function PreRender()
	{
		$su = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$su);
		if (!$su->authorized)
		{
			CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);
		}
		
		$this->action = GP("action");
		if ($this->action=="compose")
		{
			$this->reciever_id = GP("id");
			$this->reciever_type = GP("type");
		}
		elseif($this->action=="reply"||$this->action=="reject"||$this->action=="delete") 
		{
			$this->reply_id = GPT("rid");
			$r_mess = SQLProvider::ExecuteQuery("select * from tbl__messages where tbl_obj_id=$this->reply_id and status='sent'");
			if ((sizeof($r_mess)==0)||($r_mess[0]["status"]!="sent"))
			{
				CURLHandler::Redirect("/u_cabinet?data=my_messages");
			}
			$sid = $r_mess[0]["sender_id"];
			$matches = array();
			preg_match("/([a-z]+)(\d+)/i",$sid,&$matches);
			$this->reciever_id = $matches[2];
			$this->reciever_type = $matches[1];
			$this->reply_mess = $r_mess[0];
			if ($this->action=="reject"||$this->action=="delete")
			{
				$uid = $su->type.$su->id;
				if ($this->action=="reject"&&$this->reply_mess["reciever_id"]==$uid&&$this->reply_mess["status"]=="sent")
				{
					SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='rejected' where tbl_obj_id=$this->reply_id");
				}
				if ($this->action=="delete"&&$this->reply_mess["sender_id"]==$uid&&($this->reply_mess["status"]=="sent"||$this->reply_mess["status"]=="rejected"))
				{
					SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='deleted' where tbl_obj_id=$this->reply_id");
				}
				CURLHandler::Redirect("/u_cabinet?data=my_messages");
			}
			
		}
		elseif ($this->action=="sent")
		{
			$this->is_sent = true;
			return ;
		}
		
		$this->reciever_data = SQLProvider::ExecuteQuery("select * from vw__all_users_full where user_id=$this->reciever_id and `type`='$this->reciever_type'");
		if (sizeof($this->reciever_data)==0)
		{
			CURLHandler::Redirect("/u_cabinet?data=my_messages");
		}
		$this->reciever_data = $this->reciever_data[0];
		
		if ($this->IsPostBack)
		{
			$table = new CNativeDataTable("tbl__messages");
			
			$newMessage = $table->CreateNewRow(true);
			$newMessage->text = GP("message_text");
			$newMessage->time = date("Y-m-d H:i:s");
			$newMessage->reciever_id = $this->reciever_data["user_key"];
			$newMessage->sender_id = $su->type.$su->id;
			if ($this->action=="reply")
			{
				$newMessage->reply_to_id = $this->reply_mess["tbl_obj_id"];
				$newMessage->thread_id = $this->reply_mess["thread_id"];
			}
			$table->InsertObject(&$newMessage);
			if ($this->action=="reply")
			{
				SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='replied' where tbl_obj_id=$this->reply_id");
			}
			else 
			{
				$newMessage->thread_id = $newMessage->tbl_obj_id;
				$table->UpdateObject($newMessage);
			}
			CURLHandler::Redirect("/message?action=sent");
		}
	}
}
?>
