<?php
	class CMetadataObject extends CHTMLObject 
	{
		public $template;

		public $keywords;

		public $description;

		public function CMetadataObject($template = "",$data = array())
		{
		  //Default values
			$this->keywords = "event, умуте, event-сфера, event Ц бизнес, event-индустри€, event-каталог, event Ц менеджер, дл€ организаторов, организаци€ меропри€ти€, организаци€ банкета, организаци€ праздника, организаци€ свадьбы, свадьба, корпоративный праздник, корпоративна€ вечеринка, корпоративное меропри€тие, день рождени€, team-buildeng, тим-билдинг, презентаци€, торжественное открытие, концерт, праздник, event-маркетинг, подр€дчики, каталог подр€дчиков, техническое обеспечение меропри€тий, место дл€ проведени€ меропри€ти€,  площадки дл€ проведени€  меропри€тий, база площадок, база артистов, каталог артистов, артисты дл€ меропри€тий, официальный сайт группы, шоу, event Ц агентство, event-агентства, event Ц компании,  агентства по организации меропри€тий, персонал дл€ меропри€тий, идеи меропри€тий, событи€, новости event Ц индустрии, календарь событий, выставки event, расчЄт количества алкогол€, учебник организатора, event барахолка, форум event";
			$this->description = "EVENT  ј“јЋќ√. ѕортал дл€ ќрганизаторов ћеропри€тий.";
		}
		
		public function RenderHTML()
		{
			$this->dataSource = array("keywords" => $this->keywords,
			                          "description" => $this->description);
			return CStringFormatter::Format($this->template,$this->GetDataSourceData());
		}
	}
?>
