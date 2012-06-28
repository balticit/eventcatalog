CREATE VIEW `vw__public_doc` AS 
  select 
    `pd`.`tbl_obj_id` AS `tbl_obj_id`,
    `pd`.`title` AS `title`,
    `p2t`.`parent_id` AS `dir_id` 
  from 
    (`tbl__public_doc` `pd` join `tbl__public2topic` `p2t` on((`pd`.`tbl_obj_id` = `p2t`.`child_id`)));

CREATE VIEW `vw__personal_types_parent` AS 
  select 
    `tbl__personal_types`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__personal_types`.`title` AS `title` 
  from 
    `tbl__personal_types` 
  where 
    (`tbl__personal_types`.`parent_id` = 0);

CREATE VIEW `vw__personal_types` AS 
  select 
    `tbl__personal_types`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__personal_types`.`title` AS `title` 
  from 
    `tbl__personal_types` 
  where 
    (`tbl__personal_types`.`parent_id` <> 0);

CREATE VIEW `vw__news_soon` AS 
  select 
    `tbl__news`.`tbl_cai_id` AS `tbl_cai_id`,
    `tbl__news`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__news`.`title` AS `title`,
    `tbl__news`.`date` AS `date`,
    `tbl__news`.`s_image` AS `s_image`,
    `tbl__news`.`annotation` AS `annotation`,
    `tbl__news`.`text` AS `text`,
    `tbl__news`.`fp` AS `fp`,
    `tbl__news`.`active` AS `active`,
    `tbl__news`.`display_order` AS `display_order` 
  from 
    `tbl__news` 
  where 
    (`tbl__news`.`active` = 1);

CREATE VIEW `vw__news_recent` AS 
  select 
    `tbl__recent`.`tbl_cai_id` AS `tbl_cai_id`,
    `tbl__recent`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__recent`.`title` AS `title`,
    `tbl__recent`.`date` AS `date`,
    `tbl__recent`.`s_image` AS `s_image`,
    `tbl__recent`.`annotation` AS `annotation`,
    `tbl__recent`.`text` AS `text`,
    `tbl__recent`.`fp` AS `fp`,
    `tbl__recent`.`active` AS `active`,
    `tbl__recent`.`display_order` AS `display_order` 
  from 
    `tbl__recent` 
  where 
    (`tbl__recent`.`active` = 1);

CREATE VIEW `vw__contractor_list` AS 
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
    group_concat(`a`.`title` separator ', ') AS `activity_title` 
  from 
    (((`tbl__contractor_doc` `c` left join `tbl__contractor2activity` `c2a` on((`c`.`tbl_obj_id` = `c2a`.`tbl_obj_id`))) left join `tbl__activity_type` `a` on((`a`.`tbl_obj_id` = `c2a`.`kind_of_activity`))) left join `tbl__city` `ct` on((`c`.`city` = `ct`.`tbl_obj_id`))) 
  where 
    (`c`.`active` = 1) 
  group by 
    `c`.`tbl_obj_id`,`c`.`title`,`c`.`city`,`ct`.`title`,`c`.`kind_of_activity`,`c`.`short_description`,`c`.`address`,`c`.`phone`,`c`.`site_address`,`c`.`email`,`c`.`selection`,`c`.`logo_image`,`c`.`phone2`;

CREATE VIEW `vw__contractor_details_list` AS 
  select 
    `c`.`tbl_obj_id` AS `tbl_obj_id`,
    `c`.`title` AS `title`,
    `c`.`kind_of_activity` AS `kind_of_activity`,
    `c`.`short_description` AS `short_description`,
    `c`.`description` AS `description`,
    `c`.`address` AS `address`,
    `c`.`phone` AS `phone`,
    `c`.`site_address` AS `site_address`,
    `c`.`email` AS `email`,
    `c`.`selection` AS `selection`,
    `c`.`logo_image` AS `logo_image`,
    `c`.`phone2` AS `phone2`,
    `c`.`city` AS `city`,
    `ct`.`title` AS `city_name`,
    group_concat(`a`.`title` separator ', ') AS `activity_title` 
  from 
    (((`tbl__contractor_doc` `c` left join `tbl__contractor2activity` `c2a` on((`c`.`tbl_obj_id` = `c2a`.`tbl_obj_id`))) left join `tbl__activity_type` `a` on((`a`.`tbl_obj_id` = `c2a`.`kind_of_activity`))) left join `tbl__city` `ct` on((`c`.`city` = `ct`.`tbl_obj_id`))) 
  group by 
    `c`.`tbl_obj_id`,`c`.`title`,`c`.`description`,`c`.`city`,`ct`.`title`,`c`.`kind_of_activity`,`c`.`short_description`,`c`.`address`,`c`.`phone`,`c`.`site_address`,`c`.`email`,`c`.`selection`,`c`.`logo_image`,`c`.`phone2`;

