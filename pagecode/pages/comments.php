<?php
require_once(ROOTDIR."captcha/kcaptcha.php");
class comments_php extends CPageCodeHandler
{
	public $anonymous = false;
	public $hasComments = false;
	public $c_error = false;
	public $c_text;
	public $c_author;
	public $captcha_sid;
	public $comments;
	public $user_title;
	public $user_logo;
	public $r500;
	public $r100;
	public $is_cabinet;
	public $auth_error;
	public $regex_url;

	public function comments_php()
	{
		$this->CPageCodeHandler();
	}

	private function ValidateCaptcha()
	{
		if (!$this->anonymous)
		{
			return true;
		}

		$sess_id = GP("comment_captcha");
		$captcha = GP("comment_captcha_input");
		return  $_SESSION[$sess_id]==$captcha;
	}

	private function ProcessComments($coments)
	{
		$results = array();
    foreach ($coments as &$comment)
			$comment['text'] = ProcessMessage($comment['text']);
		$i=0;
		$parent_ids = array();
		$level=0;

		while (sizeof($coments)>0)
		{
			if (sizeof($parent_ids)==0)
			{
                $item = array_shift($coments);
				array_unshift(&$parent_ids, $item["tbl_obj_id"]);
				$item["level"] = 0;
				$i=0;
				array_push(&$results,$item);
				$level=0;
			}
			else {
				if ($parent_ids[0]==$coments[$i]["reply_to_id"])
				{
					CDebugger::DebugLine('test');
					$level++;
					array_unshift(&$parent_ids, $coments[$i]["tbl_obj_id"]);
					$coments[$i]["level"] = $level;
					array_push(&$results,$coments[$i]);
					array_splice(&$coments,$i,1);
					$i--;
				}
				$i++;
			}
			if ($i==sizeof($coments))
			{
				array_shift(&$parent_ids);
				$level--;
				$i=0;
			}
		}
		return $results;
	}


	public function ProcessImage(&$newComment,$table)
	{
		$file = $_FILES["comment_file"];

		if ($file["error"] === 0 &&
        $file["size"]<=IMAGE_LAGRE_SIZE_LIMIT &&
        is_file($file["tmp_name"])){
			$ext = "";
			switch ($file["type"]){
				case "image/jpeg":
				  $ext = "jpg";
				  break;
				case "image/pjpeg":
				  $ext = "jpg";
				  break;
				case "image/png":
				  $ext = "png";
				  break;
				case "image/gif":
				  $ext = "gif";
				  break;
			}

      $res = new ResizeImage($file["tmp_name"]);
      $res->resize(IMAGE_COMMENT_MAX_WIDTH, IMAGE_COMMENT_MAX_WIDTH,
                   ROOTDIR.COMMENT_IMAGES_UPLOAD_DIR.$newComment->tbl_obj_id.".$ext", false);
      $res->resize(IMAGE_COMMENT_THUMB_WIDTH, IMAGE_COMMENT_THUMB_HEIGHT,
                   ROOTDIR.COMMENT_IMAGES_UPLOAD_DIR.$newComment->tbl_obj_id."_small.$ext", true);
			unset($res);
			$newComment->image = COMMENT_IMAGES_UPLOAD_DIR.$newComment->tbl_obj_id."_small.$ext";
			$newComment->image_large = COMMENT_IMAGES_UPLOAD_DIR.$newComment->tbl_obj_id.".$ext";
			$table->UpdateObject(&$newComment);
		}
	}

	public function PreRender()
	{

		$user = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$user);

		$authresult=false;

		$action = GP("comments_action","load");
		$ctype = GP("type");
		$cid = GPT("id");
		$reply_id = GPT("comment_reply_id");
		$this->is_cabinet = GP("cabinet")==1;

		if ((GP("comment_auth")==1)&&(!$user->authorized))
		{
			$login = GP("login");
			$password = GP("password");

			if ($login!=NULL) {
				$authresult = CAuthorizer::AuthentificateUser($login,$password,&$user);
				if ($authresult == false){
					$this->auth_error = "Неверный логин либо пароль";
					$action = "load";
				}
				if ($authresult == true && GP("remeber")=="remember") {
					setcookie(COOKIE_USER_AUTH,$login,time()+60*60*24*14,"/");
				}

			}
		}

