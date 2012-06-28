<?php
class CTinyMCETextarea extends CTextarea 
{
	public $script_path = TINYMCE_SCRIPT_PATH;
	
	protected $template = "
	
	<textarea id=\"{id}\" name=\"{name}\"  style=\"display:none\" >
		{innerHTML}
	</textarea>
	
	
	<input type=\"hidden\" id=\"content___Config\" value=\"\" style=\"display:none\" />
	<iframe id=\"content___Frame\" src=\"/fckeditor/editor/fckeditor.html?InstanceName=content&amp;Toolbar=Default\" width=\"700\" height=\"500\" frameborder=\"0\" scrolling=\"no\"></iframe>
	
	";
	
	
	public function CTinyMCETextarea()
	{
		$this->CTextarea();
		
		//echo $this->innerHTML;
		
		/*$oFCKeditor = new FCKeditor("content");
		$oFCKeditor->Width = '700';
		$oFCKeditor->Height = '500';
		$oFCKeditor->BasePath = '/fckeditor/';
		$oFCKeditor->Value = "fffffffffffffffffffff";$this->innerHTML; "{innerHTML}";
		$this->template = $oFCKeditor->CreateHtml();*/
		
	}
}
?>