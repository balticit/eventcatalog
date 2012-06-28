<?php         
    class cms_weeksubscribe_php extends cms_subscribe_php 
    {
        public function cms_weeksubscribe_php()
        {
            $this->CCMSPageCodeHandler();
        }
        
        public function PreRender() 
        {
            if (GP('date_from') && GP('date_to')) {
               $this->filter = " where active = 1 and CAST(registration_date as DATE) BETWEEN CAST('".GP('date_from')."' as DATE) AND CAST('".GP('date_to')."' as DATE)";
               
               $this->period = date('j.m',strtotime(GP('date_from'))).' по '.date('j.m',strtotime(GP('date_to')));
               
               parent::PreRender();                    
            }
        }
        
    }
?>
