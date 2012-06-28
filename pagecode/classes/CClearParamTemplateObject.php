<?php
class CClearParamTemplateObject extends CHTMLObject  
{
	public $paramName;
	
	public $paramSearch = "rg";
	
	public $template;
	
	public function CClearParamTemplateObject()
	{
		$this->CHTMLObject();
	}
	
	public function RenderHTML()
	{
		$pars = CURLHandler::$rewriteParams;
		$gpars = array();
		CopyArray(&$_GET,&$gpars);
		$this->paramSearch = strtolower($this->paramSearch);
		if (!(strpos($this->paramSearch,"r")===false))
		{
			unset($pars[$this->paramName]);
		}
		if (!(strpos($this->paramSearch,"g")===false))
		{
			unset($gpars[$this->paramName]);
		}
		//$link = CURLHandler::BuildFullLink($gpars, CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($pars));
		$link = CURLHandler::$currentPath;
		return CStringFormatter::Format($this->template,array("link"=>$link));
	}
}
?>