		if (!$user->authorized)
		{
			$this->anonymous = true;
			$this->captcha_sid = md5(uniqid(rand(), true));
		}
		else
		{
			$user_data = SQLProvider::ExecuteQuery("select * from (
						select tbl_obj_id as user_id,IFNULL(nikname,title) title,logo,comments_count,comments_ban, 'user' as `type` from tbl__registered_user
						union all
						select tbl_obj_id,title,logo,comments_count,comments_ban, 'area' from tbl__area_doc
						union all
						select tbl_obj_id,title,logo,comments_count,comments_ban, 'artist' from tbl__artist_doc
						union all
						select tbl_obj_id,title,logo_image,comments_count,comments_ban, 'agency' from tbl__agency_doc
						union all
						select tbl_obj_id,title,logo_image,comments_count,comments_ban, 'contractor' from tbl__contractor_doc
					)  u
					where u.user_id=$user->id and u.type='$user->type'");
			$this->user_title = $user_data[0]["title"];
			$this->user_logo = $user_data[0]["logo"];

			if  ($user_data[0]["comments_count"]>=500)
			{
				$this->r500 = (int)( $user_data[0]["comments_count"]/500);
			}

			if ($user_data[0]["comments_count"]%500>=100)
			{
				$this->r100 = (int)(($user_data[0]["comments_count"]%500)/100);
			}

		}



