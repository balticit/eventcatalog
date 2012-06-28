<?php
function GenerateTeamplateHandlers($templateDir,$configDir,$handlerDir,$configTemplate,$handlerTemplate,$force = false)
{
	ValidateDir(&$templateDir);
	ValidateDir(&$configDir);
	ValidateDir(&$handlerDir);
	$asbTemplateDir = ROOTDIR.$templateDir;
	$asbConfigDir = ROOTDIR.$configDir;
	$asbHandlerDir = ROOTDIR.$handlerDir;
	if (is_dir($templateDir))
	{
		$files = scandir($templateDir);
		$files = preg_grep("/^.*.php$/",$files);
		$config = "";
		$handler = "";
		if (sizeof($files)>0)
		{
			$config = file_get_contents(ROOTDIR.$configTemplate);
			$handler = file_get_contents(ROOTDIR.$handlerTemplate);
		}
		foreach ($files as $key=>$value)
		{
			if (is_file($templateDir.$value))
			{
				$fileconfig = CreateConfigFromBaseTemplate(str_replace(".","_",$value),$templateDir,$value,&$config);
				$xvalue =  preg_replace("/.php$/",".xml",$value);
				if (file_exists($asbConfigDir.$xvalue))
				{
					if ($force)
					{
						unlink($asbConfigDir.$xvalue);
					}
				}
				if (!file_exists($asbConfigDir.$xvalue))
				{
					file_put_contents($asbConfigDir.$xvalue,$fileconfig);
				}
				$filehadler = CreateHandlerFromBaseTemplate(str_replace(".","_",$value),&$handler);
				if (file_exists($asbHandlerDir.$value))
				{
					if ($force)
					{
						unlink($asbHandlerDir.$value);
					}
				}
				if (!file_exists($asbHandlerDir.$value))
				{
					file_put_contents($asbHandlerDir.$value,$filehadler);
				}
			}
		}
	}
}

function GenerateTeamplateHandler($templateFile,$configDir,$handlerDir,$configTemplate,$handlerTemplate,$force = false)
{
	$file = RealFile($templateFile);
	ValidateDir(&$configDir);
	ValidateDir(&$handlerDir);
	$asbConfigDir = ROOTDIR.$configDir;
	$asbHandlerDir = ROOTDIR.$handlerDir;
	if (file_exists($file))
	{
		$fileinfo = pathinfo($file);
		$templateDir = RealDir($fileinfo["dirname"]);
		$value = $fileinfo["basename"];
		$config = file_get_contents(ROOTDIR.$configTemplate);
		$handler = file_get_contents(ROOTDIR.$handlerTemplate);
		$fileconfig = CreateConfigFromBaseTemplate(str_replace(".","_",$value),$templateDir,$value,&$config);
		$xvalue =  preg_replace("/.php$/",".xml",$value);
		if (file_exists($asbConfigDir.$xvalue))
		{
			if ($force)
			{
				unlink($asbConfigDir.$xvalue);
			}
		}
		if (!file_exists($asbConfigDir.$xvalue))
		{
			file_put_contents($asbConfigDir.$xvalue,$fileconfig);
		}
		$filehadler = CreateHandlerFromBaseTemplate(str_replace(".","_",$value),&$handler);
		if (file_exists($asbHandlerDir.$value))
		{
			if ($force)
			{
				unlink($asbHandlerDir.$value);
			}
		}
		if (!file_exists($asbHandlerDir.$value))
		{
			file_put_contents($asbHandlerDir.$value,$filehadler);
		}

	}
}

function CreateConfigFromBaseTemplate($handlerName,$templateDir,$templateFilename,&$template)
{
	return str_replace(array("[handler]", "[templateDir]","[templateFile]"),array($handlerName,$templateDir,$templateFilename),$template);
}

function CreateHandlerFromBaseTemplate($handlerName,&$template)
{
	return str_replace(array("[handler]"),array($handlerName),$template);
}
?>