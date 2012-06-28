<?php
class CStringFormatter
{
	public static function Format($string,$params = array(),$case = true)
	{
		if (is_array($params)){
			$matches = array();
			$repls = array();
			foreach ($params as $key=>$value){
				if (!(is_array($value) || is_object($value))){
					array_push($matches,"{".$key."}");
					array_push($repls,$value);
				}
			}
			if ($case===true)
				return str_replace($matches,$repls,$string);
			else
				return str_ireplace($matches,$repls,$string);
		}
		return $string;
	}

	public static function FromArray($array = array(),$semi = ",")
	{
		return implode($semi,$array);
	}
    
  public static function buildCategoryLinks($title,$link,$class_name = "")
  {
		$title = str_replace("/","^^^",$title);
		$titles = explode("/",$title);
		$class_str = "";
		if ($class_name)
			$class_str = "class=\"$class_name\"";
	  foreach ($titles as &$t){
		  if (is_null($link)) 
				$t = trim(str_replace("^^^","/",$t));
			else
				$t = "<a $class_str href=\"$link\">".trim(str_replace("^^^"," / ",$t))."</a>";
		}
		return implode(" / ",$titles);
  }
}
?>
