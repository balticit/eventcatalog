<?php
class CSelect extends CNativeHTMLObject
{

		public $size;

		public $items = array();

		public $disabled = false;
		public $titleName;
		public $valueName;
		public $selectedValue;
		public $style = array();
		public $multiple = false;
		public  static $headerTemplate = "<select class=\"{class}\" size=\"{size}\" id=\"{id}\" name=\"{name}\" {multiple} {htmlEvents} {style} {disabled}>";
		public  static $footerTemplate = "</select>";

		public function CSelect()
		{
			$this->CNativeHTMLObject();
      $this->size = 1;
		}

		public function ClearItems()
		{
			$this->items = array();
		}

		public function AddItem($title,$value,$selected = false)
		{
			array_push($this->items,new COption($title,$value,$selected));
		}

		public function DataBind()
		{
			foreach ($this->dataSource as $dataItem)
			{
				$dataItem = CHTMLObject::GetBindableData($dataItem);
				$this->AddItem($dataItem[$this->titleName],$dataItem[$this->valueName],is_array($this->selectedValue)?!(array_search($dataItem[$this->valueName],$this->selectedValue)===false) :$dataItem[$this->valueName]==$this->selectedValue);
			}
		}

		public function RenderHTML()
		{
			$this->DataBind();
			$hmap = $this->ToHashMap();
			$hmap['multiple'] = $this->multiple?'multiple="multiple"':"";
			$hmap['htmlEvents']= $this->BuildHTMLEvents();
			$hmap['style']= $this->BuildHTMLStyle();
			$hmap['disabled'] = $this->disabled?'disabled="disabled"':"";
			$html = CStringFormatter::Format(CSelect::$headerTemplate,$hmap);
			foreach ($this->items as $item)
			{
				$html.=$item->Render();
			}
			$html.=CSelect::$footerTemplate;
			return $html;
		}
	}
?>