CREATE VIEW `vw__city_location` AS 
  select 
    `cl`.`tbl_obj_id` AS `tbl_obj_id`,
    `cl`.`title` AS `title`,
    `c2l`.`parent_id` AS `city` 
  from 
    (`tbl__city_location` `cl` join `tbl__city2location` `c2l` on((`cl`.`tbl_obj_id` = `c2l`.`child_id`))) 
  where 
    (`cl`.`active` = 1);

CREATE VIEW `vw__baraholka_photo` AS 
  select 
    `bp`.`tbl_cai_id` AS `tbl_cai_id`,
    `bp`.`tbl_obj_id` AS `tbl_obj_id`,
    `bp`.`title` AS `title`,
    `bp`.`s_image` AS `s_image`,
    `bp`.`m_image` AS `m_image`,
    `bp`.`l_image` AS `l_image`,
    `p2b`.`parent_id` AS `junk_id` 
  from 
    (`tbl__photo` `bp` join `tbl__photo2baraholka` `p2b` on((`bp`.`tbl_obj_id` = `p2b`.`child_id`)));

CREATE VIEW `vw__banners` AS 
  select 
    `tbl__bans_doc`.`tbl_obj_id` AS `tbl_obj_id`,
    concat(`tbl__bans_doc`.`title`,_cp1251' | ',`tbl__bans_doc`.`file`) AS `title` 
  from 
    `tbl__bans_doc` 
  where 
    (`tbl__bans_doc`.`active` = 1);

CREATE VIEW `vw__artist_photos` AS 
  select 
    `pt`.`tbl_obj_id` AS `tbl_obj_id`,
    `pt`.`title` AS `title`,
    `pt`.`s_image` AS `s_image`,
    `pt`.`m_image` AS `m_image`,
    `pt`.`l_image` AS `l_image`,
    `a2p`.`parent_id` AS `artist_id`,
    ((length(`pt`.`s_image`) + length(`pt`.`m_image`)) + length(`pt`.`l_image`)) AS `hasImage` 
  from 
    (`tbl__photo` `pt` join `tbl__artist2photos` `a2p` on((`pt`.`tbl_obj_id` = `a2p`.`child_id`)));

CREATE VIEW `vw__artist_lite` AS 
  select 
    distinct `ar`.`tbl_obj_id` AS `tbl_obj_id`,
    `ar`.`title` AS `title`,
    `ar`.`group` AS `group`,
    `ar`.`subgroup` AS `subgroup`,
    `ar`.`region` AS `region`,
    `ar`.`country` AS `country`,
    `ar`.`style` AS `style`,
    `ar`.`description` AS `description`,
    `ar`.`site_address` AS `site_address`,
    `ar`.`demo` AS `demo`,
    `ar`.`manager_name` AS `manager_name`,
    `ar`.`agency_address` AS `agency_address`,
    `ar`.`logo` AS `logo`,
    `ar`.`manager_phone` AS `manager_phone`,
    `ar`.`email` AS `email`,
    `ag`.`title` AS `group_title`,
    `asg`.`title` AS `subgroup_title`,
    `rg`.`title` AS `region_title`,
    `cn`.`title` AS `country_title`,
    `mp3`.`title` AS `mp3_title`,
    `mp3`.`file` AS `mp3_file` 
  from 
    ((((((`tbl__artist_doc` `ar` join `tbl__artist_group` `ag` on((`ar`.`group` = `ag`.`tbl_obj_id`))) join `tbl__artist_subgroup` `asg` on((`asg`.`tbl_obj_id` = `ar`.`subgroup`))) join `tbl__regions` `rg` on((`rg`.`tbl_obj_id` = `ar`.`region`))) join `tbl__countries` `cn` on((`cn`.`tbl_obj_id` = `ar`.`country`))) left join `vw__artist2mp3` `mp` on((`mp`.`parent_id` = `ar`.`tbl_obj_id`))) left join `tbl__mp3` `mp3` on((`mp3`.`tbl_obj_id` = `mp`.`child_id`))) 
  where 
    (`ar`.`active` = 1) 
  group by 
    `ar`.`tbl_obj_id`,`ar`.`title`,`ar`.`group`,`ar`.`subgroup`,`ar`.`region`,`ar`.`country`,`ar`.`style`,`ar`.`description`,`ar`.`cost`,`ar`.`site_address`,`ar`.`demo`,`ar`.`manager_name`,`ar`.`agency_address`,`ar`.`logo`,`ar`.`manager_phone`,`ar`.`email`,`ag`.`title`,`asg`.`title`,`rg`.`title`,`cn`.`title`,`mp3`.`title`,`mp3`.`file`;

