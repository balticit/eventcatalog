<?php
	class replies_php extends CPageCodeHandler 
	{
		public function replies_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$id = GP("id","");
			$type=GP("type","");
			$replies = $this->GetControl("replies");
			$repls = SQLProvider::ExecuteQuery("
				select 
					vs.`login`,
					r.`date`,
					r.`message`
				from
					(
						select * 
						from `tbl__replies` 
						where 
							`parent_id`=md5(concat('$id','$type')) and active=1
					) as r
				INNER join 
				`vw__identity` as vs
				 	on r.sender_id=vs.tbl_obj_uid 
				 order by r.`date` desc");
			$rkeys = array_keys($repls);
			foreach ($rkeys as $rkey) {
				$repls[$rkey]["date"] = date("d.m.Y", strtotime($repls[$rkey]["date"]));
			}
			$replies->dataSource = $repls;
			//print_r($this);
		}
	}
?>
