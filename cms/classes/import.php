<?php
class  DendroImporter
{
    private function GetFilename($filename)
    {
        $files = preg_split("/[\\/]/", $filename, -1, PREG_SPLIT_NO_EMPTY);
        return sizeof($files) == 0 ? $filename : $files[sizeof($files) - 1];
    }

    private function AddColumn($table, $colname, $coltype, $colsize = null, $isnull = true, $default = null)
    {
        if (sizeof(SQLProvider::ExecuteQuery("show COLUMNs from `$table` WHERE `FIELD`='$colname'")) == 0) {
            SQLProvider::ExecuteNonReturnQuery("ALTER TABLE `$table` ADD `$colname` $coltype" . (is_null($colsize) ? "" : "($colsize) ") . ($isnull ? " NULL" : " NOT NULL") . (is_null($default) ? "" : " DEFAULT $default"));
        }
    }

    private function DeleteColumn($table, $colname)
    {
        if (sizeof(SQLProvider::ExecuteQuery("show COLUMNs from `$table` WHERE `FIELD`='$colname'")) > 0) {
            SQLProvider::ExecuteNonReturnQuery("ALTER TABLE `$table` DROP `$colname`");
        }
    }

    private function ChangeColumn($table, $colname, $new_colname, $coltype, $colsize = null, $isnull = true)
    {

        SQLProvider::ExecuteNonReturnQuery("ALTER TABLE `$table` CHANGE `$colname` `$new_colname` $coltype" . (is_null($colsize) ? "" : "($colsize) ") . ($isnull ? " NULL" : " NOT NULL"));

    }
    
    public function changeLogin(){
	$this->ChangeColumn("tbl__registered_user","login","login","VARCHAR",255);
    }

    private function AddIndex($table, $index_name, $cols_list)
    {
        if (sizeof(SQLProvider::ExecuteQuery("SHOW INDEX FROM `$table` WHERE `KEY_NAME` = '$index_name'")) == 0) {
            SQLProvider::ExecuteNonReturnQuery("ALTER TABLE `$table` ADD INDEX `$index_name` ($cols_list)");
        }
    }
		private function AddUniqueIndex($table, $index_name, $cols_list)
    {
        if (sizeof(SQLProvider::ExecuteQuery("SHOW INDEX FROM `$table` WHERE `KEY_NAME` = '$index_name'")) == 0) {
            SQLProvider::ExecuteNonReturnQuery("ALTER TABLE `$table` ADD UNIQUE INDEX `$index_name` ($cols_list)");
        }
    }


    public function Public_Topics_Order()
    {
        $this->AddColumn("tbl__public_topics", "order_num", "INT", null, true);
    }
	
	public function addCreationDateColumn()
	{
		$this->AddColumn('tbl__news','creation_date','DATE',null,true,null);
		$this->AddColumn('tbl__events','creation_date','DATE',null,true,null);
	}

