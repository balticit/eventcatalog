<?php
class cms_about_php extends CCMSPageCodeHandler
{
	public function cms_about_php()
	{
		$this->CCMSPageCodeHandler();
	}

	public function PreRender()
	{
		$filepath = RealFile("pagecode/settings/about_files/about.htm");
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
