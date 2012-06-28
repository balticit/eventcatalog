<?php
class CTemplateData extends CObject
{
	public $key;
	public $source;
	public $sourceEncoding;
	public $targetEncoding;
	public $filename;
	public $template;

	public function CTemplateData()
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
					$app = CApplicationContext::GetInstance();
					$tenc = (!is_null($this->targetEncoding))?$this->targetEncoding:$app->appEncoding;
					$senc = (!is_null($this->sourceEncoding))?$this->sourceEncoding:DEFAULT_HTML_ENCODING;
					$useConv = ($tenc!=$senc)&&(strlen($tenc)>0)&&(strlen($senc)>0);
					return ($useConv)?iconv($senc,$tenc,file_get_contents($file)):file_get_contents($file);
				}
				return "";
			}
			break;
			case "raw":
			{
				return $this->template;
			}
			break;
		}
		return "";
	}
}
?>