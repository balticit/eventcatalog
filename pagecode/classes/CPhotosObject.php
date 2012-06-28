<?php
class CPhotosObject extends CHTMLObject
{
  public $template;
  public $jsTemplate;
  public $numberFieldName = "number";
  public $numberColorFieldName = "numColor";
  public $thumbFileFieldName = "s_image";
  public $thumbSizeField = "s_size";
  public $thumbTemplate;
  public $mediumTemplate;
	public $thumbsPerLine = 4;
  public function CPhotosObject()
  {
    parent::CHTMLObject();
    $file = RealFile("/pagecode/settings/resident_common_files/photosTemplate.html");
    if (is_file($file))
      $this->template = file_get_contents ($file);
  }

  public function RenderHTML() {
    $data = $this->GetDataSourceData();
    if (!is_array($data) || sizeof($data) == 0)
      return null;

    foreach($data as $key=>&$value){
      $value[$this->numberFieldName] = $key+1;
			$value[$this->numberColorFieldName] = $key?'black':'red';
			if (isset($value['title']))
			  $value['jstitle']=addslashes($value['title']);
      else
        $value['jstitle']="";
			$s_image=RealFile(IMAGES_UPLOAD_DIR.$value[$this->thumbFileFieldName]);
			$value[$this->thumbSizeField]="";
			if(is_file($s_image)){
				list($s_width,$s_height,$type,$attr)=getimagesize($s_image);
				if ($s_width&&$s_height)
					if($s_width/$s_height>IMAGE_RES_THUMB_WIDTH/IMAGE_RES_THUMB_HEIGHT)
            $value[$this->thumbSizeField]='width="'.IMAGE_RES_THUMB_WIDTH.'"';
					else
            $value[$this->thumbSizeField]='height="'.IMAGE_RES_THUMB_HEIGHT.'"';
			}
      $value[$this->thumbFileFieldName] = "/upload/".$value[$this->thumbFileFieldName];
		}
    $js = new CRepeater();
    $js->itemTemplate = $this->jsTemplate;
    $js->dataSource = $data;
    $photos_count = sizeof($data);
		$thumbsLinesCount = ceil($photos_count/$this->thumbsPerLine);
		$thumbsLines = "";
		$thumbs = new CRepeater();
		$thumbs->separatorTemplate = '<td><!-- --></td>';
		for ($i = 0; $i < $thumbsLinesCount; $i++){
			$lineData = array_slice($data, $this->thumbsPerLine*$i, $this->thumbsPerLine);
      while (sizeof($lineData)<$this->thumbsPerLine)
        array_push($lineData, array($this->numberFieldName => "",
                                    $this->numberColorFieldName => "black",
                                    $this->thumbFileFieldName => "/images/front/nofotogal.png"));
			$thumbs->itemTemplate = $this->thumbTemplate;
			$thumbs->dataSource = $lineData;
      $thumbsLines .= "<tr>".$thumbs->RenderHTML()."</tr>";
		}
    $m_image=RealFile(IMAGES_UPLOAD_DIR.$data[0]["m_image"]);
		$data[0]["m_size"]="";
		if(is_file($m_image)){
      list($m_width,$m_height,$type,$attr)=getimagesize($m_image);
			if ($m_width&&$m_height)
				if($m_width/$m_height>IMAGE_RES_MEDIUM_WIDTH/IMAGE_RES_MEDIUM_HEIGHT)$data[0]["m_size"]='width="'.IMAGE_RES_MEDIUM_WIDTH.'"';
				else $data[0]["m_size"]='height="'.IMAGE_RES_MEDIUM_HEIGHT.'"';
		}
    return CStringFormatter::Format($this->template,
      array("js"=>$js->Render(),
            "thumbsLines"=>$thumbsLines,
            "photos_count"=>$photos_count,
            "medium_photo"=>CStringFormatter::Format($this->mediumTemplate,
                                                     $data[0])));
  }
}
?>
