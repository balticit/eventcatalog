<?php
	class CApplicationContext
	{
		private static $instance;

		public $configPath;
		
		public $appEncoding;
		
		public $currentNode;

		public $currentPath;
		
		public $page;
		
		public $IsPostBack;
		
		/**
		 * Enter description here...
		 *
		 * @return CApplicationContext
		 */
		public static function GetInstance()
		{
			if (is_null(CApplicationContext::$instance))
			{
				CApplicationContext::$instance = new CApplicationContext();
			}
			return CApplicationContext::$instance;
		}
		
		public function IsInCMSMode()
		{
			return !is_null($this->currentNode->cmsfile);
		}
		
		
		public function Start($configPath)
		{
			$this->IsPostBack = CURLHandler::IsPost();
			$this->configPath = $configPath;
			CSiteMapHandler::$configPath = $configPath;
			CURLHandler::Prepare();
			$this->appEncoding = CSiteMapHandler::$appEncoding;
			$this->currentPath = CURLHandler::$currentPath;
			$this->currentNode = CURLHandler::$currentNode;
			if ($this->IsInCMSMode())
			{
				include_once(RealFile($this->currentNode->cmsfile));
			}
			$this->page = CPageBuilder::GetPage($this->currentNode);
		}
		
		public function End()
		{
			$this->page->Render();
		}
	}
?>
