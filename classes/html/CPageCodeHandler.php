<?php
class CPageCodeHandler extends CObject
{
	public $pageId;

	public $template;

	public $encoding;

	public $controls = array();

	public $rewriteParams = array();

	public $IsPostBack;

	private $messageMapArray = array();

	public function CPageCodeHandler()
	{
		$this->CObject();
	}

	public function PreRender()
	{

	}


	public function Render()
	{
		$this->rewriteParams = CURLHandler::$rewriteParams;
		$this->PreRender();
		include($this->template);
	}
	/**
	 * Get control
	 *
	 * @param string $relativeId
	 * @return CPageObject
	 */
	public function GetControl($relativeId)
	{
		if (isset($this->controls[$this->pageId.CONTROL_SEMISECTOR.$relativeId]))
		{
			return $this->controls[$this->pageId.CONTROL_SEMISECTOR.$relativeId];
		}
		return null;
	}

	private function SetMessageMap($mapKey)
	{
		if (!isset($this->messageMapArray[$mapKey]))
		{
			$ckeys = array_keys($this->controls);
			foreach ($ckeys as $ckey)
			{
				if (is_a($this->controls[$ckey],"CPageMessageMap"))
				{
					$this->messageMapArray[$this->controls[$ckey]->mapKey]=&$this->controls[$ckey];
					if ($this->controls[$ckey]->mapKey==$mapKey)
					{
						return ;
					}
				}
			}
			$this->messageMapArray[$mapKey] = null;
		}
	}

	public function GetMessage($key,$mapKey = "default")
	{

		$this->SetMessageMap($mapKey);
		return is_a($this->messageMapArray[$mapKey],"CPageMessageMap")?$this->messageMapArray[$mapKey]->GetMessage($key):null;
	}

	public function GetFormattedMessage($key,$args = array(),$mapKey = "default")
	{
		$this->SetMessageMap($mapKey);
		return is_a($this->messageMapArray[$mapKey],"CPageMessageMap")?$this->messageMapArray[$mapKey]->GetFormattedMessage($key,$args):null;
	}
}
?>