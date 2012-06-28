<?php
class CURLHandler
{
	private static $isPost = null;

	public static $currentNode;

	public static $currentPath;

	public static $rewriteParams = array();

	public static $query = "";

	private static function findNode(CSiteNode $node, $path, &$path_num)
	{
		if (isset($path[$path_num]) &&
		    is_array($node->childs) &&
				array_key_exists($path[$path_num]."/",$node->childs)){
		  $path_num++;
			return CURLHandler::findNode($node->childs[$path[$path_num-1]."/"],$path,$path_num);
		}
		else {
		  return $node;
		}
	}

	private static function findOldNode(CSiteNode $node, $path, &$path_num)
	{
		if (isset($path[$path_num]) &&
		    is_array($node->childsHidden) &&
				array_key_exists($path[$path_num]."/",$node->childsHidden)){
		  $path_num++;
			return CURLHandler::findNode($node->childsHidden[$path[$path_num-1]."/"],$path,$path_num);
		}
		else {
		  return $node;
		}
	}

  public static function GetTranslitID($resType, $id)
	{
	  if ($resType == 'book') $resType = 'public';
    if ($resType == 'contractor' ||
		    $resType == "area" ||
				$resType == 'artist' ||
				$resType == 'agency' ||
                $resType=='eventtv'||
                $resType=='public'){
		  return SQLProvider::ExecuteScalar(
			  "select title_url from tbl__".$resType."_doc where tbl_obj_id = $id");
	  }
		return null;
	}

  public static function Prepare()
	{
		$request = $_SERVER['REQUEST_URI'];
		$path= preg_split("/\?/",$request,2);
        if(isset($path[1])){
            if(isset($_GET['group'])||
                isset($_GET['subgroup'])||
                isset($_GET['activity'])||
                (isset($_GET['type'])&&strstr($path[0],"area"))||
                isset($_GET['subtype'])
            ){
                //this is group command
                CURLHandler::$query="";
                $path=$path[0];
                ValidateDir(&$path);
                CURLHandler::$currentNode =& CSiteMapHandler::GetRootNode();
                $path = substr($path,1);
                $path = preg_split("/\//",$path, -1, PREG_SPLIT_NO_EMPTY);
                $path_num = 0;
                CURLHandler::$currentNode =& CURLHandler::findNode(CURLHandler::$currentNode,$path,$path_num);
                $params = array_splice($path,$path_num);
                CURLHandler::$currentPath = "/".implode("/",$path);
                ValidateDir(CURLHandler::$currentPath);
                $assoc = $_GET;
                $id=CURLHandler::GetGroupId(&$assoc);
                CURLHandler::Redirect301(CURLHandler::$currentPath.(isset($id)?$id:'').CURLHandler::BuildQueryParams($assoc));
            }
        }else{
            CURLHandler::$query = (isset($path[1]))?$path[1]:"";
        }
		$path=$path[0];
		ValidateDir(&$path);
		CURLHandler::$currentNode =& CSiteMapHandler::GetRootNode();
		$path = substr($path,1);
		$path = preg_split("/\//",$path, -1, PREG_SPLIT_NO_EMPTY);
		$path_num = 0;
		CURLHandler::$currentNode =& CURLHandler::findNode(CURLHandler::$currentNode,$path,$path_num);
		$params = array_splice($path,$path_num);
		CURLHandler::$currentPath = "/".implode("/",$path);
		ValidateDir(CURLHandler::$currentPath);
		$parcount = sizeof($params);
    if ($parcount>1){
		  //check old url
      if ($parcount%2){
				$oldNode = CURLHandler::$currentNode;
				$par_num = 0;
				$oldNode =& CURLHandler::findOldNode($oldNode,$params,$par_num);
				if ($par_num && ($parcount - $par_num)%2 == 0){
				  $for_id = $params[$par_num-1];
					$params = array_splice($params,$par_num);
					$parcount = floor($parcount/2);
					$newParams = array();
					for ($i=0;$i<$parcount;$i++)
					{
						$k = $i*2;
						if (isset($params[$k]))
						{
							if (strlen($params[$k])>0)
							{
								$newParams[$params[$k]] = (isset($params[$k+1]))?$params[$k+1]:null;
							}
						}
					}
					$id = null;
					if (array_key_exists('id',$newParams)){
					  $id = $newParams['id'];
						unset($newParams['id']);
						if ($id) {
							if (CURLHandler::$currentNode->id_translit == 1) {
								$id = CURLHandler::GetTranslitID(str_replace('/','',CURLHandler::$currentNode->node),$id);
							}
							else{
								$id = $for_id.$id;
							}
						}
				  }
					CURLHandler::Redirect301(CURLHandler::$currentPath.(isset($id)?$id:'').CURLHandler::BuildQueryParams($newParams));
				}
				else
				  CURLHandler::ErrorPage();
			}
			else{
				$parcount = floor($parcount/2);
				$newParams = array();
				for ($i=0;$i<$parcount;$i++)
				{
					$k = $i*2;
					if (isset($params[$k]))
					{
						if (strlen($params[$k])>0)
						{
							$newParams[$params[$k]] = (isset($params[$k+1]))?$params[$k+1]:null;
						}
					}
				}
				CURLHandler::Redirect301(CURLHandler::$currentPath.CURLHandler::BuildQueryParams($newParams));
			}
		}
		else if ($parcount){
		  if (CURLHandler::$currentNode->id_translit == 1){
			  CURLHandler::$currentPath .= $params[0]."/";
				$key = array_keys(CURLHandler::$currentNode->childsHidden);
				if (!sizeof($key))
				  CURLHandler::ErrorPage();
				$key = $key[0];
				CURLHandler::$currentNode =& CURLHandler::$currentNode->childsHidden[$key];
				CURLHandler::$rewriteParams = array('id'=>urldecode($params[0]));
			}
			else {
        if (preg_match("/[a-z]+/",$params[0],$key) &&
				    preg_match("/[0-9]+/",$params[0],$val) &&
				    is_array(CURLHandler::$currentNode->childsHidden) &&
						array_key_exists($key[0]."/",CURLHandler::$currentNode->childsHidden)){
				  CURLHandler::$currentNode =& CURLHandler::$currentNode->childsHidden[$key[0]."/"];
					CURLHandler::$rewriteParams = array('id'=>$val[0]);
				}
				else
				  CURLHandler::ErrorPage();
			}
		}
		/*------------------------------------------------------*/
	}

