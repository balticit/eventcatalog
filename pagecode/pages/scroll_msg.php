<?php
class scroll_msg_php extends CPageCodeHandler
{
	public function scroll_msg_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$max_msg_length = 55;
    $msg_count = 56;
		$newComments = $this->GetControl("msgs");
		$res_type = GP("res_type");
		$newCommentsItems = SQLProvider::ExecuteQuery(
		"select if((c.sender_id is not null) and (c.sender_id <> ''),u.title,if(c.sender_name<>'',c.sender_name,'Аноним')) as sended_by,
		        t.title as target_name, c.target_id as target_id,
                CONCAT('/u_profile/?type=',u.type,'&id=',u.user_id) as sender_link,
				if (t.type = 'user',
				  CONCAT('/u_profile/?type=user&id',t.user_id),
					CONCAT('/',t.type,'/',t.title_url)) as target_link,
				if(CHAR_LENGTH(c.text)>$max_msg_length,CONCAT(LEFT(c.text,$max_msg_length),'...'),c.text) as message_text
				from tbl__comments c
			      left join vw__all_users_full u on c.sender_id = u.user_key
				  left join vw__all_users_full t on t.user_key = c.target_id
			where c.active = 1 and IFNULL(is_deleted,0) = 0
			".($res_type?" and t.type = '$res_type'":"")."
		 order by c.time desc limit $msg_count");

		$regex_url = "/(ht|f)tp(s?)\\:\\/\\/[0-9a-zA-Z]([-.\\w]*[0-9a-zA-Z])*(:(0-9)*)*(\\/?)([a-zA-Z0-9\\-\\.\\?\\,\\'\\/\\\\\\+&amp;%\\$#_]*)?/m";
		foreach ($newCommentsItems as &$newCommentsItem)
		{
			$target_name = $newCommentsItem["target_name"];
			if (strlen($target_name) == 0)
			{
				$target = $newCommentsItem["target_id"];
				$target_id = "";
				$target_type = "";
				for ($k = 0; $k<strlen($target); $k++)
				{
					if(is_numeric($target[$k]))
					{
						$target_id .= $target[$k];
					}
					else
					{
						$target_type .= $target[$k];
					}
				}

				$newCommentsItem["target_name"] = "$target_id - $target_type";

				if ($target_type == "book")
				{
					$newCommentsItem["target_link"]="/book/details$target_id";
					$title = SQLProvider::ExecuteQuery("select * from tbl__public_doc where tbl_obj_id=$target_id");
					if (sizeof($title)>0)
					{
						$newCommentsItem["target_name"] =$title[0]["title"];
					}
				}
				if ($target_type == "eventtv")
				{
					$newCommentsItem["target_link"]="/eventtv/details$target_id";
					$title = SQLProvider::ExecuteQuery("select * from tbl__eventtv_doc where tbl_obj_id=$target_id");
					if (sizeof($title)>0)
					{
						$newCommentsItem["target_name"] =$title[0]["title"];
					}
				}if ($target_type == "resident_news")
				{
					$newCommentsItem["target_link"]="/resident_news/news/id/$target_id";
					$title = SQLProvider::ExecuteQuery("select * from tbl__resident_news where tbl_obj_id=$target_id");
					if (sizeof($title)>0)
					{
						$newCommentsItem["target_name"] =$title[0]["title"];
					}
				}
				if ($target_type == "magazine")
				{
					$newCommentsItem["target_link"]="/magazines/details$target_id";
					$title = SQLProvider::ExecuteQuery("select * from tbl__magazines where tbl_obj_id=$target_id");
					if (sizeof($title)>0)
					{
						$newCommentsItem["target_name"] =$title[0]["publication"];
					}
				}
				if ($target_type == "event")
				{
					$newCommentsItem["target_link"]="/events/details$target_id";
					$title = SQLProvider::ExecuteQuery("select * from tbl__events where tbl_obj_id=$target_id");
					if (sizeof($title)>0)
					{
						$newCommentsItem["target_name"] =$title[0]["title"];
					}
				}
				if ($target_type == "news")
				{
					$newCommentsItem["target_link"]="/news/details$target_id";
					$title = SQLProvider::ExecuteQuery("select * from tbl__news where tbl_obj_id=$target_id");
					if (sizeof($title)>0)
					{
						$newCommentsItem["target_name"] =$title[0]["title"];
					}
				}
				if (strlen($newCommentsItem["target_name"]) > 30)
				{
					$newCommentsItem["target_name"] = substr($newCommentsItem["target_name"],0,30)."...";
				}
			}
			$sender_link = $newCommentsItem["sender_link"];
			$sended_by = $newCommentsItem["sended_by"];
			if (strlen($sender_link)>0)
			{
				$newCommentsItem["sended_html"] = "<a href=\"$sender_link\" target=\"_top\">$sended_by</a>";
			}
			else
			{
				$newCommentsItem["sended_html"] = "<span class=\"common\">$sended_by</span>";
			}

			$urls = array();
			$newCommentsItem["u_text"] = $newCommentsItem["message_text"];
			preg_match_all($regex_url,$newCommentsItem["message_text"],&$urls);

      $urls = $urls[0];

      if (sizeof($urls)>0)
      {
        $url_repls = array();
        for ($j=0;$j<sizeof($urls);$j++)
        {
          $sublen = floor(strlen($urls[$j])*3/4);
          $url_repls[] = '<a href="'.$urls[$j].'" target="_blank">'.substr( $urls[$j],0,$sublen)."...</a>";
        }
        $newCommentsItem["message_text"] = str_replace($urls,$url_repls,$newCommentsItem["message_text"]);
      }

      $newCommentsItem["message_text"] = strip_tags($newCommentsItem["message_text"]);
		}
		$newComments->dataSource = $newCommentsItems;
	}
}
?>
