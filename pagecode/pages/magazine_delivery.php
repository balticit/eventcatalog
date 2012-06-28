<?php
class magazine_delivery_php extends CPageCodeHandler
{

    public function magazine_delivery_php(){
        $this->CPageCodeHandler();
    }

    public function PreRender() 
    {
      $av_rwParams = array("id");
		  CURLHandler::CheckRewriteParams($av_rwParams);  
        $app = CApplicationContext::GetInstance();
        $rewriteParams = array();
        $id = GP("id");
        if (!is_numeric($id))
        {
            $id =0;
        }
        $magazine = SQLProvider::ExecuteQuery("select * from tbl__magazines where tbl_obj_id = $id");
        
        if (sizeof($magazine)==0) {
            CURLHandler::ErrorPage();
        }
        
        $mag = $magazine[0];
        
        
        // Подробнее, Оставить свой отзыв и т.д.
        $innerHtml = '';
        if ($mag['is_sales_active'] == 1) {
                $innerHtml .= '<div style="margin: 8px 0"><a href="/magazines/delivery'.$mag['tbl_obj_id'].'" class="dost">Заказать доставку</a></div>';
        }
        $innerHtml .= '<a class="podr" href="/magazines/details'.$mag['tbl_obj_id'].'">Подробнее об издании</a><br/>';
        if ($mag['is_price_active'] == 1) {
            $innerHtml .= '<a class="price" href="/magazines/pricelist'.$mag['tbl_obj_id'].'">Ваша компания в издании</a><br/>';
        }
        if ($mag['is_comment_active'] == 1) {
            $count = SQLProvider::ExecuteQuery("select count(tbl_obj_id) as kolvo from tbl__magazines_comments where magazine_id = ".$mag['tbl_obj_id']);
            $count = $count[0]['kolvo'];
            $innerHtml .= '<a class="otz" href="/magazines/comment'.$mag['tbl_obj_id'].'">Оставить свой отзыв</a><br/>';
            $innerHtml .= '<a class="otz" href="/magazines/viewcomment'.$mag['tbl_obj_id'].'">Почитать отзывы';
            if ($count > 0) {
                $innerHtml .= " ($count)";
            }
            $innerHtml .= '</a>';
        }
        
        $mag['links'] = $innerHtml;
        
        
		$news_links = "";
		$rate = "";
		$display = "none;";
		$active = "0";
			
		/*-----*/
		if ($_POST["sended"]=="sended") {
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['captcha'] && $_POST['name']!="" && $_POST['email']!=""){
			
				$mag['comm_begin_if_ok'] = '<!--';
				$mag['comm_end_if_ok'] = '-->';
				$mag['comm_begin_if_notok'] = '';
				$mag['comm_end_if_notok'] = '';
				
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
				$mag['name'] = $_POST['name'];
				$mag['position'] = $_POST['position'];
				$mag['company'] = $_POST['company'];
				$mag['site'] = $_POST['site'];
				$mag['address'] = $_POST['address'];
				$mag['email'] = $_POST['email'];
				$mag['phone'] = $_POST['phone'];
				$mag['time'] = $_POST['time'];
				$mag['count'] = $_POST['count'];
				$mag['money'] = $_POST['money'];
				$mag['comm_begin_if_ok'] = '';
				$mag['comm_end_if_ok'] = '';
				$mag['comm_begin_if_notok'] = '<!--';
				$mag['comm_end_if_notok'] = '-->';
			}
		}
		else {
			$mag['name'] = "";
			$mag['position'] = "";
			$mag['site'] = "";
			$mag['company'] = "";
			$mag['address'] = "";
			$mag['email'] = "";
			$mag['phone'] = "";
			$mag['time'] = "";
			$mag['count'] = "1";
			

			$mag['comm_begin_if_ok'] = '';
			$mag['comm_end_if_ok'] = '';
			$mag['comm_begin_if_notok'] = '<!--';
			$mag['comm_end_if_notok'] = '-->';
		}

        $magazineCtrl = $this->GetControl("details");
        $magazineCtrl->dataSource = $mag; 
		
    }
}
?>