	public static function Redirect($url,$die = true)
	{
		header("Location: $url");
		if ($die)
		{
			exit;
		}
	}

	public static function Redirect301($url,$die = true)
	{
	  header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
		if ($die)
		  exit;
	}

	public static function BuildRewriteParams($params)
	{
		$url = "";
		foreach ($params as $key=>$value)
		{
			$url.="$key/$value/";
		}
		return $url;
	}

	public static function BuildQueryParams($params)
	{
		$query = http_build_query($params);
		return IsNullOrEmpty($query)?"":"?$query";
	}

	public static function BuildFullLink($params,$url = null)
	{
		return (is_null($url)?CURLHandler::$currentPath:$url).CURLHandler::BuildQueryParams($params);
	}

	public static function IsPost()
	{
		if (is_null(self::$isPost))
		{
			self::$isPost = (strtolower($_SERVER['REQUEST_METHOD'])=="post");
		}
		return self::$isPost;
	}

	public static function ErrorPage()
	{
		header("HTTP/1.0 404 Not Found");
		#header("HTTP/1.1 404 Not Found");
		#header("Status: 404 Not Found");
		include(ROOTDIR."404.php");
		exit();
	}

	public static function CheckRewriteParams($availableParams = array())
	{
		foreach ($_GET as $rp=>$rp_val)
			if (array_search($rp,$availableParams) === false)
			  CURLHandler::ErrorPage();
	}

    private static function GetGroupId($params)
    {
        if(!IsNullOrEmpty($params['subgroup'])){
            $subgroup_id = $params['subgroup'];
            unset($params['subgroup']);
            unset($params['group']);
            return SQLProvider::ExecuteScalar("select title_url from tbl__artist_subgroup where tbl_obj_id=$subgroup_id");
        }
        if(!IsNullOrEmpty($params['group'])){
            $group_id = $params['group'];
            unset($params['group']);
            unset($params['subgroup']);
            return SQLProvider::ExecuteScalar("select title_url from tbl__artist_group where tbl_obj_id=$group_id");
        }
        if(!IsNullOrEmpty($params['activity'])){
            $act_id = $params['activity'];
            unset($params['activity']);
            if(strstr(CURLHandler::$currentPath,"agency")){
                return SQLProvider::ExecuteScalar("select title_url from tbl__agency_type where tbl_obj_id=$act_id");
            }
            else{
                return SQLProvider::ExecuteScalar("select title_url from tbl__activity_type where tbl_obj_id=$act_id");
            }
        }
        if(!IsNullOrEmpty($params["subtype"])){
            $type_id = $params['subtype'];
            unset($params['type']);
            unset($params['subtype']);
            return SQLProvider::ExecuteScalar("select title_url from tbl__area_subtypes where tbl_obj_id=$type_id");

        }
        if(!IsNullOrEmpty($params["type"])){
            $type_id = $params['type'];
            unset($params['type']);
            unset($params['subtype']);
            return SQLProvider::ExecuteScalar("select title_url from tbl__area_types where tbl_obj_id=$type_id");

        }
        CURLHandler::ErrorPage();
        exit();
    }
}
?>
