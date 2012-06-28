<?php
class CCheckBoxList extends CNativeHTMLObject  
{
		public $items = array();
    public $baseName;
		public $titleName;
		public $valueName;
    public $checkedValue;
    public $col_count = 1;
		public $col_spase = 10;
		public static $headerTemplate = "<div class=\"{class}\" id=\"{id}\" {htmlEvents} {style}>";
		public static $footerTemplate = "</div>";
		
		public function CCheckBoxList()
		{
			$this->CNativeHTMLObject();

		}
		
		public function ClearItems()
		{
			$this->items = array();
		}
		
		public function AddItem($name,$title,$value,$checked = false)
		{
      array_push($this->items,new CCheckBox($name,$title,$value,$checked));
		}
		
		public function DataBind()
		{
			foreach ($this->dataSource as $dataItem) 
			{
				$dataItem = CHTMLObject::GetBindableData($dataItem);
				$this->AddItem(
                    $this->baseName."[".$dataItem[$this->valueName]."]",
                    $dataItem[$this->titleName],
                    $dataItem[$this->valueName],
                    is_array($this->checkedValue)?
                        !(array_search($dataItem[$this->valueName],$this->checkedValue)===false) :
                        $dataItem[$this->valueName]==$this->checkedValue);
			}
		}
		
		public function RenderHTML()
		{      
      $this->id = $this->baseName;
      $this->DataBind();
			$hmap = $this->ToHashMap();
			$hmap['htmlEvents']= $this->BuildHTMLEvents();
			$hmap['style']= $this->BuildHTMLStyle();
			$html = CStringFormatter::Format(CCheckBoxList::$headerTemplate,$hmap);
      if (sizeof($this->items) < $this->col_count)
        $this->col_count = $this->items;
      $per_col = round(sizeof($this->items)/$this->col_count);
      if ($this->col_count > 1) {
        $html .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr valign=\"top\"><td>";
      }
      $ccol = 1;
			foreach ($this->items as $key=>$item) 
			{
        if ($this->col_count > 1 and ($key - ($ccol-1)*$per_col) >= $per_col) {
          $ccol++;
          $html .= "</td><td><img src=\"/images/front/0.gif\" height=\"1\" width=\"$this->col_spase\"></td><td>";
        }
        if (($key - ($ccol-1)*$per_col) != 0)
          $html .= "<br>";
          $html.=$item->Render();
			}
      if ($this->col_count > 1) {
        $html .= "</td></tr></table>";
      }
			$html.=CCheckBoxList::$footerTemplate;
			return $html;
		}
	}
?>
