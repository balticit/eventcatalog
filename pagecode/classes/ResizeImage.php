<?php
Class ResizeImage
{
  var $imgFile="";
  var $imgWidth=0;
  var $imgHeight=0;
  var $imgType="";
  var $imgAttr="";
  var $type=NULL;
  var $_img=NULL;

  function __construct($imgFile="")
  {
    if (!function_exists("imagecreate"))
    {
      throw new Exception("Error: GD Library is not available.");
    }

    $this->type=Array(
        1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD',
        6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2',
        11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF', 15 => 'WBMP',
        16 => 'XBM');
    if(!empty ($imgFile))
      $this->setImage($imgFile);
  }

  function __destruct() {
    $this->close();
  }

  public function setImage($imgFile)
  {
    if (file_exists($imgFile)){
      if ($this->_img)
        $this->close();
      $this->imgFile=$imgFile;
      $type = null;
      list($this->imgWidth,$this->imgHeight,$type,$this->imgAttr)=getimagesize($this->imgFile);
      $this->imgType=$this->type[$type];

      if($this->imgType=='GIF')
      {
        $this->_img=imagecreatefromgif($this->imgFile);
      }
      elseif($this->imgType=='JPG')
      {
        $this->_img=imagecreatefromjpeg($this->imgFile);
      }
      elseif($this->imgType=='PNG')
      {
        $this->_img=imagecreatefrompng($this->imgFile);
      }
      elseif($this->imgType=='BMP')
      {
        $this->_img=$this->imagecreatefrombmp($this->imgFile);
      }

      if(!$this->_img || !is_resource($this->_img))
      {
        throw new Exception("Error loading ".$this->imgFile);
      }
    }
  }

  public function close()
  {
    imagedestroy($this->_img);
    $this->_img = null;
  }

  public function resize($width, $height, $newfile, $crop = false, $rotateSizes = true)
  {
    if(empty($this->imgFile))
    {
      throw new Exception("File name is not initialised.");
    }

    if($this->imgWidth<=0 || $this->imgHeight<=0)
    {
      throw new Exception("Could not resize given image");
    }

    if($width<=0)
      $width=$this->imgWidth;
    if($height<=0)
      $height=$this->imgHeight;
    
    //if($this->imgWidth < $this->imgHeight) { $rotateSizes = true; }
    
    //if ($rotateSizes){
			$ki = $this->imgWidth/$this->imgHeight;
			$kn = $width/$height;
			if ( ($ki >= 1 && $kn < 1) ||
			     ($ki < 1 && $kn >=1) ){
			  $tmp = $width;
				$width = $height;
				$height = $tmp;
		  }
   // }
		//Calc new image size
    $cWidth = $width;
    $cHeight = $height;
    if ($width >= $this->imgWidth && $height >= $this->imgHeight){
      //Если требуемые размеры больше текущих, то пока ничего не делаем
      $width = $this->imgWidth;
      $height = $this->imgHeight;
      $cWidth = $width;
      $cHeight = $height;
    }
    else{
      $r = 1;
      if ($width >= $this->imgWidth || $height >= $this->imgHeight)
				$r = max($this->imgWidth/$width,$this->imgHeight/$height);
      else
        if ($crop)
				  $r = min($this->imgWidth/$width,$this->imgHeight/$height);
			  else
				  $r = max($this->imgWidth/$width,$this->imgHeight/$height);
      $width = $this->imgWidth/$r;
      $height = $this->imgHeight/$r;
      if ($crop){
        if ($cWidth > $width)
          $cWidth = $width;
        if ($cHeight > $height)
          $cHeight = $height;
      }
      else{
        $cWidth = $width;
        $cHeight = $height;
      }
    }
    //Resize
    $newimg=imagecreatetruecolor($cWidth,$cHeight);
    imagecopyresampled($newimg, $this->_img,
                       ($cWidth-$width)/2,($cHeight-$height)/2,
                       0,0,
                       $width, $height,
                       $this->imgWidth,$this->imgHeight);


    if(!empty($newfile))
    {
      if(!preg_match("/\..*+$/",basename($newfile)))
      {
        if(preg_match("/\..*+$/",basename($this->imgFile),$matches))
           $newfile=$newfile.$matches[0];
      }
    }

    if($this->imgType=='GIF')
    {
      if(!empty($newfile))
        imagegif($newimg,$newfile);
      else
      {
        header("Content-type: image/gif");
        imagegif($newimg); }
    }
    elseif($this->imgType=='JPG' || $this->imgType=='BMP')
    {
      if(!empty($newfile))
        imagejpeg($newimg,$newfile, 90);
      else
      {
        header("Content-type: image/jpeg");
        imagejpeg($newimg);
      }
    }
    elseif($this->imgType=='PNG')
    {
      if(!empty($newfile))
        imagepng($newimg,$newfile);
      else
      {
        header("Content-type: image/png");
        imagepng($newimg);
      }
    }
    imagedestroy($newimg);
  }

  public function getPicRect(&$left,&$top,&$width,&$height)
  {
    $points = array();
    $points_cnt = 5;
    $step = ($this->imgHeight-30)/($points_cnt-1);
    for ($i = 0; $i < $points_cnt; $i++)
      $points[$i] = round($i*$step);

    $stop = false;
    for ($left = 0; $left < $this->imgWidth; $left++){
      $f = imagecolorat($this->_img,$left,$points[0]);
      for ($i = 1; $i < $points_cnt; $i++){
        if (imagecolorat($this->_img,$left,$points[$i]) != $f){
          $stop = true;
          break;
        }
      }
      if ($stop)
        break;
    }
    if ($left > 0)
      $left+=5;
    $stop = false;
    for ($right = $this->imgWidth-1; $right >=0 ; $right--){
      $f = imagecolorat($this->_img,$right,$points[0]);
      for ($i = 1; $i < $points_cnt; $i++){
        if (imagecolorat($this->_img,$right,$points[$i]) != $f){
          $stop = true;
          break;
        }
      }
      if ($stop)
        break;
    }
    if ($right < ($this->imgWidth-1))
      $right-=5;
    $width = $right - $left + 1;
    $height = $this->imgHeight;

    $points = array();
    $points_cnt = 5;
    $step = ($this->imgWidth-60)/($points_cnt-1);
    for ($i = 0; $i < $points_cnt; $i++)
      $points[$i] = round($i*$step);

    $stop = false;
    for ($top = 0; $top < $this->imgHeight; $top++){
      $f = imagecolorat($this->_img,$points[0],$top);
      for ($i = 1; $i < $points_cnt; $i++){
        if (imagecolorat($this->_img,$points[$i],$top) != $f){
          $stop = true;
          break;
        }
      }
      if ($stop)
        break;
    }
    if ($top > 0)
      $top+=5;
    $stop = false;
    for ($bottom = $this->imgHeight-1; $bottom >=0 ; $bottom--){
      $f = imagecolorat($this->_img,$points[0],$bottom);
      for ($i = 1; $i < $points_cnt; $i++){
        if (imagecolorat($this->_img,$points[$i],$bottom) != $f){
          $stop = true;
          break;
        }
      }
      if ($stop)
        break;
    }
    if ($right < ($this->imgWidth-1))
      $right-=5;
    $width = $right + 1 - $left;
    $height = $bottom + 1 - $top;
  }

  public function crop($left,$top,$width,$height,$newfile)
  {
    $newimg=imagecreatetruecolor($width,$height);
    imagecopyresampled($newimg, $this->_img,
                       -$left,-$top,
                       0,0,
                       $this->imgWidth,$this->imgHeight,
                       $this->imgWidth,$this->imgHeight);

    if(!empty($newfile))
    {
      if(!preg_match("/\..*+$/",basename($newfile)))
      {
        if(preg_match("/\..*+$/",basename($this->imgFile),$matches))
           $newfile=$newfile.$matches[0];
      }
    }

    if($this->imgType=='GIF')
    {
      if(!empty($newfile))
        imagegif($newimg,$newfile);
      else
      {
        header("Content-type: image/gif");
        imagegif($newimg); }
    }
    elseif($this->imgType=='JPG')
    {
      if(!empty($newfile))
        imagejpeg($newimg,$newfile);
      else
      {
        header("Content-type: image/jpeg");
        imagejpeg($newimg);
      }
    }
    elseif($this->imgType=='PNG')
    {
      if(!empty($newfile))
        imagepng($newimg,$newfile);
      else
      {
        header("Content-type: image/png");
        imagepng($newimg);
      }
    }
    imagedestroy($newimg);
  }

  private function ConvertBMP2GD($src, $dest = false)
  {
    if(!($src_f = fopen($src, "rb"))) {
    return false;
    }
    if(!($dest_f = fopen($dest, "wb"))) {
    return false;
    }
    $header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,14));
    $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
    fread($src_f, 40));

    extract($info);
    extract($header);

    if($type != 0x4D42) { // signature "BM"
      return false;
    }

    $palette_size = $offset - 54;
    $ncolor = $palette_size / 4;
    $gd_header = "";
    // true-color vs. palette
    $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
    $gd_header .= pack("n2", $width, $height);
    $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
    if($palette_size) {
    $gd_header .= pack("n", $ncolor);
    }
    // no transparency
    $gd_header .= "\xFF\xFF\xFF\xFF";

    fwrite($dest_f, $gd_header);

    if($palette_size) {
    $palette = fread($src_f, $palette_size);
    $gd_palette = "";
    $j = 0;
    while($j < $palette_size) {
    $b = $palette{$j++};
    $g = $palette{$j++};
    $r = $palette{$j++};
    $a = $palette{$j++};
    $gd_palette .= "$r$g$b$a";
    }
    $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
    fwrite($dest_f, $gd_palette);
    }

    $scan_line_size = (($bits * $width) + 7) >> 3;
    $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
    0x03) : 0;

    for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
    // BMP stores scan lines starting from bottom
    fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *
    $l));
    $scan_line = fread($src_f, $scan_line_size);
    if($bits == 24) {
    $gd_scan_line = "";
    $j = 0;
    while($j < $scan_line_size) {
    $b = $scan_line{$j++};
    $g = $scan_line{$j++};
    $r = $scan_line{$j++};
    $gd_scan_line .= "\x00$r$g$b";
    }
    }
    else if($bits == 8) {
    $gd_scan_line = $scan_line;
    }
    else if($bits == 4) {
    $gd_scan_line = "";
    $j = 0;
    while($j < $scan_line_size) {
    $byte = ord($scan_line{$j++});
    $p1 = chr($byte >> 4);
    $p2 = chr($byte & 0x0F);
    $gd_scan_line .= "$p1$p2";
    } $gd_scan_line = substr($gd_scan_line, 0, $width);
    }
    else if($bits == 1) {
    $gd_scan_line = "";
    $j = 0;
    while($j < $scan_line_size) {
    $byte = ord($scan_line{$j++});
    $p1 = chr((int) (($byte & 0x80) != 0));
    $p2 = chr((int) (($byte & 0x40) != 0));
    $p3 = chr((int) (($byte & 0x20) != 0));
    $p4 = chr((int) (($byte & 0x10) != 0));
    $p5 = chr((int) (($byte & 0x08) != 0));
    $p6 = chr((int) (($byte & 0x04) != 0));
    $p7 = chr((int) (($byte & 0x02) != 0));
    $p8 = chr((int) (($byte & 0x01) != 0));
    $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
    } $gd_scan_line = substr($gd_scan_line, 0, $width);
    }

    fwrite($dest_f, $gd_scan_line);
    }
    fclose($src_f);
    fclose($dest_f);
    return true;
  }

  function imagecreatefrombmp($filename)
  {
    $tmp_name = tempnam("/tmp", "GD");
    if($this->ConvertBMP2GD($filename, $tmp_name)) {
      $img = imagecreatefromgd($tmp_name);
      unlink($tmp_name);
      return $img;
    }
    return false;
  }
}
?>
