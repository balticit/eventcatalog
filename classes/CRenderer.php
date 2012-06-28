<?php
class CRenderer
{
	public static function RenderControl($id,$relative = true)
	{
		$app = CApplicationContext::GetInstance();
		$page = $app->page;
		if ($relative)
		{
			$id = $page->pageId.CONTROL_SEMISECTOR.$id;
		}
		if (isset($page->controls[$id]))
		{
			echo $page->controls[$id]->Render();
		}
	}
}
?>