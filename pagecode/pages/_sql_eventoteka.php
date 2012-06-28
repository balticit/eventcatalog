select
									tbl_obj_id, title,title_url, annotation, text, registration_date,
                  case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new,
                  logo_image, link,
									DATE_FORMAT(registration_date,'%d.%m.%Y') formatted_date, topics
								from (
									select tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'eventtv' link, 
									(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = tbl_obj_id
									order by pt.order_num) topics 
									from tbl__eventtv_doc where active = 1
									union all
									select tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link, 
									(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = tbl_obj_id
									order by pt.order_num) topics
									from tbl__public_doc where active = 1
									) t
								order by registration_date desc"