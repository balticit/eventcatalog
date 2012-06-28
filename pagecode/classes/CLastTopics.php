<?php
	class CLastTopics extends CHTMLObject 
	{
		public $count;
		
		public function RenderHTML()
		{
			$topics = SQLProvider::ExecuteQuery("SELECT t1.topic_id, t1.topic_title
						FROM phpbb_topics AS t1
						INNER JOIN (
						
						SELECT DISTINCT `topic_id`
						FROM `phpbb_posts`
						ORDER BY `post_time` DESC
						LIMIT $this->count
						) AS t2 ON t1.topic_id = t2.topic_id");
			$result = '<table style="border:#999999 1px solid; width:100%;">
							<tr>
								<td style="padding:15px; color:#555555;">
								<span style="font-size:17px; font:Arial, Helvetica, sans-serif; font-weight:bold;">Новые темы на Форуме</span><br><br>';
			
			foreach ($topics as $key=>$value)
			{
				$result.='<a class="forumThemes" href="/forum/viewtopic.php?t='.$value["topic_id"].'">'.$value["topic_title"].'</a><br>';
			}
			$result.='</td>
					</tr>
				</table>';
			return $result;
		}
	}
?>