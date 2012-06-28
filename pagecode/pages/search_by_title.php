<?php

  $querywords = array();
  foreach ($words as $value) {
    array_push($querywords,"(UPPER(t.title) LIKE '%$value%')");
  }

  $query = "select
    'title' as searchtype,
    t.title as title,
    t.title_url as title_url,
    CASE t.login_type
      WHEN 'agency' THEN 'Агенство'
      WHEN 'area' THEN 'Площадки'
      WHEN 'contractor' THEN 'Подрядчики'
      WHEN 'artist' THEN 'Артисты'
    END as division,
    t.login_type as alias,
    t.tbl_obj_id as id, (";
  
  $queryfilter = "";
  foreach ($querywords as $value) {
      $query .= $value." + ";
      $queryfilter .= " OR ".$value;
  }
  
  $queryfilter = substr($queryfilter,3);
  
  $query .= " 0) as relev
    FROM
      vw__all_users t
    WHERE\n";
    
  $filter = "t.active = 1 AND t.login_type <> 'user'";
  if ($contractor == 0) {
      $filter .= " AND t.login_type <> 'contractor'";
  }
  if ($area == 0) {
      $filter .= " AND t.login_type <> 'area'";
  }
  if ($artist == 0) {
      $filter .= " AND t.login_type <> 'artist'";
  }
  if ($agency == 0) {
      $filter .= " AND t.login_type <> 'agency'";
  }
  
  $query .= "$filter AND ($queryfilter)\n ORDER BY relev DESC, title ASC";
  
  $titleSearch = SQLProvider::ExecuteQuery($query);
  
  $searchResult = array_merge($searchResult,$titleSearch);
