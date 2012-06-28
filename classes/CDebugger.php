<?php
class CDebugger
{
	private static $debugNumber = 0;

	public static function DebugLine($data,$semi = "<br/>")
	{
		if (DEBUG)
		{
			CDebugger::$debugNumber++;
			print "Debug output ".CDebugger::$debugNumber.": $data $semi ";
		}
	}

	public static function DebugArray($data,$semi = "<br/>",$escape = true)
	{
		if (DEBUG)
		{
			CDebugger::$debugNumber++;
			print "Debug output ".CDebugger::$debugNumber.": $semi ";
			
			print ($escape)?htmlspecialchars(print_r($data,true)):print_r($data,true);

			print $semi;
		}
	}

	/*private static function PrintRecurse($name,&$object,$escape = true,$spaces = "    ")
	{
		if ((!is_array($object))&&(!is_object($object)))
		{
			print $name."=>".$object;
		}
		else
		{
			$type = gettype($object)." ".((is_object($object))?get_class($object):"");
			print "$spaces [$name] => $type
$spaces      (";
			foreach ($object as $key => $value) 
			{
				if ((is_array($object))||(is_object($object)))
				{	
					CDebugger::PrintRecurse($key,&$value,$escape,$spaces."    ");	
				}
				else 
				{
print "$spaces         [$key]=>$value";					
				}
			}
print "$spaces      )";

		}
	}*/
}
?>