    public function Ad_blocks_subscribe()
    {
        SQLProvider::ExecuteNonReturnQuery("
		CREATE TABLE tbl__ad_blocks(
tbl_obj_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
title VARCHAR(50) NULL,
ad_text VARCHAR(50) NULL,
date_end DATE NULL,
user_type VARCHAR(20) NOT NULL,
user_id INT NOT NULL,
other_link VARCHAR( 300 ) NULL
) ENGINE = MYISAM ;");
        SQLProvider::ExecuteNonReturnQuery("
CREATE TABLE  `tbl__subscribed` (
`email` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY (  `email` )
) ENGINE = MYISAM ;

");
    }

    public function UserLike()
    {
        SQLProvider::ExecuteNonReturnQuery(" CREATE  TABLE  `tbl__userlike` (
`tbl_obj_id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `to_resident_id` int( 11  ) ,
 `to_resident_type` varchar( 20  )  ,
 `from_resident_id` int( 11  ) ,
 `from_resident_type` varchar( 20  )  ,
 `date` datetime ,
 PRIMARY  KEY (  `tbl_obj_id`  )) ENGINE  =  MyISAM;");
    }

    public function Artist_City()
    {
        $this->AddColumn("tbl__artist_doc", "city", "INT", null, true);
        $this->AddColumn("tbl__artist_doc", "other_city", "VARCHAR", 40, true);
    }

    public function Msg_Read()
    {
        $this->AddColumn("tbl__messages", "time_read", "DATETIME", null, true);
    }

    public function Agency_Adds()
    {
        SQLProvider::ExecuteNonReturnQuery("CREATE TABLE `tbl__agency_photos` (
`child_id` INT NOT NULL ,
`parent_id` INT NOT NULL ,
PRIMARY KEY ( `child_id` , `parent_id` )
) ENGINE = MYISAM ;");

        SQLProvider::ExecuteNonReturnQuery("
CREATE TABLE `tbl__agency2activity` (
`tbl_obj_id` int( 4 ) NOT NULL ,
`kind_of_activity` int( 4 ) NOT NULL ,
`advanced` int( 4 ) DEFAULT '0',
PRIMARY KEY ( `tbl_obj_id` , `kind_of_activity` )
) ENGINE = MYISAM;");

    }

    public function Agency_View()
    {
        SQLProvider::ExecuteNonReturnQuery("
	insert into tbl__agency2activity(tbl_obj_id, kind_of_activity)
	SELECT tbl_obj_id, kind_of_activity
FROM tbl__agency_doc
WHERE tbl_obj_id NOT
IN (

SELECT tbl_obj_id
FROM tbl__agency2activity
)
AND kind_of_activity >0");
        SQLProvider::ExecuteNonReturnQuery("CREATE VIEW `vw__agency_list3` AS
			SELECT
			  a.*,
        if(a.city>0, c.title, a.other_city) as city_title,
        date_format(a.registration_date,'%d.%m.%Y') AS formatted_date
			from  tbl__agency_doc a
      left join tbl__city c on c.tbl_obj_id = a.city
      where a.active=1
			");
    }

    public function EventTV()
    {

        SQLProvider::ExecuteNonReturnQuery("CREATE TABLE IF NOT EXISTS `tbl__eventtv_topics` (
`tbl_obj_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 50 ) NOT NULL ,
`order_num` INT NOT NULL ,
`color` VARCHAR( 6 ) NOT NULL
) ENGINE = MYISAM ;");
        SQLProvider::ExecuteNonReturnQuery("CREATE TABLE IF NOT EXISTS `tbl__eventtv_doc` (
`tbl_obj_id` int( 5 ) NOT NULL ,
`title` varchar( 100 ) DEFAULT NULL ,
`annotation` varchar( 100 ) DEFAULT NULL ,
`text` text NOT NULL ,
`active` int( 4 ) NOT NULL ,
`registration_date` varchar( 19 ) DEFAULT 'Null',
`is_new` int( 4 ) NOT NULL DEFAULT '0',
`logo_image` varchar( 100 ) DEFAULT NULL ,
PRIMARY KEY ( `tbl_obj_id` )
) ENGINE = MYISAM DEFAULT CHARSET = cp1251;");

        SQLProvider::ExecuteNonReturnQuery("CREATE TABLE IF NOT EXISTS `tbl__eventtv2topic` (
`child_id` int( 4 ) NOT NULL ,
`parent_id` int( 4 ) NOT NULL ,
PRIMARY KEY ( `child_id` , `parent_id` )
) ENGINE = MYISAM DEFAULT CHARSET = cp1251;");
        SQLProvider::ExecuteNonReturnQuery("
    CREATE VIEW `vw__eventtv_doc` AS
    select
      `pd`.`tbl_obj_id` AS `tbl_obj_id`,
      `pd`.`title` AS `title`,
      `pd`.`title_url` AS `title_url`,
      `pd`.`logo_image` AS `logo_image`,
      `pd`.`annotation` AS `annotation`,
      `pd`.`is_new` AS `is_new`,
      `p2t`.`parent_id` AS `dir_id`,
      `pd`.`registration_date` AS `registration_date`
      from `tbl__eventtv_doc` `pd`
        join `tbl__eventtv2topic` `p2t` on `pd`.`tbl_obj_id` = `p2t`.`child_id`
      where `pd`.`active` = 1 order by `pd`.`registration_date` desc;
    ");
        SQLProvider::ExecuteNonReturnQuery("CREATE TABLE IF NOT EXISTS `tbl__eventtv_photos` (
`tbl_obj_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`file_name` VARCHAR( 512 ) NOT NULL
) ENGINE = MYISAM ;");


        $this->DeleteColumn("tbl__public_topics", "view_eventtv");
    }

    public function Area_Artist_Agency_Fields()
    {
        $this->AddColumn("tbl__artist_doc", "phone2", "VARCHAR", 30, true);

        $this->AddColumn("tbl__agency_doc", "youtube_video", "VARCHAR", 250, true);
        $this->AddColumn("tbl__contractor_doc", "youtube_video", "VARCHAR", 250, true);

        $this->AddColumn("tbl__area_doc", "youtube_video", "VARCHAR", 250, true);
        $this->AddColumn("tbl__area_doc", "phone2", "VARCHAR", 30, true);
        $this->AddColumn("tbl__area_doc", "cost_banquet", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "cost_official_buffet", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "cost_rent", "VARCHAR", 100, true);
        $this->AddColumn("tbl__area_doc", "cost_service", "VARCHAR", 100, true);
        $this->AddColumn("tbl__area_doc", "kitchen_features", "VARCHAR", 100, true);
        $this->AddColumn("tbl__area_doc", "invite_catering", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "service_entrance", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "wardrobe", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "stage", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "makeup_rooms", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "dancing_size", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "car_into", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "light", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "sound", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "panels", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "projector", "INT", null, true);
        $this->AddColumn("tbl__area_doc", "other", "VARCHAR", 100, true);
        $this->AddColumn("tbl__area_doc", "manager_name", "VARCHAR", 100, true);

        $this->AddColumn("tbl__area_halls", "max_places_conference", "INT", 4, true);
        $this->AddColumn("tbl__area_halls", "cost_conference", "INT", null, true);

        $this->ChangeColumn("tbl__area_doc", "invite_catering", "invite_catering", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "service_entrance", "service_entrance", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "wardrobe", "wardrobe", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "stage", "stage", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "makeup_rooms", "makeup_rooms", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "dancing_size", "dancing_size", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "car_into", "car_into", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "light", "light", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "sound", "sound", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "panels", "panels", "INT", null, true);
        $this->ChangeColumn("tbl__area_doc", "projector", "projector", "INT", null, true);


    }
	
	public function fixCatUrl(){
		SQLProvider::ExecuteNonReturnQuery("update tbl__agency_type set title_url='svadebnye_agentstva' where title_url='cvadebnye_agentstva'");
	}


    public function AreaView()
    {
        SQLProvider::ExecuteNonReturnQuery("
    CREATE OR REPLACE VIEW `vw__area_halls` as
    select area_id, sum(max_places_banquet) sum_places_banquet from tbl__area_halls group by area_id");

        SQLProvider::ExecuteNonReturnQuery("
    CREATE OR REPLACE VIEW `vw__area_list2` as
    SELECT a.*, h.sum_places_banquet
FROM `tbl__area_doc` a
left join vw__area_halls h on h.area_id = a.tbl_obj_id
where a.active =1");
    }

    public function ArtistStyles()
    {
        $this->DeleteColumn("tbl__styles", "artist_group");
        $this->AddColumn("tbl__styles", "style_group", "INT", null, true);
        $this->AddColumn("tbl__artist_group", "style_group", "INT", null, true);
        SQLProvider::ExecuteNonReturnQuery("
    CREATE TABLE IF NOT EXISTS `tbl__styles_groups` (
    `tbl_obj_id`  INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `title` VARCHAR( 100 ) NOT NULL
    ) ENGINE = MYISAM ;");
    }

    public function EventTVmain()
    {
        SQLProvider::ExecuteNonReturnQuery("
    CREATE TABLE IF NOT EXISTS `tbl__eventtv_main` (
    `video_id` VARCHAR( 50 ) NOT NULL ,
    `doc_id` INT NULL ,
    PRIMARY KEY ( `video_id` )
    ) ENGINE = MYISAM ;");
    }

    public function AgencyRecreateView3()
    {
        SQLProvider::ExecuteNonReturnQuery("CREATE OR REPLACE VIEW `vw__agency_list3` AS
			SELECT
			  a.*,
        if(a.city>0, c.title, a.other_city) as city_title,
        date_format(a.registration_date,'%d.%m.%Y') AS formatted_date
			from  tbl__agency_doc a
      left join tbl__city c on c.tbl_obj_id = a.city
      where a.active=1
			");
    }

    public function ContractorRecreateView()
    {
        SQLProvider::ExecuteNonReturnQuery("CREATE OR REPLACE VIEW `vw__contractor_list` AS
			select
				`c`.`tbl_obj_id` AS `tbl_obj_id`,
				`c`.`title` AS `title`,
				`c`.`kind_of_activity` AS `kind_of_activity`,
				`c`.`short_description` AS `short_description`,
				`c`.`address` AS `address`,
				`c`.`phone` AS `phone`,
				`c`.`site_address` AS `site_address`,
				`c`.`email` AS `email`,
				`c`.`selection` AS `selection`,
				`c`.`logo_image` AS `logo_image`,
				`c`.`phone2` AS `phone2`,
				`c`.`city` AS `city`,
				`ct`.`title` AS `city_name`,
				group_concat(`a`.`title` separator ', ') AS `activity_title`,
				date_format(`c`.`registration_date`,_cp1251'%d.%m.%Y') AS `formatted_date`,
				ifnull(`c`.`points`,0) AS `points`,
				ifnull(`c`.`voted`,0) AS `voted`,
				if((ifnull(`c`.`visited`,0) > ifnull(`c`.`voted`,0)),ifnull(`c`.`visited`,0),ifnull(`c`.`voted`,0)) AS `visited`,
				`c`.`recommended` AS `recommended`,
				`c`.`registration_date` AS `registration_date`,
				`c`.`youtube_video` AS `youtube_video`
			from `tbl__contractor_doc` `c`
			left join `tbl__contractor2activity` `c2a` on `c`.`tbl_obj_id` = `c2a`.`tbl_obj_id`
			left join `tbl__activity_type` `a` on `a`.`tbl_obj_id` = `c2a`.`kind_of_activity`
			left join `tbl__city` `ct` on `c`.`city` = `ct`.`tbl_obj_id`
			where `c`.`active` = 1
			group by
				`c`.`tbl_obj_id`,
				`c`.`title`,
				`c`.`city`,
				`ct`.`title`,
				`c`.`kind_of_activity`,
				`c`.`short_description`,
				`c`.`address`,
				`c`.`phone`,
				`c`.`site_address`,
				`c`.`email`,
				`c`.`selection`,
				`c`.`logo_image`,
				`c`.`phone2`");
    }

    public function ArtistAreaCMSViews()
    {
        SQLProvider::ExecuteNonReturnQuery("
		  CREATE OR REPLACE VIEW `vw__artist_style_hiearchy` AS
			select tbl_obj_id, 0 parent_id, title
			from tbl__styles_groups
			union all
			select tbl_obj_id, style_group parent_id, title
			from tbl__styles
			");
        SQLProvider::ExecuteNonReturnQuery("
		  CREATE OR REPLACE VIEW `vw__area_types_hiearchy` AS
			select tbl_obj_id, 0 parent_id, title, priority
			from tbl__area_types
			union all
			select tbl_obj_id, parent_id, title, priority
			from tbl__area_subtypes
			");

    }

    public function PublicEventTvDates()
    {
        $this->ChangeColumn("tbl__public_doc", "registration_date", "registration_date", "DATETIME", null, false);
        $this->ChangeColumn("tbl__eventtv_doc", "registration_date", "registration_date", "DATETIME", null, false);
    }


    private function ColExists($table, $colname)
    {
        return sizeof(SQLProvider::ExecuteQuery("show COLUMNs from `$table` WHERE `FIELD`='$colname'")) > 0;
    }

    public function ColsTranslit()
    {
        if (!$this->ColExists('tbl__area_doc', 'title_url')) {
            SQLProvider::ExecuteNonReturnQuery("
				ALTER TABLE `tbl__area_doc`
				  ADD `title_url` VARCHAR( 50 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NULL AFTER `title`");
            SQLProvider::ExecuteNonReturnQuery("
			  ALTER TABLE `tbl__area_doc`
				  ADD UNIQUE area_title_url (`title_url`)");
        }
        if (!$this->ColExists('tbl__contractor_doc', 'title_url')) {
            SQLProvider::ExecuteNonReturnQuery("
				ALTER TABLE `tbl__contractor_doc`
				  ADD `title_url` VARCHAR( 50 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NULL AFTER `title`");
            SQLProvider::ExecuteNonReturnQuery("
			  ALTER TABLE `tbl__contractor_doc`
				  ADD UNIQUE contractor_title_url (`title_url`)");
        }
        if (!$this->ColExists('tbl__artist_doc', 'title_url')) {
            SQLProvider::ExecuteNonReturnQuery("
				ALTER TABLE `tbl__artist_doc`
				  ADD `title_url` VARCHAR( 50 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NULL AFTER `title`");
            SQLProvider::ExecuteNonReturnQuery("
			  ALTER TABLE `tbl__artist_doc`
				  ADD UNIQUE artist_title_url (`title_url`)");
        }
        if (!$this->ColExists('tbl__agency_doc', 'title_url')) {
            SQLProvider::ExecuteNonReturnQuery("
				ALTER TABLE `tbl__agency_doc`
				  ADD `title_url` VARCHAR( 50 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NULL AFTER `title`");
            SQLProvider::ExecuteNonReturnQuery("
			  ALTER TABLE `tbl__agency_doc`
				  ADD UNIQUE agency_title_url (`title_url`)");
        }

        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__area_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__area_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__area_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__area_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__contractor_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__contractor_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__contractor_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__contractor_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__artist_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__artist_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__artist_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__artist_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__agency_doc where title_url is null");
        foreach ($r as $key => $value) {
            $flag = true;
            $old_title = $value['title'];
            while ($flag) {
                $url = translitURL($value['title']);
                $r2 = SQLProvider::ExecuteQuery("
			    select tbl_obj_id from tbl__agency_doc
				  where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
                if (sizeof($r2)) {
                    $value['title'] .= " ";
                }
                else {
                    $flag = false;
                    if ($old_title != $value['title'])
                        SQLProvider::ExecuteNonReturnQuery("
				      update tbl__agency_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                }
            }
            $url = translitURL($value['title']);
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__agency_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        SQLProvider::ExecuteNonReturnQuery("
		  CREATE OR REPLACE VIEW `vw__all_users` as
			select
			  `tbl__registered_user`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__registered_user`.`title` AS `title`,
				 null `title_url` AS `title_url`,
				`tbl__registered_user`.`email` AS `email`,
				`tbl__registered_user`.`login` AS `login`,
				`tbl__registered_user`.`password` AS `password`,
				`tbl__registered_user`.`forum_name` AS `forum_name`,
				`tbl__registered_user`.`registration_date` AS `registration_date`,
				`tbl__registered_user`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__registered_user`.`active` AS `active`,
				`tbl__registered_user`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__registered_user`.`password_reset` AS `password_reset`,
				`tbl__registered_user`.`reset_expire` AS `reset_expire`,
				`tbl__registered_user`.`subscribe` AS `subscribe`,
				_cp1251'user' AS `login_type` from `tbl__registered_user`
      union all
			select
				`tbl__agency_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__agency_doc`.`title` AS `title`,
				`tbl__agency_doc`.`title_url` AS `title_url`,
				`tbl__agency_doc`.`email` AS `email`,
				`tbl__agency_doc`.`login` AS `login`,
				`tbl__agency_doc`.`password` AS `password`,
				`tbl__agency_doc`.`forum_name` AS `forum_name`,
				`tbl__agency_doc`.`registration_date` AS `registration_date`,
				`tbl__agency_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__agency_doc`.`active` AS `active`,
				`tbl__agency_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__agency_doc`.`password_reset` AS `password_reset`,
				`tbl__agency_doc`.`reset_expire` AS `reset_expire`,
				`tbl__agency_doc`.`subscribe` AS `subscribe`,
				_cp1251'agency' AS `login_type`
			from `tbl__agency_doc`
			union all
			select
			  `tbl__area_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__area_doc`.`title` AS `title`,
				`tbl__area_doc`.`title_url` AS `title_url`,
				`tbl__area_doc`.`email` AS `email`,
				`tbl__area_doc`.`login` AS `login`,
				`tbl__area_doc`.`password` AS `password`,
				`tbl__area_doc`.`forum_name` AS `forum_name`,
				`tbl__area_doc`.`registration_date` AS `registration_date`,
				`tbl__area_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__area_doc`.`active` AS `active`,
				`tbl__area_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__area_doc`.`password_reset` AS `password_reset`,
				`tbl__area_doc`.`reset_expire` AS `reset_expire`,
				`tbl__area_doc`.`subscribe` AS `subscribe`,
				_cp1251'area' AS `login_type`
			from `tbl__area_doc`
		  union all
			select
			  `tbl__contractor_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__contractor_doc`.`title` AS `title`,
				`tbl__contractor_doc`.`title_url` AS `title_url`,
				`tbl__contractor_doc`.`email` AS `email`,
				`tbl__contractor_doc`.`login` AS `login`,
				`tbl__contractor_doc`.`password` AS `password`,
				`tbl__contractor_doc`.`forum_name` AS `forum_name`,
				`tbl__contractor_doc`.`registration_date` AS `registration_date`,
				`tbl__contractor_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__contractor_doc`.`active` AS `active`,
				`tbl__contractor_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__contractor_doc`.`password_reset` AS `password_reset`,
				`tbl__contractor_doc`.`reset_expire` AS `reset_expire`,
				`tbl__contractor_doc`.`subscribe` AS `subscribe`,
				_cp1251'contractor' AS `login_type`
			from `tbl__contractor_doc`
			union all
			select
			  `tbl__artist_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__artist_doc`.`title` AS `title`,
				`tbl__artist_doc`.`title_url` AS `title_url`,
				`tbl__artist_doc`.`email` AS `email`,
				`tbl__artist_doc`.`login` AS `login`,
				`tbl__artist_doc`.`password` AS `password`,
				`tbl__artist_doc`.`forum_name` AS `forum_name`,
				`tbl__artist_doc`.`registration_date` AS `registration_date`,
				`tbl__artist_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__artist_doc`.`active` AS `active`,
				`tbl__artist_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__artist_doc`.`password_reset` AS `password_reset`,
				`tbl__artist_doc`.`reset_expire` AS `reset_expire`,
				`tbl__artist_doc`.`subscribe` AS `subscribe`,
				_cp1251'artist' AS `login_type` from `tbl__artist_doc`");

        SQLProvider::ExecuteNonReturnQuery("
		  CREATE OR REPLACE VIEW `vw__all_users_full` as
		  select
			  `tbl__registered_user`.`tbl_obj_id` AS `user_id`,
			  if(`tbl__registered_user`.`nikname` is not null and `tbl__registered_user`.`nikname` <> _cp1251'',
				  `tbl__registered_user`.`nikname`,
				  `tbl__registered_user`.`title`
				) AS `title`,
				`tbl__registered_user`.`logo` AS `logo`,
				`tbl__registered_user`.`comments_count` AS `comments_count`,
				`tbl__registered_user`.`comments_ban` AS `comments_ban`,
				_cp1251'user' AS `type`,
				concat(_cp1251'user',`tbl__registered_user`.`tbl_obj_id`) AS `user_key`,
				null title_url
			from `tbl__registered_user`
			union all
			select
			  `tbl__area_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__area_doc`.`title` AS `title`,
				`tbl__area_doc`.`logo` AS `logo`,
				`tbl__area_doc`.`comments_count` AS `comments_count`,
				`tbl__area_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'area' AS `area`,
				concat(_cp1251'area',`tbl__area_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__area_doc`.`title_url`
			from `tbl__area_doc`
			union all
			select
			  `tbl__artist_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__artist_doc`.`title` AS `title`,
				`tbl__artist_doc`.`logo` AS `logo`,
				`tbl__artist_doc`.`comments_count` AS `comments_count`,
				`tbl__artist_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'artist' AS `artist`,
				concat(_cp1251'artist',`tbl__artist_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__artist_doc`.`title_url`
			from `tbl__artist_doc`
			union all
			select
			  `tbl__agency_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__agency_doc`.`title` AS `title`,
				`tbl__agency_doc`.`logo_image` AS `logo_image`,
				`tbl__agency_doc`.`comments_count` AS `comments_count`,
				`tbl__agency_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'agency' AS `agency`,
				concat(_cp1251'agency',`tbl__agency_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__agency_doc`.`title_url`
			from `tbl__agency_doc`
			union all
			select
			  `tbl__contractor_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__contractor_doc`.`title` AS `title`,
				`tbl__contractor_doc`.`logo_image` AS `logo_image`,
				`tbl__contractor_doc`.`comments_count` AS `comments_count`,
				`tbl__contractor_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'contractor' AS `contractor`,
				concat(_cp1251'contractor',`tbl__contractor_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__contractor_doc`.`title_url`
			from `tbl__contractor_doc`");

        SQLProvider::ExecuteNonReturnQuery("CREATE OR REPLACE VIEW `vw__agency_list3` AS
			SELECT
			  a.*,
        if(a.city>0, c.title, a.other_city) as city_title,
        date_format(a.registration_date,'%d.%m.%Y') AS formatted_date
			from  tbl__agency_doc a
      left join tbl__city c on c.tbl_obj_id = a.city
      where a.active=1
			");
        SQLProvider::ExecuteNonReturnQuery("CREATE OR REPLACE VIEW `vw__contractor_list` AS
			select
				`c`.`tbl_obj_id` AS `tbl_obj_id`,
				`c`.`title` AS `title`,
				`c`.`title_url`,
				`c`.`kind_of_activity` AS `kind_of_activity`,
				`c`.`short_description` AS `short_description`,
				`c`.`address` AS `address`,
				`c`.`phone` AS `phone`,
				`c`.`site_address` AS `site_address`,
				`c`.`email` AS `email`,
				`c`.`selection` AS `selection`,
				`c`.`logo_image` AS `logo_image`,
				`c`.`phone2` AS `phone2`,
				`c`.`city` AS `city`,
				`ct`.`title` AS `city_name`,
				group_concat(`a`.`title` separator ', ') AS `activity_title`,
				date_format(`c`.`registration_date`,_cp1251'%d.%m.%Y') AS `formatted_date`,
				ifnull(`c`.`points`,0) AS `points`,
				ifnull(`c`.`voted`,0) AS `voted`,
				if((ifnull(`c`.`visited`,0) > ifnull(`c`.`voted`,0)),ifnull(`c`.`visited`,0),ifnull(`c`.`voted`,0)) AS `visited`,
				`c`.`recommended` AS `recommended`,
				`c`.`registration_date` AS `registration_date`,
				`c`.`youtube_video` AS `youtube_video`
			from `tbl__contractor_doc` `c`
			left join `tbl__contractor2activity` `c2a` on `c`.`tbl_obj_id` = `c2a`.`tbl_obj_id`
			left join `tbl__activity_type` `a` on `a`.`tbl_obj_id` = `c2a`.`kind_of_activity`
			left join `tbl__city` `ct` on `c`.`city` = `ct`.`tbl_obj_id`
			where `c`.`active` = 1
			group by
				`c`.`tbl_obj_id`,
				`c`.`title`,
				`c`.`title_url`,
				`c`.`city`,
				`ct`.`title`,
				`c`.`kind_of_activity`,
				`c`.`short_description`,
				`c`.`address`,
				`c`.`phone`,
				`c`.`site_address`,
				`c`.`email`,
				`c`.`selection`,
				`c`.`logo_image`,
				`c`.`phone2`");

        SQLProvider::ExecuteNonReturnQuery("
			CREATE OR REPLACE VIEW `vw__area_list2` as
			SELECT a.*, h.sum_places_banquet
			FROM `tbl__area_doc` a
			left join vw__area_halls h on h.area_id = a.tbl_obj_id
			where a.active =1");
        SQLProvider::ExecuteNonReturnQuery("update tbl__event_calendar set link = replace(link,'/news/details/id/','/news/details')");
    }

    public function ResidentsRegDate()
    {
        SQLProvider::ExecuteNonReturnQuery("
		  ALTER TABLE `tbl__area_doc` CHANGE `registration_date` `registration_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        SQLProvider::ExecuteNonReturnQuery("
		  ALTER TABLE `tbl__artist_doc` CHANGE `registration_date` `registration_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        SQLProvider::ExecuteNonReturnQuery("
		  ALTER TABLE `tbl__agency_doc` CHANGE `registration_date` `registration_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        SQLProvider::ExecuteNonReturnQuery("
		  ALTER TABLE `tbl__contractor_doc` CHANGE `registration_date` `registration_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }

    public function EventTVNewRubrics()
    {
        $this->AddColumn('tbl__eventtv_topics', 'group_num', 'int', 2, false, 1);
    }

    public function ResidenNullFields()
    {
        $this->ChangeColumn('tbl__agency_doc', 'kind_of_activity', 'kind_of_activity', 'int', 5, true);
        $this->ChangeColumn('tbl__agency_doc', 'forum_name', 'forum_name', 'varchar', 70, true);
        $this->ChangeColumn('tbl__agency_doc', 'artists', 'artists', 'varchar', 2, true);
        $this->ChangeColumn('tbl__agency_doc', 'selection', 'selection', 'varchar', 2, true);
        $this->ChangeColumn('tbl__artist_doc', 'group', 'group', 'int', 5, true);
        $this->ChangeColumn('tbl__artist_doc', 'title', 'title', 'varchar', 54, false);
        $this->ChangeColumn('tbl__artist_doc', 'login', 'login', 'varchar', 56, false);
        $this->ChangeColumn('tbl__area_doc', 'rent', 'rent', 'int', 4, true);
        $this->ChangeColumn('tbl__area_doc', 'dancing', 'dancing', 'int', 4, true);
        $this->ChangeColumn('tbl__area_doc', 'date_open', 'date_open', 'timestamp', null, true);
        $this->ChangeColumn('tbl__area_doc', 'login', 'login', 'varchar', 30, false);
        $this->ChangeColumn('tbl__contractor_doc', 'selection', 'selection', 'varchar', 13, true);
    }

    public function NewCMSUser()
    {
        $res = SQLProvider::ExecuteQuery("select 1 from dir_admins where username = 'volalex'");
		if($res==null){
            SQLProvider::ExecuteNonReturnQuery("
			  insert into dir_admins(username, password, submit_notif, admtype)
				values('volalex',MD5('123root321'),'1',1)");
	    }
    }

    public function AgencyTextType()
    {
        $this->AddColumn('tbl__agency_type', 'description', 'text');
        $this->AddColumn('tbl__agency_type', 'seo_text_caption', 'varchar', 128);
        $this->AddColumn('tbl__agency_type', 'seo_text', 'text');
    }

    public function FirstResident()
    {
        $this->AddColumn("tbl__activity_type", "first_id", "int");
        $this->AddColumn("tbl__agency_type", "first_id", "int");
        $this->AddColumn("tbl__area_types", "first_id", "int");
        $this->AddColumn("tbl__area_subtypes", "first_id", "int");
        $this->AddColumn("tbl__artist_group", "first_id", "int");
        $this->AddColumn("tbl__artist_subgroup", "first_id", "int");

        SQLProvider::ExecuteNonReturnQuery(
            "CREATE OR REPLACE VIEW `vw__contractor2activity` AS
      select r.tbl_obj_id, r.title, l.kind_of_activity
      from tbl__contractor_doc r
      join tbl__contractor2activity l on l.tbl_obj_id = r.tbl_obj_id
      where r.active=1
      union all
      select distinct r.tbl_obj_id, r.title, t.parent_id
      from tbl__contractor_doc r
      join tbl__contractor2activity l on l.tbl_obj_id = r.tbl_obj_id
      join tbl__activity_type t on t.tbl_obj_id = l.kind_of_activity
      where t.parent_id > 0
        and r.active=1");
        SQLProvider::ExecuteNonReturnQuery(
            "CREATE OR REPLACE VIEW `vw__agency2activity` AS
      select r.tbl_obj_id, r.title, l.kind_of_activity
      from tbl__agency_doc r
      join tbl__agency2activity l on l.tbl_obj_id = r.tbl_obj_id
      where r.active=1");
        SQLProvider::ExecuteNonReturnQuery(
            "CREATE OR REPLACE VIEW `vw__area2subtype` AS
      select r.tbl_obj_id, r.title, l.subtype_id
      from tbl__area_doc r
      join tbl__area2subtype l on l.area_id = r.tbl_obj_id
      where r.active=1");
        SQLProvider::ExecuteNonReturnQuery(
            "CREATE OR REPLACE VIEW `vw__area2type` AS
      select distinct r.tbl_obj_id, r.title, s.parent_id type_id
      from tbl__area_doc r
      join tbl__area2subtype l on l.area_id = r.tbl_obj_id
      join tbl__area_subtypes s on s.tbl_obj_id = l.subtype_id
      where r.active=1");
        SQLProvider::ExecuteNonReturnQuery(
            "CREATE OR REPLACE VIEW `vw__artist2subgroup` AS
      select r.tbl_obj_id, r.title, l.subgroup_id
      from tbl__artist_doc r
      join tbl__artist2subgroup l on l.artist_id = r.tbl_obj_id
      where r.active=1");
        SQLProvider::ExecuteNonReturnQuery(
            "CREATE OR REPLACE VIEW `vw__artist2group` AS
      select distinct r.tbl_obj_id, r.title, s.parent_id group_id
      from tbl__artist_doc r
      join tbl__artist2subgroup l on l.artist_id = r.tbl_obj_id
      join tbl__artist_subgroup s on s.tbl_obj_id = l.subgroup_id
      where r.active=1");

    }

    public function fixTranslit(){
        $tables = array("tbl__artist_group","tbl__artist_subgroup","tbl__area_types","tbl__area_subtypes",
            "tbl__agency_type","tbl__activity_type");
        foreach($tables as $value){
            $r = SQLProvider::ExecuteQuery("select tbl_obj_id,title from ".$value);
            foreach ($r as $record) {
                $url = translitURL($record['title']);
                SQLProvider::ExecuteNonReturnQuery("update ".$value." set title_url='".
                mysql_real_escape_string($url)."' where tbl_obj_id = ".$record['tbl_obj_id']);
            }
        }
        
    
    }

    public function ArtistGroupTitleUrl()
    {
        $this->AddColumn("tbl__artist_group", "title_url", "VARCHAR", 255);
        $this->AddColumn("tbl__artist_subgroup", "title_url", "VARCHAR", 255);
        $this->AddColumn("tbl__area_types", "title_url", "VARCHAR", 255);
        $this->AddColumn("tbl__area_subtypes", "title_url", "VARCHAR", 255);
        $this->AddColumn("tbl__agency_type", "title_url", "VARCHAR", 255);
        $this->AddColumn("tbl__activity_type", "title_url", "VARCHAR", 255);
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__artist_group where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("select tbl_obj_id from tbl__artist_group
			where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("update tbl__artist_group set title = '" . mysql_real_escape_string($value['title']) . "'
                where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("update tbl__artist_group set title_url = '" .
                mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__artist_subgroup where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__artist_subgroup
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__artist_subgroup set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__artist_subgroup set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        //Area types translit
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__area_types where title_url is null");
        foreach ($r as $key => $value) {
                $url = translitURL($value['title']);
                $r2 = SQLProvider::ExecuteQuery("select tbl_obj_id from tbl__area_types
			where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
                if (sizeof($r2)) {
                    $value['title'] .= " ";
                    SQLProvider::ExecuteNonReturnQuery("update tbl__area_types set title = '" . mysql_real_escape_string($value['title']) . "' 				where tbl_obj_id = " . $value['tbl_obj_id']);
                    $url = translitURL($value['title']);
                }
                SQLProvider::ExecuteNonReturnQuery("update tbl__area_types set title_url = '" .
                    mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
            }
            //Area subtypes translit
            $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__area_subtypes where title_url is null");
            foreach ($r as $key => $value) {
                $url = translitURL($value['title']);
                $r2 = SQLProvider::ExecuteQuery("select tbl_obj_id from tbl__area_subtypes
			where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
                if (sizeof($r2)) {
                    $value['title'] .= " ";
                    SQLProvider::ExecuteNonReturnQuery("update tbl__area_subtypes set title = '" . mysql_real_escape_string($value['title']) . "' 				where tbl_obj_id = " . $value['tbl_obj_id']);
                    $url = translitURL($value['title']);
                }
                SQLProvider::ExecuteNonReturnQuery("update tbl__area_subtypes set title_url = '" .
                    mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
            }
            //Agency tbl__agency_type
            $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__agency_type where title_url is null");
            foreach ($r as $key => $value) {
                $url = translitURL($value['title']);
                $r2 = SQLProvider::ExecuteQuery("select tbl_obj_id from tbl__agency_type
			where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
                if (sizeof($r2)) {
                    $value['title'] .= " ";
                    SQLProvider::ExecuteNonReturnQuery("update tbl__agency_type set title = '" . mysql_real_escape_string($value['title']) . "' 				where tbl_obj_id = " . $value['tbl_obj_id']);
                    $url = translitURL($value['title']);
                }
                SQLProvider::ExecuteNonReturnQuery("update tbl__agency_type set title_url = '" .
                    mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
            }
            //Contractor tbl__activity_type
            $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__activity_type where title_url is null");
            foreach ($r as $key => $value) {
                $url = translitURL($value['title']);
                $r2 = SQLProvider::ExecuteQuery("select tbl_obj_id from tbl__activity_type
			where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
                if (sizeof($r2)) {
                    $value['title'] .= " ";
                    SQLProvider::ExecuteNonReturnQuery("update tbl__activity_type set title = '" . mysql_real_escape_string($value['title']) . "' 				where tbl_obj_id = " . $value['tbl_obj_id']);
                    $url = translitURL($value['title']);
                }
                SQLProvider::ExecuteNonReturnQuery("update tbl__activity_type set title_url = '" .
                    mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
            }
        }

    public function refillTranslit()
    {
        SQLProvider::ExecuteNonReturnQuery("update tbl__area_doc set title_url = null;");
        SQLProvider::ExecuteNonReturnQuery("update tbl__contractor_doc set title_url = null;");
        SQLProvider::ExecuteNonReturnQuery("update tbl__artist_doc set title_url = null;");
        SQLProvider::ExecuteNonReturnQuery("update tbl__agency_doc set title_url = null;");

        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__area_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__area_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= "_";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__area_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__area_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__contractor_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__contractor_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= "_";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__contractor_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__contractor_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__artist_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__artist_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= "_";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__artist_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__artist_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__agency_doc where title_url is null");
        foreach ($r as $key => $value) {
            $flag = true;
            $old_title = $value['title'];
            while ($flag) {
                $url = translitURL($value['title']);
                $r2 = SQLProvider::ExecuteQuery("
			    select tbl_obj_id from tbl__agency_doc
				  where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
                if (sizeof($r2)) {
                    $value['title'] .= "_";
                }
                else {
                    $flag = false;
                    if ($old_title != $value['title'])
                        SQLProvider::ExecuteNonReturnQuery("
				      update tbl__agency_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                }
            }
            $url = translitURL($value['title']);
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__agency_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
    }

    public function AddArtistColumns()
    {
        $this->AddColumn('tbl__artist_doc', 'price_from', 'varchar', 50);
        $this->AddColumn('tbl__artist_doc', 'price_to', 'varchar', 50);
        $this->AddColumn('tbl__artist_doc', 'rider', 'INT');
    }


    public function AddEventTvTitleUrl()
    {
        //Add column
        $this->AddColumn("tbl__eventtv_doc", "title_url", "varchar", 51);
        //Select all nulls
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__eventtv_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__eventtv_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__eventtv_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__eventtv_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        //ReCreate view
        SQLProvider::ExecuteNonReturnQuery("
    CREATE OR REPLACE VIEW `vw__eventtv_doc` AS
    select
      `pd`.`tbl_obj_id` AS `tbl_obj_id`,
      `pd`.`title` AS `title`,
      `pd`.`title_url` AS `title_url`,
      `pd`.`logo_image` AS `logo_image`,
      `pd`.`annotation` AS `annotation`,
      `pd`.`is_new` AS `is_new`,
      `p2t`.`parent_id` AS `dir_id`,
      `pd`.`registration_date` AS `registration_date`
      from `tbl__eventtv_doc` `pd`
        join `tbl__eventtv2topic` `p2t` on `pd`.`tbl_obj_id` = `p2t`.`child_id`
      where `pd`.`active` = 1 order by `pd`.`registration_date` desc;
    ");
    }

    public function AddBookTitleUrl()
    {
        $this->AddColumn("tbl__public_doc", "title_url", "varchar", 51);
        //Select all nulls
        $r = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__public_doc where title_url is null");
        foreach ($r as $key => $value) {
            $url = translitURL($value['title']);
            $r2 = SQLProvider::ExecuteQuery("
			  select tbl_obj_id from tbl__public_doc
				where title_url = '" . mysql_real_escape_string($url) . "' and tbl_obj_id != " . $value['tbl_obj_id']);
            if (sizeof($r2)) {
                $value['title'] .= " ";
                SQLProvider::ExecuteNonReturnQuery("
				  update tbl__public_doc set title = '" . mysql_real_escape_string($value['title']) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
                $url = translitURL($value['title']);
            }
            SQLProvider::ExecuteNonReturnQuery("
			  update tbl__public_doc set title_url = '" . mysql_real_escape_string($url) . "' where tbl_obj_id = " . $value['tbl_obj_id']);
        }
        SQLProvider::ExecuteNonReturnQuery("
    CREATE OR REPLACE VIEW `vw__public_doc` AS
    select
      `pd`.`tbl_obj_id` AS `tbl_obj_id`,
      `pd`.`title` AS `title`,
      `pd`.`title_url` AS `title_url`,
      `pd`.`logo_image` AS `logo_image`,
      `pd`.`annotation` AS `annotation`,
      `pd`.`is_new` AS `is_new`,
      `p2t`.`parent_id` AS `dir_id`,
      `pd`.`registration_date` AS `registration_date`
      from `tbl__public_doc` `pd`
        join `tbl__public2topic` `p2t` on `pd`.`tbl_obj_id` = `p2t`.`child_id`
      where `pd`.`active` = 1 order by `pd`.`registration_date` desc;
    ");

    }

    public function recreateAllusersView()
    {
        SQLProvider::ExecuteNonReturnQuery("
		  CREATE OR REPLACE VIEW `vw__all_users` as
			select
			  `tbl__registered_user`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__registered_user`.`title` AS `title`,
				 null AS `title_url`,
				`tbl__registered_user`.`email` AS `email`,
				`tbl__registered_user`.`login` AS `login`,
				`tbl__registered_user`.`password` AS `password`,
				`tbl__registered_user`.`forum_name` AS `forum_name`,
				`tbl__registered_user`.`registration_date` AS `registration_date`,
				`tbl__registered_user`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__registered_user`.`active` AS `active`,
				`tbl__registered_user`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__registered_user`.`password_reset` AS `password_reset`,
				`tbl__registered_user`.`reset_expire` AS `reset_expire`,
				`tbl__registered_user`.`subscribe` AS `subscribe`,
				_cp1251'user' AS `login_type` from `tbl__registered_user`
      union all
			select
				`tbl__agency_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__agency_doc`.`title` AS `title`,
				`tbl__agency_doc`.`title_url` AS `title_url`,
				`tbl__agency_doc`.`email` AS `email`,
				`tbl__agency_doc`.`login` AS `login`,
				`tbl__agency_doc`.`password` AS `password`,
				`tbl__agency_doc`.`forum_name` AS `forum_name`,
				`tbl__agency_doc`.`registration_date` AS `registration_date`,
				`tbl__agency_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__agency_doc`.`active` AS `active`,
				`tbl__agency_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__agency_doc`.`password_reset` AS `password_reset`,
				`tbl__agency_doc`.`reset_expire` AS `reset_expire`,
				`tbl__agency_doc`.`subscribe` AS `subscribe`,
				_cp1251'agency' AS `login_type`
			from `tbl__agency_doc`
			union all
			select
			  `tbl__area_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__area_doc`.`title` AS `title`,
				`tbl__area_doc`.`title_url` AS `title_url`,
				`tbl__area_doc`.`email` AS `email`,
				`tbl__area_doc`.`login` AS `login`,
				`tbl__area_doc`.`password` AS `password`,
				`tbl__area_doc`.`forum_name` AS `forum_name`,
				`tbl__area_doc`.`registration_date` AS `registration_date`,
				`tbl__area_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__area_doc`.`active` AS `active`,
				`tbl__area_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__area_doc`.`password_reset` AS `password_reset`,
				`tbl__area_doc`.`reset_expire` AS `reset_expire`,
				`tbl__area_doc`.`subscribe` AS `subscribe`,
				_cp1251'area' AS `login_type`
			from `tbl__area_doc`
		  union all
			select
			  `tbl__contractor_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__contractor_doc`.`title` AS `title`,
				`tbl__contractor_doc`.`title_url` AS `title_url`,
				`tbl__contractor_doc`.`email` AS `email`,
				`tbl__contractor_doc`.`login` AS `login`,
				`tbl__contractor_doc`.`password` AS `password`,
				`tbl__contractor_doc`.`forum_name` AS `forum_name`,
				`tbl__contractor_doc`.`registration_date` AS `registration_date`,
				`tbl__contractor_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__contractor_doc`.`active` AS `active`,
				`tbl__contractor_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__contractor_doc`.`password_reset` AS `password_reset`,
				`tbl__contractor_doc`.`reset_expire` AS `reset_expire`,
				`tbl__contractor_doc`.`subscribe` AS `subscribe`,
				_cp1251'contractor' AS `login_type`
			from `tbl__contractor_doc`
			union all
			select
			  `tbl__artist_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__artist_doc`.`title` AS `title`,
				`tbl__artist_doc`.`title_url` AS `title_url`,
				`tbl__artist_doc`.`email` AS `email`,
				`tbl__artist_doc`.`login` AS `login`,
				`tbl__artist_doc`.`password` AS `password`,
				`tbl__artist_doc`.`forum_name` AS `forum_name`,
				`tbl__artist_doc`.`registration_date` AS `registration_date`,
				`tbl__artist_doc`.`registration_confirmed` AS `registration_confirmed`,
				`tbl__artist_doc`.`active` AS `active`,
				`tbl__artist_doc`.`registration_confirm_code` AS `registration_confirm_code`,
				`tbl__artist_doc`.`password_reset` AS `password_reset`,
				`tbl__artist_doc`.`reset_expire` AS `reset_expire`,
				`tbl__artist_doc`.`subscribe` AS `subscribe`,
				_cp1251'artist' AS `login_type` from `tbl__artist_doc`");

        SQLProvider::ExecuteNonReturnQuery("
		  CREATE OR REPLACE VIEW `vw__all_users_full` as
		  select
			  `tbl__registered_user`.`tbl_obj_id` AS `user_id`,
			  if(`tbl__registered_user`.`nikname` is not null and `tbl__registered_user`.`nikname` <> _cp1251'',
				  `tbl__registered_user`.`nikname`,
				  `tbl__registered_user`.`title`
				) AS `title`,
				`tbl__registered_user`.`logo` AS `logo`,
				`tbl__registered_user`.`comments_count` AS `comments_count`,
				`tbl__registered_user`.`comments_ban` AS `comments_ban`,
				_cp1251'user' AS `type`,
				concat(_cp1251'user',`tbl__registered_user`.`tbl_obj_id`) AS `user_key`,
				null title_url
			from `tbl__registered_user`
			union all
			select
			  `tbl__area_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__area_doc`.`title` AS `title`,
				`tbl__area_doc`.`logo` AS `logo`,
				`tbl__area_doc`.`comments_count` AS `comments_count`,
				`tbl__area_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'area' AS `area`,
				concat(_cp1251'area',`tbl__area_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__area_doc`.`title_url`
			from `tbl__area_doc`
			union all
			select
			  `tbl__artist_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__artist_doc`.`title` AS `title`,
				`tbl__artist_doc`.`logo` AS `logo`,
				`tbl__artist_doc`.`comments_count` AS `comments_count`,
				`tbl__artist_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'artist' AS `artist`,
				concat(_cp1251'artist',`tbl__artist_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__artist_doc`.`title_url`
			from `tbl__artist_doc`
			union all
			select
			  `tbl__agency_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__agency_doc`.`title` AS `title`,
				`tbl__agency_doc`.`logo_image` AS `logo_image`,
				`tbl__agency_doc`.`comments_count` AS `comments_count`,
				`tbl__agency_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'agency' AS `agency`,
				concat(_cp1251'agency',`tbl__agency_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__agency_doc`.`title_url`
			from `tbl__agency_doc`
			union all
			select
			  `tbl__contractor_doc`.`tbl_obj_id` AS `tbl_obj_id`,
				`tbl__contractor_doc`.`title` AS `title`,
				`tbl__contractor_doc`.`logo_image` AS `logo_image`,
				`tbl__contractor_doc`.`comments_count` AS `comments_count`,
				`tbl__contractor_doc`.`comments_ban` AS `comments_ban`,
				_cp1251'contractor' AS `contractor`,
				concat(_cp1251'contractor',`tbl__contractor_doc`.`tbl_obj_id`) AS `user_key`,
				`tbl__contractor_doc`.`title_url`
			from `tbl__contractor_doc`");

        SQLProvider::ExecuteNonReturnQuery("CREATE OR REPLACE VIEW `vw__agency_list3` AS
			SELECT
			  a.*,
        if(a.city>0, c.title, a.other_city) as city_title,
        date_format(a.registration_date,'%d.%m.%Y') AS formatted_date
			from  tbl__agency_doc a
      left join tbl__city c on c.tbl_obj_id = a.city
      where a.active=1
			");
        SQLProvider::ExecuteNonReturnQuery("CREATE OR REPLACE VIEW `vw__contractor_list` AS
			select
				`c`.`tbl_obj_id` AS `tbl_obj_id`,
				`c`.`title` AS `title`,
				`c`.`title_url`,
				`c`.`kind_of_activity` AS `kind_of_activity`,
				`c`.`short_description` AS `short_description`,
				`c`.`address` AS `address`,
				`c`.`phone` AS `phone`,
				`c`.`site_address` AS `site_address`,
				`c`.`email` AS `email`,
				`c`.`selection` AS `selection`,
				`c`.`logo_image` AS `logo_image`,
				`c`.`phone2` AS `phone2`,
				`c`.`city` AS `city`,
				`ct`.`title` AS `city_name`,
				date_format(`c`.`registration_date`,_cp1251'%d.%m.%Y') AS `formatted_date`,
				ifnull(`c`.`points`,0) AS `points`,
				ifnull(`c`.`voted`,0) AS `voted`,
				if((ifnull(`c`.`visited`,0) > ifnull(`c`.`voted`,0)),ifnull(`c`.`visited`,0),ifnull(`c`.`voted`,0)) AS `visited`,
				`c`.`recommended` AS `recommended`,
				`c`.`registration_date` AS `registration_date`,
				`c`.`youtube_video` AS `youtube_video`
			from `tbl__contractor_doc` `c`
			left join `tbl__contractor2activity` `c2a` on `c`.`tbl_obj_id` = `c2a`.`tbl_obj_id`
			left join `tbl__city` `ct` on `c`.`city` = `ct`.`tbl_obj_id`
			where `c`.`active` = 1
			group by
				`c`.`tbl_obj_id`,
				`c`.`title`,
				`c`.`title_url`,
				`c`.`city`,
				`ct`.`title`,
				`c`.`kind_of_activity`,
				`c`.`short_description`,
				`c`.`address`,
				`c`.`phone`,
				`c`.`site_address`,
				`c`.`email`,
				`c`.`selection`,
				`c`.`logo_image`,
				`c`.`phone2`");
    }


    public function AgencyPageTitle()
    {
        $this->AddColumn('tbl__agency_type', 'page_title', 'varchar', 255);
    }

    public function AddSeoFields()
    {
        $this->AddColumn('tbl__activity_type', 'seo_text', 'text');
        $this->AddColumn('tbl__area_subtypes', 'seo_text', 'text');
        $this->AddColumn('tbl__artist_subgroup', 'seo_text', 'text');
        
    }
	
	public function MergeFixes()
	{
		$this->AddColumn('tbl__news','in_calendar','tinyint',1,true,0);
        $this->AddColumn('tbl__news','type','varchar',255,false);
        $this->AddColumn('tbl__news','place','varchar',255,false);
		$this->AddColumn('tbl__news','site','varchar',255,false);
        $this->AddColumn("tbl__news","area_id","int",7,false,0);
		$this->AddColumn("tbl__resident_news","logo_image","varchar",255);
		$this->AddColumn("tbl__news","city","int",7,false,0);
        $this->AddColumn("tbl__news_dir","sort","int",11,false,0);
	}
		
		public function AddProAccount()
    {
        SQLProvider::ExecuteNonReturnQuery("
      CREATE TABLE IF NOT EXISTS tbl__pro_accounts(
      tbl_obj_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      to_resident_id INT NOT NULL,
      to_resident_type VARCHAR(32) NOT NULL,
      pro_type INT NOT NULL DEFAULT 1,
      date_end DATE NOT NULL,
			cost FLOAT NULL,
			period INT NULL,
      payed INT NOT NULL DEFAULT 0,
			date_pay DATE NULL
      ) ENGINE = MYISAM ;");
        $this->AddIndex('tbl__pro_accounts', 'tbl__pro_accounts_idx1', '`to_resident_type` , `to_resident_id`');
				$this->AddUniqueIndex('tbl__pro_accounts', 'tbl__pro_accounts_idx2', '`to_resident_type` , `to_resident_id`, `date_end`');
				$this->AddColumn('tbl__pro_accounts','cost','float');
				$this->AddColumn('tbl__pro_accounts','period','int');
				$this->AddColumn('tbl__pro_accounts','date_pay','date');
		  
			  SQLProvider::ExecuteNonReturnQuery(
						"create or replace view vw__pro_account_active as
						select to_resident_id, to_resident_type, min(date_end) date_end
						from tbl__pro_accounts
						where payed = 1
							and date_end >= CURRENT_DATE()
						group by to_resident_id, to_resident_type");
				SQLProvider::ExecuteNonReturnQuery(
						"create or replace view vw__pro_account as
						select p.*
						from tbl__pro_accounts p
						join vw__pro_account_active pa on pa.to_resident_type = p.to_resident_type
																												 and pa.to_resident_id = p.to_resident_id
																												 and pa.date_end = p.date_end
						where p.payed = 1
							and p.date_end >= CURRENT_DATE()");
				SQLProvider::ExecuteNonReturnQuery(
						"create or replace view vw__contractor_list_pro as
						select
								r.tbl_obj_id,
								r.title,
								r.title_url,
								r.kind_of_activity,
								r.short_description,
								r.address,
								r.phone,
								r.site_address,
								r.email,
								r.selection,
								r.logo_image,
								r.phone2,
								r.city,
								ct.title AS city_name,
								date_format(r.registration_date,_cp1251'%d.%m.%Y') AS formatted_date,
								ifnull(r.points,0) AS points,
								ifnull(r.voted,0) AS voted,
								if((ifnull(r.visited,0) > ifnull(r.voted,0)),ifnull(r.visited,0),ifnull(r.voted,0)) AS visited,
								r.recommended,
								r.registration_date,
								r.priority,
								r.youtube_video,
								pa.pro_type,
								pa.date_end AS pro_date_end,
								pa.cost AS pro_cost,
								pa.period AS pro_period,
								pa.date_pay AS pro_date_pay
						from tbl__contractor_doc r
						left join tbl__city ct on r.city = ct.tbl_obj_id
						left join vw__pro_account pa on pa.to_resident_type = 'contractor' and pa.to_resident_id = r.tbl_obj_id
						where r.active = 1");
				SQLProvider::ExecuteNonReturnQuery(
						"create or replace view vw__artist_list_pro as
						select
								r.*,
								pa.pro_type,
								pa.date_end AS pro_date_end,
								pa.cost AS pro_cost,
								pa.period AS pro_period,
								pa.date_pay AS pro_date_pay
						from tbl__artist_doc r
						left join vw__pro_account pa on pa.to_resident_type = 'artist' and pa.to_resident_id = r.tbl_obj_id
						where r.active = 1");
				SQLProvider::ExecuteNonReturnQuery(
						"create or replace view vw__agency_list_pro as
						select
								r.*,
								if(r.city>0,c.title,r.other_city) AS city_title,
								date_format(r.registration_date,'%d.%m.%Y') AS formatted_date,
								pa.pro_type,
								pa.date_end AS pro_date_end,
								pa.cost AS pro_cost,
								pa.period AS pro_period,
								pa.date_pay AS pro_date_pay
						from tbl__agency_doc r
						left join tbl__city c on c.tbl_obj_id = r.city
						left join vw__pro_account pa on pa.to_resident_type = 'agency' and to_resident_id = r.tbl_obj_id
						where r.active = 1");
				SQLProvider::ExecuteNonReturnQuery(
						"create or replace view vw__area_list_pro as
						select
								r.*,
								if(r.city>0,c.title,r.other_city) AS city_title,
								date_format(r.registration_date,'%d.%m.%Y') AS formatted_date,
								h.sum_places_banquet,
								pa.pro_type,
								pa.date_end AS pro_date_end,
								pa.cost AS pro_cost,
								pa.period AS pro_period,
								pa.date_pay AS pro_date_pay
						from tbl__area_doc r
						left join tbl__city c on c.tbl_obj_id = r.city
						left join vw__area_halls h on h.area_id = r.tbl_obj_id
						left join vw__pro_account pa on pa.to_resident_type = 'area' and to_resident_id = r.tbl_obj_id
						where r.active = 1");
    }
}

?>
