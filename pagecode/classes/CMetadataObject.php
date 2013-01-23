<?php
	class CMetadataObject extends CHTMLObject 
	{
		public $template;

		public $keywords;

		public $description;

		public function CMetadataObject($template = "",$data = array())
		{
		  //Default values
			$this->keywords = "организация мероприятий, event management, аренда оборудования для мероприятийи праздников, артисты, подбор места проведения мероприятия, каталоги организаторов мероприятий";
			$this->description = "Event Каталог - профессиональный сайт для организаторов мероприятий, прокатных компаний, артистов, площадок и агентств. Удобный поиск исполнителей, подбор артистов и места проведения мероприятия, а также комментарии коллег и рейтинги компаний.";
		}
		
		public function RenderHTML()
		{
			$this->dataSource = array("keywords" => $this->keywords,
			                          "description" => $this->description);
			return CStringFormatter::Format($this->template,$this->GetDataSourceData());
		}
	}
?>
