<?php         

    class cms_subscribe_php extends CCMSPageCodeHandler 
    {
        protected $filter = " where active = 1 AND DATEDIFF(registration_date,now()) BETWEEN -1 and 0";
        
        protected $period = "";
        
        public function cms_subscribe_php()
        {
            $this->CCMSPageCodeHandler();
            
            $prevDay= time() - (24 * 60 * 60);
            
            $this->period = date('j.m',$prevDay).' по '.date('j.m');
        }
        
        protected function stripDescription($description) {
            $stripped = strip_tags($description);
            if (strlen($stripped) > 150) {
                $stripped = substr($stripped,0,150).'...';
            }
            return $stripped;
        } 
        
        public function PreRender() 
        {
            $innerHTML = "C ".$this->period." в EVENT КАТАЛОГ добавились:<br/>";
            
            $innerHTML .= "<br/>НОВЫЕ РЕЗИДЕНТЫ<br/><br/>";
            
            // Новые события
            $newsfilter = str_replace('registration_date','date',$this->filter);
            $newevents = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`
from tbl__news ".$newsfilter); 

            if (count($newevents) > 0) {
              $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/news/' style='color:#000'><font color='#000'>НОВЫЕ СОБЫТИЯ</font></a>:<br/><br/>";
              foreach ($newevents as $event) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/news/details/id/".$event['tbl_obj_id']."/' style='color:#000'><font color='#000'>".$event['title']."</font></a><br/>";
              }
              $innerHTML .= "<br/>";
            }
            
            // Новости резидентов
            $residentNews = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`
from tbl__resident_news ".$newsfilter." "); 

            if (count($residentNews) > 0) {
              $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/resident_news/' style='color:#000'><font color='#000'>НОВОСТИ РЕЗИДЕНТОВ</font></a>:<br/><br/>";
              foreach ($residentNews as $event) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/resident_news/news/id/".$event['tbl_obj_id']."/' style='color:#000'><font color='#000'>".$event['title']."</font></a><br/>";
              }
              $innerHTML .= "<br/>";
            }
            
            // Учебник
            $newbooks = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`
from tbl__public_doc ".$this->filter); 

            if (count($newbooks) > 0) {
              $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/book/' style='color:#000'><font color='#000'>НОВОЕ В ЭНЦИКЛОПЕДИИ</font></a>:<br/><br/>";
              foreach ($newbooks as $book) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/book/details/id/".$book['tbl_obj_id']."/' style='color:#000'><font color='#000'>".$book['title']."</font></a><br/>";
              }
              $innerHTML .= "<br/>";
            }
            
            // Contractors
            $newcontactors = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`,
       description AS `description`
from tbl__contractor_doc ".$this->filter);
    
            if (count($newcontactors) > 0) {
              $innerHTML .= "<font color='#F05620'>Подрядчики:</font><br/><br/>";
              foreach ($newcontactors as $contactor) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/contractor/details/id/".$contactor['tbl_obj_id']."/' style='color:#F05620'><font color='#F05620'>".$contactor['title']."</font></a><br/>";
                $description = $this->stripDescription($contactor['description']);
                if ($description != '') {
                    $innerHTML .= $description.'<br/>';
                }
                $innerHTML .= '<br/>';
              }
            }              
            
            // Area
            $newareas = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`,
       description AS `description`
from tbl__area_doc ".$this->filter);
    
            if (count($newareas) > 0) {
              $innerHTML .= "<font color='#3399FF'>Площадки:</font><br/><br/>";
              foreach ($newareas as $area) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/area/details/id/".$area['tbl_obj_id']."/' style='color:#3399FF'><font color='#3399FF'>".$area['title']."</font></a><br/>";
                $description = $this->stripDescription($area['description']);
                if ($description != '') {
                    $innerHTML .= $description.'<br/>';
                }
                $innerHTML .= '<br/>';
              }
            }              
            
            // Artists
            $newartists = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`,
       description AS `description`
from tbl__artist_doc ".$this->filter);      
    
            if (count($newartists) > 0) {
              $innerHTML .= "<font color='#FF0066'>Артисты:</font><br/><br/>";
              foreach ($newartists as $artist) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/artist/details/id/".$artist['tbl_obj_id']."/' style='color:#FF0066'><font color='#FF0066'>".$artist['title']."</font></a><br/>";
                $description = $this->stripDescription($artist['description']);
                if ($description != '') {
                    $innerHTML .= $description.'<br/>';
                }
                $innerHTML .= '<br/>';
              }
            }              

            // Agency
            $newagency = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`,
       description AS `description`
from tbl__agency_doc ".$this->filter);        
    
            if (count($newagency) > 0) {
              $innerHTML .= "<font color='#99CC00'>Агентства:</font><br/><br/>";
              foreach ($newagency as $agency) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/agency/details/id/".$agency['tbl_obj_id']."/' style='color:#99CC00'><font color='#99CC00'>".$agency['title']."</font></a><br/>";
                $description = $this->stripDescription($agency['description']);
                if ($description != '') {
                    $innerHTML .= $description.'<br/>';
                }
                $innerHTML .= '<br/>';
              }
            }    
            
            // Новые площадки
            $openedAreas = SQLProvider::ExecuteQuery("select tbl_obj_id AS `tbl_obj_id`,
       title AS `title`
from vw__opened_areas ".$newsfilter); 

            if (count($openedAreas) > 0) {
              $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/opened_area/' style='color:#000'><font color='#000'>НОВЫЕ ПЛОЩАДКИ МОСКВЫ</font></a>:<br/><br/>";
              foreach ($openedAreas as $area) {
                $innerHTML .= "<a href='http://".$_SERVER['HTTP_HOST']."/opened_area/details/id/".$area['tbl_obj_id']."/' style='color:#000'><font color='#000'>".$area['title']."</font></a><br/>";
              }
              $innerHTML .= "<br/>";
            }             
            
            $innerHTML .= "<br/>Удачного Вам дня!<br/><br/>
Если Вы не желаете получать рассылку от <a href='http://www.eventcatalog.ru/'>www.eventcatalog.ru</a>, пожалуйста, перейдите по <a href='http://eventcatalog.ru/stopsubscribe'>этой ссылке</a>.";
            
            $content = $this->GetControl("content");
            $content->innerHTML = $innerHTML;            
        }
        
    }
?>
