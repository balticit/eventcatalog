<?php
class cms_star_shedule_php extends CCMSPageCodeHandler
{
	public function cms_star_shedule_php()
	{
		$this->CCMSPageCodeHandler();
	}

	public function PreRender()
	{
		$filepath = RealFile("pagecode/settings/star_calendar_files/star_shedule.htm");
		if ($this->IsPostBack)
		{
			$cn =GP("content","");
			if (is_file($filepath))
			{
				file_put_contents($filepath,$cn);
			}
		}
		$content = $this->GetControl("content");
		if (is_file($filepath))
		{
			$content->innerHTML = file_get_contents($filepath);
		}
	}
}
?>
