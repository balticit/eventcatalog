<?php 
class watermark
{
	public static  function create_watermark( $main_img_obj, $text, $font, $r = 128, $g = 128, $b = 128, $alpha_level = 100 )
	{
		$width = imagesx($main_img_obj);
		$height = imagesy($main_img_obj);
		$angle =  -rad2deg(atan2((-$height),($width)));

		$text = " ".$text." ";

		$c = imagecolorallocatealpha($main_img_obj, $r, $g, $b, $alpha_level);
		$size = (($width+$height)/2)*2/strlen($text);
		$box  = imagettfbbox ($size, $angle, $font, $text );
		$x = $width/2 - abs($box[4] - $box[0])/2;
		$y = $height/2 + abs($box[5] - $box[1])/2;

		imagettftext($main_img_obj,$size ,$angle, $x, $y, $c, $font, $text);
		return $main_img_obj;
	}

	private static function convertStr($isoline) {
		for ($i = 0; $i < strlen($isoline); $i++) {
			$thischar = substr($isoline, $i, 1);
			$charcode = ord($thischar);
			$uniline .= ($charcode > 191) ? '&#'.(1040 + ($charcode - 192)).';' : $thischar;
		}
		return $uniline;
	}

	public static function getGoldHeight($width,$height)
	{
		if ($height<=($width*0.75))
		{
			return $height;
		}
		else 
		{
			return $width*0.75;
		}
	}
	
	public static  function create_plain_watermark( $main_img_obj, $text, $font, $r = 128, $g = 128, $b = 128, $alpha_level = 100 )
	{
		
		$width = imagesx($main_img_obj);
		$height = imagesy($main_img_obj);
		$ms = $width>$height?$height:$width;
		$goriz = true;//($width>=$height);
		$text = " ".$text." ";
		$text = iconv("windows-1251","utf-8",$text);
		$c = imagecolorallocatealpha($main_img_obj, $r, $g, $b, $alpha_level);
		$size = floor(watermark::getGoldHeight($goriz?$width:$height,$goriz?$height:$width)/15);
		$angle = $goriz?0:90;
		$box  = imagettfbbox ($size, $angle, $font, $text );		
		$x = (int)(($width - abs($box[4] - $box[0]))/2);
		$y = (int)(($height + abs($box[5]))/2);
    imagettftext($main_img_obj,$size ,$angle, $x,$y, $c, $font, $text);
		return $main_img_obj;
	}
}
 ?>
