<?php
require_once(ROOTDIR."captcha/kcaptcha.php");
class ajax_etv_order_php extends CPageCodeHandler
{
	private $email_for_order = "catalog@eventcatalog.ru";
	private $email_subject = "Заявка EVENT TV";


	public function ajax_etv_order_php()
	{
		$this->CPageCodeHandler();
	}

	private function ValidateCaptcha()
	{
		$sess_id = GP("comment_captcha",0);
		$captcha = GP("comment_captcha_input",0);
		return  !IsNullOrEmpty($sess_id) &&
			!IsNullOrEmpty($captcha) &&
			($_SESSION[$sess_id]==$captcha);
	}

	public function PreRender()
	{
		$app = CApplicationContext::GetInstance();
		header('Content-type: text/html;charset=windows-1251');  
		$body = $this->GetControl("body");        
		if($this->IsPostBack){
		  if (!GP("video_link")){
			  $body->template = "Не указана сслыка на видео";
			}
		  else if (!GP("catalog_link")){
			  $body->template = "Не указана сслыка вашей компании на сайте evencatalog.ru";
			}
		  else if (!GP("link")){
			  $body->template = "Не указан сайт вашей компании";
			}
		  else if (!GP("contact")){
			  $body->template = "Не указано контактное лицо";
			}
		  else if (!GP("email")){
			  $body->template = "Не указан E-mail";
			}
		  else if (!GP("phone")){
			  $body->template = "Не указан телефон";
			}
			else if(!$this->ValidateCaptcha()){
				$body->template = "Неверно введены цифры";
			}
			else{
			  $body->template = "OK";
				$message = $this->GetControl("message");
				$message->dataSource = array("video_link" => iconv("utf-8",$app->appEncoding,GP("video_link")),
				                             "catalog_link" => iconv("utf-8",$app->appEncoding,GP("catalog_link")),
																		 "link" => iconv("utf-8",$app->appEncoding,GP("link")),
																		 "contact" => iconv("utf-8",$app->appEncoding,GP("contact")),
																		 "email" => iconv("utf-8",$app->appEncoding,GP("email")),
																		 "phone" => iconv("utf-8",$app->appEncoding,GP("phone")));
				SendHTMLMail($this->email_for_order,
				             iconv($app->appEncoding,"utf-8",$message->RenderHTML()),
										 iconv($app->appEncoding,"utf-8",$this->email_subject));

			}
		}
		else {
			$c_id = md5(uniqid(rand(), true));
			$captcha = array (
				"captcha_sid"=>$c_id,
				"captcha_link"=>"/captcha/sid.php?".session_name()."=".session_id()."&sid=".$c_id);
			$body->dataSource = $captcha;
		}
	}
}
?>
