<?php
class magazine_details_php extends CPageCodeHandler
{

    public function magazine_details_php(){
        $this->CPageCodeHandler();
    }

    public function PreRender() {
        
        $app = CApplicationContext::GetInstance();
        $av_rwParams = array("id");
		    CURLHandler::CheckRewriteParams($av_rwParams);  
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
        
        // ���������, �������� ���� ����� � �.�.
        $innerHtml = '';
        if ($mag['is_sales_active'] == 1) {
                $innerHtml .= '<div style="margin: 8px 0"><a href="/magazines/delivery/id/'.$mag['tbl_obj_id'].'" class="dost">�������� ��������</a></div>';
        }
        $innerHtml .= '<a class="podr" href="/magazines/details/id/'.$mag['tbl_obj_id'].'">��������� �� �������</a><br/>';
        if ($mag['is_price_active'] == 1) {
            $innerHtml .= '<a class="price" href="/magazines/pricelist/id/'.$mag['tbl_obj_id'].'">���� �������� � �������</a><br/>';
        }
        
        $mag['links'] = $innerHtml;
        
        $magazineCtrl = $this->GetControl("details");
        $magazineCtrl->dataSource = $mag; 
    }
}
?>