CREATE VIEW `vw__artist_group_hierarcy` AS 
  select 
    `tbl__artist_subgroup`.`tbl_obj_id` AS `child_id`,
    `tbl__artist_subgroup`.`parent_id` AS `parent_id`,
    `tbl__artist_subgroup`.`title` AS `title` 
  from 
    `tbl__artist_subgroup` union 
  select 
    `tbl__artist_group`.`tbl_obj_id` AS `child_id`,
    0 AS `parent_id`,
    `tbl__artist_group`.`title` AS `title` 
  from 
    `tbl__artist_group`;

CREATE VIEW `vw__artist2mp3` AS 
  select 
    max(`m2a`.`child_id`) AS `child_id`,
    `m2a`.`parent_id` AS `parent_id` 
  from 
    (`tbl__artist2mp3` `m2a` join `tbl__mp3` `mp` on((`m2a`.`child_id` = `mp`.`tbl_obj_id`))) 
  where 
    (length(`mp`.`file`) > 0) 
  group by 
    `m2a`.`parent_id`;

CREATE VIEW `vw__area_subtypes` AS 
  select 
    `arst`.`tbl_obj_id` AS `tbl_obj_id`,
    `arst`.`title` AS `title`,
    `t2s`.`parent_id` AS `parent_id` 
  from 
    (`tbl__area_subtypes` `arst` join `tbl__area_types2subtypes` `t2s` on((`arst`.`tbl_obj_id` = `t2s`.`child_id`)));

CREATE VIEW `vw__area_photos` AS 
  select 
    `p`.`tbl_cai_id` AS `tbl_cai_id`,
    `p`.`tbl_obj_id` AS `tbl_obj_id`,
    `p`.`title` AS `title`,
    `p`.`s_image` AS `s_image`,
    `p`.`m_image` AS `m_image`,
    `p`.`l_image` AS `l_image`,
    `ap`.`parent_id` AS `area_id` 
  from 
    (`tbl__area_photos` `ap` join `tbl__photo` `p` on((`ap`.`child_id` = `p`.`tbl_obj_id`)));

