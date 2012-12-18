<?php
class add_resident_news_php extends CPageCodeHandler
{

	public function add_resident_news_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$user = new CSessionUser("user");
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);
		$addform = array();
		$addform['begin_comm_if_not_posted'] = "";
		$addform['end_comm_if_not_posted'] = "";
		$addform['begin_comm_if_posted'] = "<!--";
		$addform['end_comm_if_posted'] = "-->";
		$addform['comm_begin_if_authorized'] = "<!--";
		$addform['comm_end_if_authorized'] = "-->";
		
		if (($user->authorized)and($user->usertype!="user")) {
			$addform['usertype'] = $user->type;
			$addform['comm_begin_if_not_authorized'] = "";
			$addform['comm_end_if_not_authorized'] = "";
			$addform['cont_underline'] = "";
			$addform['area_underline'] = "";
			$addform['arti_underline'] = "";
			$addform['agen_underline'] = "";
			switch ($user->type) {
				case "contractor" :
					$addform['cont_underline'] = "text-decoration: underline;";
					$addform['company_news'] = "<span style=\"color:#f05620;\">Новость подрядчика</span>";
				break;
				case "area" :
					$addform['area_underline'] = "text-decoration: underline;";
					$addform['company_news'] = "<span style=\"color:#3399ff;\">Новость площадки</span>";
				break;
				case "artist" :
					$addform['arti_underline'] = "text-decoration: underline;";
					$addform['company_news'] = "<span style=\"color:#ff0066;\">Новость артиста</span>";
				break;
				case "agency" :
					$addform['agen_underline'] = "text-decoration: underline;";
					$addform['company_news'] = "<span style=\"color:#99cc00;\">Новость агентства</span>";
				break;
			}
			
			$company = SQLProvider::ExecuteQuery("select * from vw__all_users where tbl_obj_id=".$user->id." and login_type='".$user->type."'");
			$addform['company_name'] = $company[0]['title'];
			$addform['comm_begin_if_not_authorized'] = "";
			$addform['comm_end_if_not_authorized'] = "";
			if ($_POST["sended"]=="sended") {
				if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['captcha']){
					$addform['begin_comm_if_not_posted'] = "<!--";
					$addform['end_comm_if_not_posted'] = "-->";
					$addform['begin_comm_if_posted'] = "";
					$addform['end_comm_if_posted'] = "";
					
					//Тут должно быть добавление в базу данных
					mysql_query("insert into tbl__resident_news (date,resident_type,resident_id,title,title_url,text,name,email,active)
								values ('".date("Y-m-d")."','".$user->type."',".$user->id.",'".addslashes($_POST["title"]).
								"', '".translitURL($_POST["title"])."' ,  '".addslashes($_POST["text"])."','".addslashes($_POST["name"]).
								"','".addslashes($_POST["email"])."',0)");
					$news_id = mysql_insert_id();
					foreach ($_FILES as $image) {
						if ($image["error"]==0) {
						
							$r = mysql_query("show table status from eventcatalog_ru like 'tbl__photo'");
							$f = mysql_fetch_assoc($r);
							$photo_id = $f["Auto_increment"];
							
							switch ($image["type"]) {
								case "image/pjpeg";
								case "image/jpeg" :
									$im = imagecreatefromjpeg($image["tmp_name"]);
									$im_middle = imagecreatetruecolor(360,264);
									imagecopyresampled($im_middle,$im,0,0,0,0,360,264,imagesx($im),imagesy($im));
									$big_title = $photo_id."-residentnews-big.jpg";
									$middle_title = $photo_id."-residentnews-middle.jpg";
									$little_title = $photo_id."-residentnews-little.jpg";
									imagejpeg($im_middle,$_SERVER['DOCUMENT_ROOT']."/application/public/upload/".$middle_title);
									$im_little = imagecreatetruecolor(85,50);
									imagecopyresampled($im_little,$im,0,0,0,0,85,50,imagesx($im),imagesy($im));
									imagejpeg($im_little,$_SERVER['DOCUMENT_ROOT']."/application/public/upload/".$little_title);
									imagejpeg($im,$_SERVER['DOCUMENT_ROOT']."/application/public/upload/".$big_title);
									imagedestroy($im);
									imagedestroy($im_middle);
									imagedestroy($im_little);
									mysql_query("insert into tbl__photo (s_image,m_image,l_image) values ('$little_title','$middle_title','$big_title')");
									mysql_query("insert into tbl__resident_news2photo (news_id,photo_id) values ($news_id,$photo_id)");
								break;
								
								case "image/gif" :
									$im = imagecreatefromgif($image["tmp_name"]);
									$im_middle = imagecreatetruecolor(360,264);
									imagecopyresampled($im_middle,$im,0,0,0,0,360,264,imagesx($im),imagesy($im));
									$big_title = $photo_id."-residentnews-big.gif";
									$middle_title = $photo_id."-residentnews-middle.gif";
									$little_title = $photo_id."-residentnews-little.gif";
									imagegif($im_middle,$_SERVER['DOCUMENT_ROOT']."/application/public/upload/".$middle_title);
									$im_little = imagecreatetruecolor(85,50);
									imagecopyresampled($im_little,$im,0,0,0,0,85,50,imagesx($im),imagesy($im));
									imagegif($im_little,$_SERVER['DOCUMENT_ROOT']."/application/public/upload/".$little_title);
									imagegif($im,$_SERVER['DOCUMENT_ROOT']."/application/public/upload/".$big_title);
									imagedestroy($im);
									imagedestroy($im_middle);
									imagedestroy($im_little);
									mysql_query("insert into tbl__photo (s_image,m_image,l_image) values ('$little_title','$middle_title','$big_title')");
									mysql_query("insert into tbl__resident_news2photo (news_id,photo_id) values ($news_id,$photo_id)");
								break;								
							}
						}
					}
										
				}else{
					$addform['title'] = $_POST["title"];
					$addform['title_url'] = translitURL($_POST["title"]);
					$addform['text'] = $_POST["text"];
					$addform['name'] = $_POST["name"];
					$addform['email'] = $_POST["email"];
				}
			}
			else {
				$addform['title'] = "";
				$addform['title_url'] = translitURL($_POST["title"]);
				$addform['text'] = "";
				$addform['name'] = "";
				$addform['email'] = "";
			}
		}
		else {
			$addform['usertype'] = "";
			$addform['comm_begin_if_not_authorized'] = "<!--";
			$addform['comm_end_if_not_authorized'] = "-->";
			$addform['begin_comm_if_posted'] = "";
			$addform['end_comm_if_posted'] = "";
			$addform['comm_begin_if_authorized'] = "";
			$addform['comm_end_if_authorized'] = "";
		}		
		$groupList= $this->GetControl("addform");
		$groupList->dataSource = $addform;
	}
}
?>
