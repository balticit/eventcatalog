<?php
	class CMetadataObject extends CHTMLObject 
	{
		public $template;

		public $keywords;

		public $description;

		public function CMetadataObject($template = "",$data = array())
		{
		  //Default values
			$this->keywords = "event, �����, event-�����, event � ������, event-���������, event-�������, event � ��������, ��� �������������, ����������� �����������, ����������� �������, ����������� ���������, ����������� �������, �������, ������������� ��������, ������������� ���������, ������������� �����������, ���� ��������, team-buildeng, ���-�������, �����������, ������������� ��������, �������, ��������, event-���������, ����������, ������� �����������, ����������� ����������� �����������, ����� ��� ���������� �����������,  �������� ��� ����������  �����������, ���� ��������, ���� ��������, ������� ��������, ������� ��� �����������, ����������� ���� ������, ���, event � ���������, event-���������, event � ��������,  ��������� �� ����������� �����������, �������� ��� �����������, ���� �����������, �������, ������� event � ���������, ��������� �������, �������� event, ������ ���������� ��������, ������� ������������, event ���������, ����� event";
			$this->description = "EVENT �������. ������ ��� ������������� �����������.";
		}
		
		public function RenderHTML()
		{
			$this->dataSource = array("keywords" => $this->keywords,
			                          "description" => $this->description);
			return CStringFormatter::Format($this->template,$this->GetDataSourceData());
		}
	}
?>