CREATE VIEW `vw__area_list` AS 
  select 
    `a`.`tbl_obj_id` AS `tbl_obj_id`,
    replace(replace(`a`.`title`,_cp1251'"',_cp1251''),_cp1251'\'',_cp1251'') AS `title`,
    `a`.`area_type` AS `area_type`,
    `a`.`area_subtype` AS `area_subtype`,
    `a`.`area_cost` AS `area_cost`,
    `a`.`description` AS `description`,
    `a`.`menu` AS `menu`,
    `a`.`city` AS `city`,
    `a`.`city_location` AS `city_location`,
    `a`.`address` AS `address`,
    `a`.`phone` AS `phone`,
    `a`.`rent` AS `rent`,
    `a`.`email` AS `email`,
    `a`.`site_address` AS `site_address`,
    `a`.`selection` AS `selection`,
    `a`.`max_count_man` AS `max_count_man`,
    `a`.`max_sitting_man` AS `max_sitting_man`,
    `a`.`date_open` AS `date_open`,
    `a`.`kitchen` AS `kitchen`,
    `a`.`parking` AS `parking`,
    `a`.`equipment` AS `equipment`,
    `a`.`dancing` AS `dancing`,
    `a`.`style` AS `style`,
    `a`.`plus` AS `plus`,
    `a`.`active` AS `active`,
    `a`.`logo` AS `logo`,
    `a`.`location_scheme` AS `location_scheme`,
    cast(`a`.`area_cost` as decimal(10,0)) AS `area_cost_decimal`,
    `c`.`title` AS `city_name`,
    `cl`.`title` AS `location_name`,
    `at`.`title` AS `area_type_name`,
    `ast`.`title` AS `area_subtype_name`,
    count(`ah`.`tbl_obj_id`) AS `halls_count` 
  from 
    (((((`tbl__area_doc` `a` join `tbl__city` `c` on((`a`.`city` = `c`.`tbl_obj_id`))) left join `tbl__city_location` `cl` on((`cl`.`tbl_obj_id` = `a`.`city_location`))) join `tbl__area_types` `at` on((`at`.`tbl_obj_id` = `a`.`area_type`))) join `tbl__area_subtypes` `ast` on((`ast`.`tbl_obj_id` = `a`.`area_subtype`))) left join `tbl__area_halls` `ah` on((`ah`.`area_id` = `a`.`tbl_obj_id`))) 
  where 
    (`a`.`active` = 1) 
  group by 
    `a`.`tbl_obj_id`,`a`.`title`,`a`.`area_type`,`a`.`area_subtype`,`a`.`area_cost`,`a`.`description`,`a`.`menu`,`a`.`city`,`a`.`city_location`,`a`.`address`,`a`.`phone`,`a`.`rent`,`a`.`email`,`a`.`site_address`,`a`.`selection`,`a`.`max_count_man`,`a`.`max_sitting_man`,`a`.`date_open`,`a`.`kitchen`,`a`.`parking`,`a`.`equipment`,`a`.`dancing`,`a`.`style`,`a`.`plus`,`a`.`active`,`a`.`logo`,`a`.`location_scheme`,cast(`a`.`area_cost` as decimal(10,0)),`c`.`title`,`cl`.`title`,`at`.`title`,`ast`.`title`;

CREATE VIEW `vw__all_users` AS 
  select 
    `tbl__registered_user`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__registered_user`.`title` AS `title`,
    `tbl__registered_user`.`email` AS `email`,
    `tbl__registered_user`.`login` AS `login`,
    `tbl__registered_user`.`password` AS `password`,
    `tbl__registered_user`.`forum_name` AS `forum_name`,
    `tbl__registered_user`.`registration_date` AS `registration_date`,
    `tbl__registered_user`.`registration_confirmed` AS `registration_confirmed`,
    `tbl__registered_user`.`active` AS `active`,
    `tbl__registered_user`.`registration_confirm_code` AS `registration_confirm_code`,
    _cp1251'user' AS `login_type` 
  from 
    `tbl__registered_user` union 
  select 
    `tbl__agency_doc`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__agency_doc`.`title` AS `title`,
    `tbl__agency_doc`.`email` AS `email`,
    `tbl__agency_doc`.`login` AS `login`,
    `tbl__agency_doc`.`password` AS `password`,
    `tbl__agency_doc`.`forum_name` AS `forum_name`,
    `tbl__agency_doc`.`registration_date` AS `registration_date`,
    `tbl__agency_doc`.`registration_confirmed` AS `registration_confirmed`,
    `tbl__agency_doc`.`active` AS `active`,
    `tbl__agency_doc`.`registration_confirm_code` AS `registration_confirm_code`,
    _cp1251'agency' AS `login_type` 
  from 
    `tbl__agency_doc` union 
  select 
    `tbl__area_doc`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__area_doc`.`title` AS `title`,
    `tbl__area_doc`.`email` AS `email`,
    `tbl__area_doc`.`login` AS `login`,
    `tbl__area_doc`.`password` AS `password`,
    `tbl__area_doc`.`forum_name` AS `forum_name`,
    `tbl__area_doc`.`registration_date` AS `registration_date`,
    `tbl__area_doc`.`registration_confirmed` AS `registration_confirmed`,
    `tbl__area_doc`.`active` AS `active`,
    `tbl__area_doc`.`registration_confirm_code` AS `registration_confirm_code`,
    _cp1251'area' AS `login_type` 
  from 
    `tbl__area_doc` union 
  select 
    `tbl__contractor_doc`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__contractor_doc`.`title` AS `title`,
    `tbl__contractor_doc`.`email` AS `email`,
    `tbl__contractor_doc`.`login` AS `login`,
    `tbl__contractor_doc`.`password` AS `password`,
    `tbl__contractor_doc`.`forum_name` AS `forum_name`,
    `tbl__contractor_doc`.`registration_date` AS `registration_date`,
    `tbl__contractor_doc`.`registration_confirmed` AS `registration_confirmed`,
    `tbl__contractor_doc`.`active` AS `active`,
    `tbl__contractor_doc`.`registration_confirm_code` AS `registration_confirm_code`,
    _cp1251'contractor' AS `login_type` 
  from 
    `tbl__contractor_doc` union 
  select 
    `tbl__artist_doc`.`tbl_obj_id` AS `tbl_obj_id`,
    `tbl__artist_doc`.`title` AS `title`,
    `tbl__artist_doc`.`email` AS `email`,
    `tbl__artist_doc`.`login` AS `login`,
    `tbl__artist_doc`.`password` AS `password`,
    `tbl__artist_doc`.`forum_name` AS `forum_name`,
    `tbl__artist_doc`.`registration_date` AS `registration_date`,
    `tbl__artist_doc`.`registration_confirmed` AS `registration_confirmed`,
    `tbl__artist_doc`.`active` AS `active`,
    `tbl__artist_doc`.`registration_confirm_code` AS `registration_confirm_code`,
    _cp1251'artist' AS `login_type` 
  from 
    `tbl__artist_doc`;

