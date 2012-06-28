<?php
  $querywords = array();
  foreach ($words as $value) {
    array_push($querywords,"(UPPER(t.body) LIKE '%$value%')");
  }
  
  $query = "select 'other' as searchtype, t.* , (";
  
  $queryfilter = "";
  foreach ($querywords as $value) {
      $query .= $value." + ";
      $queryfilter .= " OR ".$value;
  }
  
  $queryfilter = substr($queryfilter,3);
  
  $query .= " 0) as relev
    FROM
      vw__search_others t
    WHERE\n";
    
  $query .= "$queryfilter\n ORDER BY relev DESC, title ASC";
  
  $otherSearch = SQLProvider::ExecuteQuery($query);
  
  $searchResult = array_merge($searchResult,$otherSearch);    