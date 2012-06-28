<?php
class CPageBuilder
{
  private static $page;

  public static function GetPage($node)
  {
    if ( is_a($node,"CSiteNode"))
    {
      if (is_null(CPageBuilder::$page))
      {
        CPageBuilder::$page = CPageBuilder::BuildPage(RealFile($node->configfile),$node->id,$node->encoding);
      }
    }
    return CPageBuilder::$page;
  }

  private static function GetFile($param,$encoding)
  {
    $file = RealFile($param["filename"]);
    if (!is_null($file))
    {
      $enc = (isset($param["encoding"]))?$param["encoding"]:null;
      $tenc = (isset($param["targetEncoding"]))?$param["targetEncoding"]:$encoding;;
      return  ((!is_null($enc))&&($enc!=$tenc))?iconv($tenc,$enc,file_get_contents($file)):file_get_contents($file);
    }
    return "";
  }

  private static function BuildPage($configPath,$pid,$encoding)
  {
    $parser = XMLParser::GetInstance();
    $parser->targerEncoding = $encoding;
    $xmlarr= $parser->GetXMLArray($configPath);
    $page = CClassFactory::CreateClassIntance($xmlarr["page"][0]["/"]["codehandler"],$xmlarr["page"][0]["/"]);
    $page->IsPostBack = CURLHandler::IsPost();
    $page->pageId = $pid;
    $page->encoding = $encoding;
    if (isset($xmlarr["page"][0]["control"]))
    {
      foreach ($xmlarr["page"][0]["control"] as $key=>$value)
      {
        $control = CClassFactory::CreateClassIntance($value["/"]["class"],$value["/"]);
        if (!is_null($control))
        {
          $control->pageId = $page->pageId;
          $control->parentId = $page->pageId;
          $control->SetUniqueId();
          if (is_a($control,"CPageObject"))
          {
            $control->ownerPage = $page;
          }
          $control->PreInit();
          if (isset($value["param"][0]))
          {
            foreach ($value["param"] as $param)
            {
              if (isset($param["/"]["name"]))
              {
                if (property_exists($value["/"]["class"],$param["/"]["name"]))
                {
                  $pn = $param["/"]["name"];
                  switch ($param["/"]["type"]) {
                    case "var":
                    {
                      $control->$pn = $param["\\"];
                    }
                    break;
                    case "file":
                    {
                      $file = CPageBuilder::GetFile($param["/"],$encoding);
                      if (!is_null($file))
                      {
                        $control->$pn = $file;
                      }
                    }
                    break;
                    case "datasource":
                    {
                      $driver = CClassFactory::CreateClassIntance($param["/"]["driver"],$param["/"]);
                      if (!is_null($driver))
                      {
                        $driver->sourceEncoding = (isset($param["/"]["sourceEncoding"]))?$param["/"]["sourceEncoding"]:null;
                        $driver->targetEncoding = (isset($param["/"]["targetEncoding"]))?$param["/"]["targetEncoding"]:$encoding;;
                        $control->$pn = $driver->GetData();
                      }
                    }
                    break;
                    case "list":
                    {
                      if (isset($param["item"][0]))
                      {
                        $ilist = array();
                        foreach ($param["item"] as $item)
                        {
                          switch ($item["/"]["type"]) {
                            case "var":
                            {
                              if (isset($item["/"]["key"]))
                              {
                                $ilist[$item["/"]["key"]]=$item["\\"];
                              }
                              else
                              {
                                array_push($ilist,$item["\\"]);
                              }
                            }
                            break;
                            case "keyvalue":
                            {
                              if (isset($item["/"]["key"]))
                              {
                                $ilist[$item["/"]["key"]]=(isset($item["/"]["value"]))?$item["/"]["value"]:null;
                              }
                            }
                            break;
                            case "file":
                            {
                              $file = CPageBuilder::GetFile($item["/"],$encoding);
                              if (!is_null($file))
                              {
                                if (isset($item["/"]["key"]))
                                {
                                  $ilist[$item["/"]["key"]]=$file;
                                }
                                else
                                {
                                  array_push($ilist,$file);
                                }
                              }
                            }
                            break;
                            case "class":
                            {
                              $class = null;
                              if (IsArrayValue($item["/"],"class",$class))
                              {
                                $datavar = null;
                                if (IsArrayValue($item["/"],"datavar",$datavar))
                                {
                                  $item["/"][$datavar]=$item["\\"];
                                }
                                $obj = CClassFactory::CreateClassIntance($class,$item["/"]);
                                if (!is_null($obj))
                                {
                                  if (property_exists($class,"parentId"))
                                  {
                                    $obj->parentId = $control->uniqueId;
                                  }
                                  if (method_exists($obj,"SetUniqueId"))
                                  {
                                    $obj->SetUniqueId();
                                  }
                                  if (is_a($obj,"CPageObject"))
                                  {
                                    $obj->ownerPage = $page;
                                  }
                                  if (method_exists($obj,"PreInit"))
                                  {
                                    $obj->PreInit();
                                  }
                                  if (method_exists($obj,"Init"))
                                  {
                                    $obj->Init();
                                  }
                                  if (isset($item["/"]["key"]))
                                  {
                                    $ilist[$item["/"]["key"]]=$obj;
                                  }
                                  else
                                  {
                                    array_push($ilist,$obj);
                                  }

                                }
                              }
                            }
                            break;
                          }
                        }
                        $control->$pn = $ilist;
                      }
                    }
                    break;
                  }
                }
              }
            }
          }

          $control->Init();
          $page->controls[$control->uniqueId] = $control;
        }
      }
    }
    return $page;
  }
}

?>