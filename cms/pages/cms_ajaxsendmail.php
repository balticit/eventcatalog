<?php
  class cms_ajaxsendmail extends CCMSPageCodeHandler 
  {
        public function cms_ajaxsendmail()
        {
            $this->CCMSPageCodeHandler();
        }
        
        public function PreRender() {
        }
        
        public function Render() 
        {
           $filter = GP("filter");

           $index  = GP("index");
           $user_subscribed = GP("user_subscribed");

           $add_header = str_replace("\\n","\r\n",GP("mailheader"))."\r\n";

           $errors = array();
           
           $limit = rand(1,6);
           
           try {
           
               $sql = "select tbl_obj_id,email,login_type from vw__all_users where subscribe=1 and (0=1 $filter)
                       union all
                       select null,email,null from tbl__subscribed where 1 = $user_subscribed
                       LIMIT $index,$limit"; 
               
               $body = file_get_contents(TMP_DIR.'sendmail_new.txt');
               
               $emails =  SQLProvider::ExecuteQuery($sql);
               
               foreach($emails as $f) {
                   if ($f["email"] != '') {
                       $body1 = str_replace("stopsubscribe","stopsubscribe?data=".base64_encode($f["login_type"]."|".$f["tbl_obj_id"]),$body);
                       try {
                           mail($f["email"],GP("subject"),$body1,$add_header);
                       }
                       catch(Exception $ex) {
                           error_log($ex->getMessage(), 0);
                           $errors[] = 'Error on sending mail. Email: '.$f["email"].', login_type: '.$f["login_type"].', id: '.$f["tbl_obj_id"];
                           $errors[] = $ex->getMessage();
                           $errors[] = '----';
                       }
                   }
                   
                   $index++;
               }           
               
           }
           catch(Exception $ex) {
               error_log($ex->getMessage(), 0);
               $errors[] = 'Error on sending mail. SQL: '.$sql;
               $errors[] = $ex->getMessage();
               $errors[] = '----';
           }
           
           
           header('Content-type: application/javascript');
           if (count($errors) == 0) {
               echo json_encode(array('index'=>$index));
           }
           else {
               $json = array(
                 'index' => $index,
                 'errors' => $errors
               );
               echo json_encode($json);
           }
           
        }
  }