<?php
class magazine_comment_php extends CPageCodeHandler
{

    public function magazine_comment_php(){
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
        if (sizeof($magazine)==0)
        {
            CURLHandler::ErrorPage();
        }
        $mag = $magazine[0];
        
        // Подробнее, Оставить свой отзыв и т.д.
        $innerHtml = '';
        if ($mag['is_sales_active'] == 1) {
                $innerHtml .= '<div style="margin: 8px 0"><a href="/magazines/delivery/id/'.$mag['tbl_obj_id'].'" class="dost">Заказать доставку</a></div>';
        }
        $innerHtml .= '<a class="podr" href="/magazines/details/id/'.$mag['tbl_obj_id'].'">Подробнее об издании</a><br/>';
        if ($mag['is_price_active'] == 1) {
            $innerHtml .= '<a class="price" href="/magazines/pricelist/id/'.$mag['tbl_obj_id'].'">Ваша компания в издании</a><br/>';
        }
        if ($mag['is_comment_active'] == 1) {
            $count = SQLProvider::ExecuteQuery("select count(tbl_obj_id) as kolvo from tbl__magazines_comments where magazine_id = ".$mag['tbl_obj_id']);
            $count = $count[0]['kolvo'];
            $innerHtml .= '<a class="otz" href="/magazines/comment/id/'.$mag['tbl_obj_id'].'">Оставить свой отзыв</a><br/>';
            $innerHtml .= '<a class="otz" href="/magazines/viewcomment/id/'.$mag['tbl_obj_id'].'">Почитать отзывы';
            if ($count > 0) {
                $innerHtml .= " ($count)";
            }
            $innerHtml .= '</a>';
        }
        
        $mag['links'] = $innerHtml;
        
        
        $user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        
        if (($user->authorized)) {
            $mag['comm_begin_if_not_authorized'] = "<!--";
            $mag['comm_end_if_not_authorized'] = "-->";
            $mag['comm_begin_if_authorized'] = "";
            $mag['comm_end_if_authorized'] = "";
            
            if ($_POST["sended"]=="sended") {
                if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['captcha'] && $_POST['comment']!="" && $_POST['name']!=""){
                    $mag['comm_begin_if_ok'] = '<!--';
                    $mag['comm_end_if_ok'] = '-->';
                    $mag['comm_begin_if_send'] = '';
                    $mag['comm_end_if_send'] = '';                
                    
                    $text = "<table border=1>";
                    
                    $login = SQLProvider::ExecuteQuery("select login from vw__all_users where tbl_obj_id = ".$user->id." and login_type= '".$user->type."';");
                    $login = $login[0];
                
                    $text .= "<tr><td>Название издания</td><td>".$mag['publication']."</td></tr>";
                    $text .= "<tr><td>Выпуск издания</td><td>".$mag['season']."</td></tr>";
                    $text .= "<tr><td>ID пользователя</td><td>".$user->id."</td></tr>";
                    $text .= "<tr><td>Тип пользователя</td><td>".$user->type."</td></tr>";
                    $text .= "<tr><td>Логин пользователя</td><td>".$login['login']."</td></tr>";
                    $text .= "<tr><td>Имя</td><td>".$_POST['name']."</td></tr>";
                    $text .= "<tr><td>Должность</td><td>".$_POST['position']."</td></tr>";
                    $text .= "<tr><td>Название компании</td><td>".$_POST['company']."</td></tr>";
                    $text .= "<tr><td>Отзыв</td><td>".$_POST['comment']."</td></tr>";
                    $text .= "<tr><td>Дата отзыва</td><td>".date("Y-m-d H:i:s")."</td></tr>";
                    
                    $text .= "</table>";
                    
                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";
                    
                    // Additional headers
                    $headers .= 'development@eventcatalog.ru' . "\r\n";
                    $headers .= 'From: development@eventcatalog.ru' . "\r\n";
                    
                    mail('catalog@eventcatalog.ru','Отзыв об издании',$text,$headers);
                    
                }
                else {
                    $mag['comment'] = $_POST['comment'];
                    $mag['name'] = $_POST['name'];
                    $mag['position'] = $_POST['position'];
                    $mag['company'] = $_POST['company'];
                    
                    $mag['comm_begin_if_ok'] = '';
                    $mag['comm_end_if_ok'] = '';
                    $mag['comm_begin_if_send'] = '<!--';
                    $mag['comm_end_if_send'] = '-->';                
                }
            }
            else {
                $mag['comment'] = '';
                $mag['name'] = '';
                $mag['position'] = '';
                $mag['company'] = '';
                
                $mag['comm_begin_if_ok'] = '';
                $mag['comm_end_if_ok'] = '';
                $mag['comm_begin_if_send'] = '<!--';
                $mag['comm_end_if_send'] = '-->';
            }
            
        }
        else {
            $mag['comm_begin_if_not_authorized'] = "";
            $mag['comm_end_if_not_authorized'] = "";
            $mag['comm_begin_if_authorized'] = "<!--";
            $mag['comm_end_if_authorized'] = "-->";
        }
        
        
        $magazineCtrl = $this->GetControl("details");
        $magazineCtrl->dataSource = $mag; 
    }
}