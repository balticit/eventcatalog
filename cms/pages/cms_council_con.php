<?php
class cms_council_con_php extends CCMSPageCodeHandler
{
	public function cms_council_con_php()
	{
		$this->CCMSPageCodeHandler();
	}

	public function PreRender()
	{
		$filepath = RealFile("pagecode/settings/council_files/council_con.htm");
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
