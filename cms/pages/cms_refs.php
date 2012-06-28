<?php
	class cms_refs_php extends CCMSPageCodeHandler 
	{
		public function cms_refs_php()
		{
			$this->CCMSPageCodeHandler();
			
		}
		
		/**
		 * Enter description here...
		 *
		 * @param CDBLookupGridDataField $sender
		 * @param unknown_type $args
		 */
		public function SetOther($sender,$args)
		{
			$post = GP($sender->parentId);
			if (!IsNullOrEmpty($post["other_title"]))
			{
				$_POST[$sender->parentId]["title"] = $post["other_title"];
			}
		}
		
		
		public function PreRender()
		{
			$table = GP("table");
			
			if ($table!='data')
			{
				$exTable = $this->GetControl($table);
				if (!is_null($exTable))
				{
					$data = $this->GetControl("data");
					$data->template = $exTable->Render();
				}
			}
		}
	}
?>
