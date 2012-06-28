<?php
class CDateTimePicker extends CHTMLObject  
{
	public $value;

	public $scriptPath = DATE_TIME_PICKER_SCRIPT_PATH;
	
	public $imagePath = DATE_TIME_PICKER_IMAGE_PATH;
	
	public $inputClass;
	
	public $linkClass;
	
	public $name;
	
	public $id;
	
	public $pickHint;
	
	public $dateFormat = "DDMMyyyy";
	
	public $showTime = false;
	
	public $timeMode = 24;
	
	public function CDateTimePicker()
	{
		$this->CHTMLObject();
	}
	
	public function RenderHTML()
	{
		$img = new CImage();
		$img->title = $this->pickHint;
		$img->alt = $this->pickHint;
		$img->src = $this->imagePath;
		$link = new CLinkLabel();
		$link->class = $this->linkClass;
		$link->title = $this->pickHint;
		$link->href = CStringFormatter::Format("javascript:NewCal('{id}','{format}',{showtime},{timeformat});",
		array("id"=>$this->id,"format"=>$this->dateFormat,"showtime"=>($this->showTime?"true":"false"),"timeformat"=>$this->timeMode));
		$link->innerHTML = $img->RenderHTML();
		$scipt = new CScriptSrc();
		$scipt->src = $this->scriptPath;
		$input = new CTextBox();
		$input->class = $this->inputClass;
		$input->id = $this->id;
		$input->name = $this->name;
		$input->value = $this->value;
		return $scipt->RenderHTML().$input->RenderHTML().$link->RenderHTML();
	}
}
?>