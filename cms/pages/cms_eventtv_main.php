<?php         

    class cms_eventtv_main_php extends CCMSPageCodeHandler 
    {        
        
        public $video_id = "";
        public $doc_id = null;
        public $doc_list = "";
        
        public function cms_eventtv_main_php()
        {
            $this->CCMSPageCodeHandler();
            
        }
        
        public function PreRender() 
        {
            if ($this->IsPostBack) {
              
              SQLProvider::ExecuteNonReturnQuery("delete from tbl__eventtv_main");              
              $video_id = mysql_escape_string(GP('video_id'));
              $doc_id = GP('doc_id');
              if (!isset($doc_id))
                $doc_id = "null";
              
              SQLProvider::ExecuteNonReturnQuery("
                insert into tbl__eventtv_main (video_id, doc_id)
                values ('$video_id',$doc_id)");
              
            }
            
            $res = SQLProvider::ExecuteQuery("
              select video_id, doc_id from tbl__eventtv_main");
            if (sizeof($res)) {
               $this->video_id = $res[0]['video_id'];
               $this->doc_id = $res[0]['doc_id'];
            }
            
            $docs = SQLProvider::ExecuteQuery("
              select tbl_obj_id, title from tbl__eventtv_doc
              where active = 1 order by tbl_obj_id desc
            "); 
            array_unshift($docs,array('tbl_obj_id'=>'-1','title'=>' -- выберите статью --'));
            foreach ($docs as $doc) {
              $sel = "";
              if ($doc['tbl_obj_id'] == $this->doc_id)
                $sel = " selected";
              
              $this->doc_list .= '<option value="'.$doc['tbl_obj_id'].'"'.$sel.'>'.$doc['title'].'</option>';
            }
        }
        
    }
?>
