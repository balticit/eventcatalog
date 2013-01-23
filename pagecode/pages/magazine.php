<?php
class magazine_php extends CPageCodeHandler
{

    public function magazine_php(){
        $this->CPageCodeHandler();
    }

    public function PreRender() 
    {
        $av_rwParams = array();
        CURLHandler::CheckRewriteParams($av_rwParams);  
        
        $magazines = SQLProvider::ExecuteQuery("select m.* from `tbl__magazines` m where m.active=1 order by m.order_num,m.tbl_obj_id limit 0,4");
        
        $innerHtml = "";
        
        // Наименование
        $innerHtml .= "<tr>";
        foreach ($magazines as &$mag) {
            $innerHtml .= '<td><a href="/magazines/details'.$mag['tbl_obj_id'].'" style="color:#'.$mag['color'].'">'.$mag['publication'].'</a></td>';
            //$i++;
        }
        $innerHtml .= "</tr>";
        
        // Лого
        $innerHtml .= "<tr>";
        foreach ($magazines as &$mag) {
            $innerHtml .= '<td><a href="/magazines/details'.$mag['tbl_obj_id'].'"><img src="/upload/'.$mag['logo'].'" border="0"/></a></td>';
        }
        $innerHtml .= "</tr>";
        
        // Выпуск
        $innerHtml .= "<tr>";
        foreach ($magazines as &$mag) {
            $innerHtml .= '<td><a href="/magazines/details'.$mag['tbl_obj_id'].'" style="color:#000">Выпуск: '.$mag['season'].'</a></td>';
        }
        $innerHtml .= "</tr>";
        
        // Заказать
        $innerHtml .= "<tr>";
        foreach ($magazines as &$mag) {
            if ($mag['is_sales_active'] == 1) {
                $innerHtml .= '<td style="padding-top:5px"><a href="/magazines/delivery'.$mag['tbl_obj_id'].'"  class="dost">Заказать доставку</a></td>';
            }
            else {
                $innerHtml .= '<td style="padding-top:5px">&nbsp;</td>';
            }
        }
        $innerHtml .= "</tr>";
        
        // Подробнее, Оставить свой отзыв и т.д.
        $innerHtml .= "<tr>";
        foreach ($magazines as &$mag) {
            $innerHtml .= '<td style="padding-top:5px" valign="top"><a class="podr" href="/magazines/details'.$mag['tbl_obj_id'].'">Подробнее об издании</a><br/>';
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
            $innerHtml .= '</td>';
        }
        $innerHtml .= "</tr>";

        $magazinesCtrl = $this->GetControl("magazines");
        $magazinesCtrl->dataSource = array('magazines'=>$innerHtml);    
        
        
        
        
        
    }
}
?>