		if ($action=="add")
		{
			if (($this->auth_error==null)&&($this->IsPostBack)&&($this->ValidateCaptcha()))
			{
				$table = new CNativeDataTable("tbl__comments");
				$newComment = $table->CreateNewRow(true);
				$newComment->text = GP("comment_text");
				if ($reply_id>0)
				{
					$newComment->reply_to_id = $reply_id;
					$comment_data = SQLProvider::ExecuteQuery("select * from tbl__comments where tbl_obj_id=$reply_id");
					$newComment->target_id = $comment_data[0]["target_id"];


				}
				else
				{
					$newComment->target_id = $ctype.$cid;

				}

				if ($this->anonymous)
				{
					$newComment->sender_name = GP("comment_author");
					$newComment->active =0;
				}
				else
				{
					$newComment->sender_id = $user->type.$user->id;
					$newComment->active =1;
				}
				$newComment->time = date("Y-m-d H:i:s");
				//$newComment->active =1;
				$table->InsertObject(&$newComment);
				$this->ProcessImage(&$newComment,$table);
				//Отправка сообщения о комментарии
				$residend_info = SQLProvider::ExecuteQuery(
					"SELECT u.tbl_obj_id, u.login_type, u.email
					FROM `vw__all_users` u where CONCAT(u.login_type,u.tbl_obj_id) = '".$newComment->target_id."'");
				if (sizeof($residend_info)) {
					$app = CApplicationContext::GetInstance();
                    $link = $_SERVER['HTTP_HOST'].'/'.$residend_info[0]['login_type'].'/details/id/'.$residend_info[0]['tbl_obj_id'];
                    $mtitle = iconv($app->appEncoding,"utf-8","Вам написали отзыв на портале eventcatalog.ru");
					$mbody = iconv($app->appEncoding,"utf-8",'<div id="content"><p>Уважаемый резидент!</p>'.
					'<p>Вам был написан отзыв.</p>'.
					'<p>Прочитать и ответить Вы можете перейдя по ссылке: <a target="_blank" href="http://'.$link.'">http://'.$link .'</a></p>'.
					'<p>С уважением,<br />'.
					'EVENTКАТАЛОГ<br />'.
					'Ежедневный помощник организаторов мероприятий.</p></div>');
					SendHTMLMail($residend_info[0]['email'],$mbody,$mtitle);
				}
				if (!$this->anonymous)
				{
					$table_name = $user->GetTable();
					SQLProvider::ExecuteNonReturnQuery("update $table_name set comments_count=
					(select count(*) from tbl__comments
					where sender_id=$user->id and sender_name = '$user->type' and active=1 and is_deleted=0)
					where tbl_obj_id=$user->id");
				}
			}
			else
			{
				if (GP("comment_auth")==0) {
					$this->c_error = true;
					$this->c_author = GP("comment_author");
					$this->c_text = GP("comment_text");
				}
			}

			$action = "load";
		}

		if ($action=="edit")
		{
			if (($this->auth_error==null)&&($this->IsPostBack))
			{
				$table = new CNativeDataTable("tbl__comments");
				$newComment = $table->SelectUnique(new CAndFilter(new CEqFilter($table->fields["sender_id"],$user->type.$user->id),
					new CEqFilter($table->fields["tbl_obj_id"],$reply_id)));
			   if ($newComment!=null)
			   {
			   		$newComment->text = GP("comment_text");
			   		if (GP("comment_image_overwrite")==1)
			   		{
			   			unlink(ROOTDIR.$newComment->image);
			   			$newComment->image = "";

			   		}
			   		$this->ProcessImage(&$newComment,$table);
			   		$table->UpdateObject(&$newComment);
			   }
			}
			$action = "load";
		}

		if (($action=="delete")&&($this->IsPostBack)&&($user->authorized))
		{
			$reply_id = GPT("comment_reply_id");
			if ($reply_id>0)
			{
				$comment_data = SQLProvider::ExecuteQuery("select * from tbl__comments where tbl_obj_id=$reply_id");
				$img = $comment_data[0]["image"];
				if (strlen($img)>0)
				{
					unlink(ROOTDIR.$img);
				}
				$img = $comment_data[0]["image_large"];
				if (strlen($img)>0)
				{
					unlink(ROOTDIR.$img);
				}
				SQLProvider::ExecuteNonReturnQuery("update tbl__comments set is_deleted=1 where tbl_obj_id=$reply_id");

				$table_name = $user->GetTable();

				SQLProvider::ExecuteNonReturnQuery("update $table_name set comments_count=
					(select count(*) from tbl__comments
					where sender_id=$user->id and sender_name = '$user->type' and active=1 and is_deleted=0)
					where tbl_obj_id=$user->id");
			}

			$action = "load";
		}

		if ($authresult)
		{
			die('<html><body><script type="text/javascript">top.location.href = top.location.href+"";</script></body></html>');
		}

		if ($action=="load")
		{
			if ($this->is_cabinet)
			{
				$uid = $user->type.$user->id;

				$pageSize = 20;
				$page = GP("page",1);

				SQLProvider::ExecuteNonReturnQuery("drop temporary table if exists `tmp_comments`");
				SQLProvider::ExecuteNonReturnQuery("create temporary table `tmp_comments`
				(
					tbl_obj_id int(5) not null,
					`time` datetime not null,
					order_no int(4) not null,
					sub_order_no int(4) not null
				) ENGINE = MYISAM");
				SQLProvider::ExecuteNonReturnQuery("drop temporary table if exists `tmp_comments_ids`");
				SQLProvider::ExecuteNonReturnQuery("create temporary table `tmp_comments_ids`
				(
					tbl_obj_id int(5) not null,
					order_no int(4) not null
				) ENGINE = MYISAM");
				SQLProvider::ExecuteNonReturnQuery("set @order_no =0");
				SQLProvider::ExecuteNonReturnQuery("insert into tmp_comments
					select tbl_obj_id,`time`,@order_no:=@order_no+1,0 from
					(select tbl_obj_id,`time` from tbl__comments
					where sender_id='$uid'
					order by time desc) t");
				SQLProvider::ExecuteNonReturnQuery("insert into tmp_comments_ids
					select tbl_obj_id,order_no from tmp_comments");
				SQLProvider::ExecuteNonReturnQuery("set @order_no =0");
				SQLProvider::ExecuteNonReturnQuery("set @sub_order_no =0");
				SQLProvider::ExecuteNonReturnQuery("insert into tmp_comments(tbl_obj_id,`time`,sub_order_no,order_no)
					select tbl_obj_id,`time`,@sub_order_no:=if(@order_no=order_no,@sub_order_no+1,1),@order_no:=order_no from
					(select c.tbl_obj_id,`time`,i.order_no from tbl__comments c
						inner join tmp_comments_ids i on c.reply_to_id=i.tbl_obj_id
					where sender_id!='$uid'
					order by i.order_no asc, time asc) t");

				$this->comments = SQLProvider::ExecuteQuery("select c.*,u.*,if(sub_order_no=0,0,1) `level` from tbl__comments c
					inner join tmp_comments tc on c.tbl_obj_id=tc.tbl_obj_id
					left join vw__all_users_full u on c.sender_id=u.user_key
				order by order_no,sub_order_no
				limit ".(($page-1)*$pageSize).",$pageSize");

				$count = SQLProvider::ExecuteQuery("select count(*) as quan from tbl__comments c
					inner join tmp_comments tc on c.tbl_obj_id=tc.tbl_obj_id");
				$count = $count[0]["quan"];
				$pages = floor($count/$pageSize)+(($count%$pageSize==0)?0:1);

				$pager = $this->GetControl("pager");
				$pager->currentPage = $page;
				$pager->totalPages = $pages;

			}
			else
			{
				$comments = SQLProvider::ExecuteQuery("select * from tbl__comments c
						left join vw__all_users_full u on c.sender_id=u.user_key
						where c.target_id='$ctype"."$cid' order by c.time");

				$this->comments = $this->ProcessComments($comments);
			}

      $cnt_not_deleted = 0;
      foreach($this->comments as $c)
        if ($c['is_deleted'] != 1) $cnt_not_deleted++;
			$this->hasComments = $cnt_not_deleted;
			$yesterday  = date("Y-m-d",  mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));
			$today  = date("Y-m-d");
			$tmin = date("i");
			$thour = date("H");
			$tHmin = $thour*60+$tmin;
			$this->regex_url = "/(ht|f)tp(s?)\\:\\/\\/[0-9a-zA-Z]([-.\\w]*[0-9a-zA-Z])*(:(0-9)*)*(\\/?)([a-zA-Z0-9\\-\\.\\?\\,\\'\\/\\\\\\+&amp;%\\$#_]*)?/m";
			for ($i=0;$i<sizeof($this->comments);$i++)
			{

				$urls = array();
				$this->comments[$i]["u_text"] = $this->comments[$i]["text"];
				preg_match_all($this->regex_url,$this->comments[$i]["text"],&$urls);
				$urls = $urls[0];

				if (sizeof($urls)>0)
				{
					$url_repls = array();
					for ($j=0;$j<sizeof($urls);$j++)
					{
						$sublen = floor(strlen($urls[$j])*3/4);
						$url_repls[] = '<a href="'.$urls[$j].'">'.substr( $urls[$j],0,$sublen)."...</a>";
					}
					$this->comments[$i]["text"] = str_replace($urls,$url_repls,$this->comments[$i]["text"]);
				}


				if (!$this->anonymous)
				{
					$this->comments[$i]["is_owner"] = $this->comments[$i]["user_id"]==$user->id&&$this->comments[$i]["type"]==$user->type;

					if ($this->comments[$i]["comments_count"]>=500)
					{
						$this->comments[$i]["r500"] = (int)($this->comments[$i]["comments_count"]/500);
					}

					if ($this->comments[$i]["comments_count"]%500>=100)
					{
						$this->comments[$i]["r100"] = (int)(($this->comments[$i]["comments_count"]%500)/100);
					}

				}

				$ts = strtotime($this->comments[$i]["time"]);

				if (date("Y-m-d",$ts)==$today)
				{
					$min = date("i",$ts);
					$hour = date("H",$ts);
					$Hmin = $hour*60+$min;
					if ($tHmin-$Hmin<2)
					{
						$this->comments[$i]["time_mess"] = "только что";
					}
					elseif ($thour - $hour<12)
					{
						$dHmin = $tHmin - $Hmin;
						$dh = ($dHmin-($dHmin%60))/60;
						$dm = $dHmin%60;
						$mess="";
						if ($dh>0)
						if ($dh==1)
						{
							$mess.="1 час";
						}
						elseif ($dh<5)
						{
							$mess.="$dh часа";
						}
						else
						{
							$mess.="$dh часов";
						}

						if ($dm%10>4||$dm%10==0||($dm>10&&$dm<20))
						{
							$mess.=" $dm минут";
						}
						elseif ($dm%10==1)
						{
							$mess.=" $dm минута";
						}
						else
						{
							$mess.=" $dm минуты";
						}
						$this->comments[$i]["time_mess"] = $mess." назад";
					}
					else
					{
						$this->comments[$i]["time_mess"] = date("сегодня H:i",$ts);
					}
				}
				elseif (date("Y-m-d",$ts)==$yesterday)
				{
					$this->comments[$i]["time_mess"] = date("вчера H:i",$ts);
				}
				else
				{
					$this->comments[$i]["time_mess"] = date("d F Y H:i",$ts);
				}

			}
		}
	}
}
?>
