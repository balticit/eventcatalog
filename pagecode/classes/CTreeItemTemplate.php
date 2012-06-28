<?php
	class CTreeItemTemplate extends CObject 
	{
		public $level;
		public $source;
		public $sourceEncoding;
		public $targetEncoding;
		public $filename;
		public $template;
		
		public  function CTreeItemTemplate()
		{
			$this->CObject();
		}
		
		public function GetTemplate()
		{
			switch ($this->source) {
				case "file":
				{
					$file = RealFile($this->filename);
					if (!is_null($file))
					{
						$this->template = ($this->sourceEncoding!=$this->targetEncoding)?
							iconv($this->sourceEncoding,$this->targetEncoding,file_get_contents($file)):file_get_contents($file);
					}
				}
				break;
			}
			return $this->template;
		}
	}
?>