<?php
	class CTreeRepeater extends CHTMLObject 
	{
		public $itemHeaders;
		public $itemTemplates;
		public $itemFooters;
		
		public $itemHeadersArray = array();
		public $itemTemplatesArray = array();
		public $itemFootersArray = array();
		
		public $levels =0;
		
		public $childField;
		public $parentField;
		public $rootValue = 0;
		
		private $tree = array();
		public function CTreeRepeater()
		{
			$this->CHTMLObject();
		}
		
		public function RenderHTML()
		{
			$this->CreateTemplateArrays();
			$this->tree = $this->GetTreeItems($this->rootValue);
			$html = "";
			foreach ($this->tree  as $treeItem)
			{
				$html.=$treeItem->RenderHTML();
			}
			return $html;
		}
		
		private function GetTreeItems($pid,$level = 0)
		{
			$items = array();
			if ($level==$this->levels)
			{
				return $items;
			}
			$keys = $this->GetIndexesByParent($pid);
			foreach ($keys as $key)
			{
				$treeItem = new CTreeItem();
				$treeItem->level = $level;
				$treeItem->dataSource = $this->dataSource[$key];
				$treeItem->headerTemplate = (isset($this->itemHeadersArray[$level]))?$this->itemHeadersArray[$level]:"";
				$treeItem->footerTemplate = (isset($this->itemFootersArray[$level]))?$this->itemFootersArray[$level]:"";
				$treeItem->itemTemplate = (isset($this->itemTemplatesArray[$level]))?$this->itemTemplatesArray[$level]:"";
				$treeItem->controls = $this->GetTreeItems($treeItem->dataSource[$this->childField],$level+1);
				array_push($items,$treeItem);
			}
			return $items;
		}
		
		private function CreateTemplateArrays()
		{
			if (is_array($this->itemFooters))
			foreach ($this->itemFooters as $item)
			{
				$this->itemFootersArray[$item->level]=$item->GetTemplate();
			}
			if (is_array($this->itemHeaders))
			foreach ($this->itemHeaders as $item)
			{
				$this->itemHeadersArray[$item->level]=$item->GetTemplate();
			}
			if (is_array($this->itemTemplates))
			foreach ($this->itemTemplates as $item)
			{
				$this->itemTemplatesArray[$item->level]=$item->GetTemplate();
			}
		}
		
		private function GetIndexesByParent($pid)
		{
			$res = array();
			foreach ($this->dataSource as $key=>$value) {
				if (@$value[$this->parentField]==$pid)
				{
					array_push($res,$key);
				}
			}
			return $res;
		}
	}
?>
