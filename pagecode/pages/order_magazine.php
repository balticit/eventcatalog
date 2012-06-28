<?php
class order_magazine_php extends CPageCodeHandler
{

	public function order_magazine_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$av_rwParams = array();
		CURLHandler::CheckRewriteParams($av_rwParams);  
		
		if ($_POST["sended"]=="sended") {
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['captcha'] && $_POST['name']!="" && $_POST['email']!=""){
			
				$addform['comm_begin_if_ok'] = '<!--';
				$addform['comm_end_if_ok'] = '-->';
				$addform['comm_begin_if_notok'] = '';
				$addform['comm_end_if_notok'] = '';
				
				$text = "<table border=1>";
				
				$text .= "<tr><td>ФИО</td><td>".$_POST['name']."</td></tr>";
				$text .= "<tr><td>Должность</td><td>".$_POST['position']."</td></tr>";
				$text .= "<tr><td>Название компании</td><td>".$_POST['company']."</td></tr>";
				$text .= "<tr><td>Сайт компании</td><td>".$_POST['site']."</td></tr>";
				$text .= "<tr><td>Адрес доставки</td><td>".$_POST['address']."</td></tr>";
				$text .= "<tr><td>Телефон</td><td>".$_POST['phone']."</td></tr>";
				$text .= "<tr><td>E-mail*</td><td>".$_POST['email']."</td></tr>";
				$text .= "<tr><td>Удобное время доставки</td><td>".$_POST['time']."</td></tr>";
				$text .= "<tr><td>Количество экземпляров</td><td>".$_POST['count']."</td></tr>";
				$text .= "<tr><td>Стоимость</td><td>".$_POST['money']."</td></tr>";
				
				$text .= "</table>";
				
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";
				
				// Additional headers
				$headers .= 'development@eventcatalog.ru' . "\r\n";
				$headers .= 'From: development@eventcatalog.ru' . "\r\n";
				
				mail('catalog@eventcatalog.ru','Заказ EVENT КАТАЛОГА',$text,$headers);
			
			
			}else{
				$addform['name'] = $_POST['name'];
				$addform['position'] = $_POST['position'];
				$addform['company'] = $_POST['company'];
				$addform['site'] = $_POST['site'];
				$addform['address'] = $_POST['address'];
				$addform['email'] = $_POST['email'];
				$addform['phone'] = $_POST['phone'];
				$addform['time'] = $_POST['time'];
				$addform['count'] = $_POST['count'];
				$addform['money'] = $_POST['money'];
				$addform['comm_begin_if_ok'] = '';
				$addform['comm_end_if_ok'] = '';
				$addform['comm_begin_if_notok'] = '<!--';
				$addform['comm_end_if_notok'] = '-->';
			}
		}
		else {
			$addform['name'] = "";
			$addform['position'] = "";
			$addform['site'] = "";
			$addform['company'] = "";
			$addform['address'] = "";
			$addform['email'] = "";
			$addform['phone'] = "";
			$addform['time'] = "";
			$addform['count'] = "1";
			$addform['money'] = "1500";

			$addform['comm_begin_if_ok'] = '';
			$addform['comm_end_if_ok'] = '';
			$addform['comm_begin_if_notok'] = '<!--';
			$addform['comm_end_if_notok'] = '-->';
		}

		$groupList= $this->GetControl("addform");
		$groupList->dataSource = $addform;

	}
}
?>
