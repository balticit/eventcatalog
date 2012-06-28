<?php
    class CCitySelector extends CHTMLObject 
    {
        public $current = 'Все';
        
        public $headerCss;
        
        public function CCitySelector($template = "",$data = array())
        {
            $this->CHTMLObject();
            $this->template = $template;
            $this->dataSource = $data;
        }
        
        public function RenderHTML()
        {
            $html = '';
            if ($this->headerCss != '') {
                $html .= '<style type="text/css"> .boxy-wrapper .title-bar {'.$this->headerCss.'} </style>';
            }
            
            $rewriteParams = array();
            $rewriteParams['all'] = 'cities';
            $allCitiesUrl = CApplicationContext::GetInstance()->currentPath.CURLHandler::BuildRewriteParams($rewriteParams);
            
            $html .=
            '<ul id="nav">
                <li><div class="label">'.$this->current.'</div></li>
            </ul>
            <div id="cityselectorwrapper">
                <div>
                <div id="cityselector">
                   <div class="city-selector-item" style="font-weight:bold"><a href="'.$allCitiesUrl.'">Все города</a></div>';
                
            foreach ($this->GetDataSourceData() as $key=>$city) {
                if ($city['priority'] > 0) {
                    $html .= '<div class="city-selector-item" style="font-weight:bold"><a href="'.$city['link'].'">'.$city['title'].'</a></div>';
                }
                else {
                    $html .= '<div class="city-selector-item"><a href="'.$city['link'].'">'.$city['title'].'</a></div>';
                }
            }    
                
            $html .= '</div>
               </div>
            </div>
            <script language="JavaScript" type="text/javascript">
              $(document).ready(function(){
                $("#nav").click(function(){
                    new Boxy($("#cityselector").parent(),{
                        title: "Выберите город",
                        closeText: "[X]",
                        modal: true,
                        clickToFront: true
                    }).show();
                });
              });
            </script>
            ';
            
            return $html;
        }
    }