CREATE VIEW `vw__area_cities` AS 
  select 
    distinct `c`.`tbl_obj_id` AS `tbl_obj_id`,
    `c`.`title` AS `title` 
  from 
    (`tbl__city` `c` join `vw__area_list` `a` on((`a`.`city` = `c`.`tbl_obj_id`))) 
  where 
    (`c`.`active` = 1);

CREATE VIEW `vw__agency_list` AS 
  select 
    `a`.`tbl_obj_id` AS `tbl_obj_id`,
    `a`.`title` AS `title`,
    `a`.`logo_image` AS `logo_image`,
    `a`.`kind_of_activity` AS `kind_of_activity`,
    `a`.`description` AS `description`,
    `a`.`city` AS `city`,
    `a`.`address` AS `address`,
    `a`.`phone` AS `phone`,
    `a`.`site_address` AS `site_address`,
    `a`.`email` AS `email`,
    `a`.`phone2` AS `phone2`,
    `a`.`selection` AS `selection`,
    `at`.`title` AS `activity_title`,
    `ct`.`title` AS `city_title` 
  from 
    ((`tbl__agency_doc` `a` join `tbl__agency_type` `at` on((`a`.`kind_of_activity` = `at`.`tbl_obj_id`))) join `tbl__city` `ct` on((`a`.`city` = `ct`.`tbl_obj_id`))) 
  where 
    (`a`.`active` = 1);

CREATE VIEW `vw__activity_hierarcy2contractors` AS 
  select 
    distinct `tbl__activity_type`.`tbl_obj_id` AS `child_id`,
    `tbl__activity_type`.`parent_id` AS `parent_id`,
    `tbl__activity_type`.`title` AS `title` 
  from 
    `tbl__activity_type`;

CREATE VIEW `vw__activities_parent` AS 
  select 
    `vw__activity_hierarcy2contractors`.`child_id` AS `child_id`,
    `vw__activity_hierarcy2contractors`.`title` AS `title` 
  from 
    `vw__activity_hierarcy2contractors` 
  where 
    (`vw__activity_hierarcy2contractors`.`parent_id` = 0);

CREATE VIEW `vw__activities` AS 
  select 
    `vw__activity_hierarcy2contractors`.`child_id` AS `child_id`,
    `vw__activity_hierarcy2contractors`.`title` AS `title` 
  from 
    `vw__activity_hierarcy2contractors` 
  where 
    (`vw__activity_hierarcy2contractors`.`parent_id` <> 0);