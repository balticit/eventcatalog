<?php
class pricelist_php extends CPageCodeHandler
{

	public function pricelist_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
    $av_rwParams = array();
		CURLHandler::CheckRewriteParams($av_rwParams);  
	
		if ($_POST["sended"]=="sended") {
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['captcha'] && $_POST['name']!="" && $_POST['company']!="" && $_POST['phone']!="" && $_POST['email']!=""){
			
				$addform['comm_begin_if_ok'] = '<!--';
				$addform['comm_end_if_ok'] = '-->';
				$addform['comm_begin_if_notok'] = '';
				$addform['comm_end_if_notok'] = '';
				$addform["filename"] = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename.htm");
				$addform["filename1"] = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename1.htm");
				$addform["filename2"] = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename2.htm");
				
				$text = "<table border=1>";
				
				$text .= "<tr><td>Имя</td><td>".$_POST['name']."</td></tr>";
				$text .= "<tr><td>Компания</td><td>".$_POST['company']."</td></tr>";
				$text .= "<tr><td>Телефон</td><td>".$_POST['phone']."</td></tr>";
				$text .= "<tr><td>E-mail*</td><td>".$_POST['email']."</td></tr>";
				
				$text .= "</table>";
				
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";
				
				// Additional headers
				$headers .= 'reklama@eventcatalog.ru' . "\r\n";
				$headers .= 'From: reklama@eventcatalog.ru' . "\r\n";
				
				mail('reklama@eventcatalog.ru','Запрос на скачавание прайс-листа',$text,$headers);
			
			
			}else{
				$addform['name'] = $_POST['name'];
				$addform['company'] = $_POST['company'];
				$addform['email'] = $_POST['email'];
				$addform['phone'] = $_POST['phone'];
				$addform['warning'] = "<b style=\"color: red;\">Пожалуйста, заполните все пункты анкеты.</b>";

				$addform['comm_begin_if_ok'] = '';
				$addform['comm_end_if_ok'] = '';
				$addform['comm_begin_if_notok'] = '<!--';
				$addform['comm_end_if_notok'] = '-->';
			}
		}
		else {
			$addform['name'] = "";
			$addform['company'] = "";
			$addform['email'] = "";
			$addform['phone'] = "";
			$addform['warning'] = "";

			$addform['comm_begin_if_ok'] = '';
			$addform['comm_end_if_ok'] = '';
			$addform['comm_begin_if_notok'] = '<!--';
			$addform['comm_end_if_notok'] = '-->';
		}

		$groupList= $this->GetControl("content");
		$groupList->dataSource = $addform;
	

	}
}
?>
