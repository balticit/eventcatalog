<?php
class magazine_viewcomment_php extends CPageCodeHandler
{

    public function magazine_viewcomment_php(){
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
        
        $comments = SQLProvider::ExecuteQuery("select * from tbl__magazines_comments where magazine_id = $id");
        
        if ($comments > 0) {
            $html = "";
            foreach ($comments as &$cmnt) {
                $html .= '<div style="background-color: #dddddd; width:600px;margin-botton:10px;padding:10px">';
                $html .= '<em>«'.$cmnt['comment'].'»</em><br/>';
                $html .= $cmnt['author'];
                $html .= '</div>';
            }
            
            $mag['comments'] = $html;
        }
        else {
            $mag['comments'] = '<p>Пока что еще ни одного отзыва.</p>';
        }
        
        $magazineCtrl = $this->GetControl("details");
        $magazineCtrl->dataSource = $mag; 
    }
}
