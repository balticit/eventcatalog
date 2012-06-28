<?php
class CPager extends CHTMLObject
{
	public $itemTemplate;
	public $activeItemTemplate;
	public $nextTemplate;
	public $previousTemplate;

	public $headerTemplate;
	public $footerTemplate;

	public $currentPage;
	public $pagesInRow;
	public $totalPages;
	public $rewriteParams = array();
	public $pageParam = "page";
	public $shiftIndex = 1;
	
	
	public $useQuery = false;

	public function CPager()
	{
		$this->CHTMLObject();
	}

	public function RenderHTML()
	{
		$html = $this->headerTemplate;
		if ($this->currentPage>1)
		{
			$html.=CStringFormatter::Format($this->previousTemplate,array("previous"=>($this->currentPage-1)));
		}
		$low = 0;
		$count = $this->totalPages;
		if ($this->pagesInRow>0)
		{
			$row = floor(($this->currentPage-1)/$this->pagesInRow);
			$low = $row*$this->pagesInRow;
			$count = ($row+1)*$this->pagesInRow;
			if ($count>$this->totalPages)
			{
				$count=$this->totalPages;
			}
		}
		for ($i=$low+$this->shiftIndex;$i<$count+$this->shiftIndex;$i++)
		{
			$link = "";
			if (!$this->useQuery)
			{
				$cpars = $this->rewriteParams;
				$cpars[$this->pageParam] = $i;
				$link = CURLHandler::$currentPath.CURLHandler::BuildQueryParams($cpars);
			}
			else 
			{
				$cpars = array();
				CopyArray(&$_GET,&$cpars);
				SetInArray(&$cpars,$this->pageParam ,$i);
				$link = CURLHandler::BuildFullLink($cpars);
			}
			$html.=CStringFormatter::Format(($i==$this->currentPage)?$this->activeItemTemplate:$this->itemTemplate,array("page"=>$i,"link"=>$link));
		}
		if ($this->currentPage<$this->totalPages)
		{
			$html.=CStringFormatter::Format($this->nextTemplate,array("previous"=>($this->currentPage+1)));
		}
		$html.=$this->footerTemplate;
		return CStringFormatter::Format($html,$this->dataSource);
	}
}
?>