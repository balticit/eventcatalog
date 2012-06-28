<?php
class CHTMLObject extends CPageObject
{


  public function CHTMLObject()
  {
    $this->CPageObject();

  }
  
  public function PreRender()
  {

  }

  public function RenderHTML()
  {
    return "<p>HTML output not implemented</p>";
  }

  public function Render()
  {
    $this->PreRender();
    return $this->RenderHTML();
  }

}
?>