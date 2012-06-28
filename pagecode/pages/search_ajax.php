<?php
class search_ajax_php extends CPageCodeHandler
{
  public function search_ajax_php()
  {
    $this->CPageCodeHandler();
  }

  public function PreRender() {
  }

  public function Render()
  {
    $query = GP("query");
    header('Content-type: text/html;charset=utf-8');
    $query1251 = mb_convert_encoding($query,"WINDOWS-1251","UTF-8");
    $sql = "select t.title as title, t.login_type as alias, t.tbl_obj_id as id FROM vw__all_users t WHERE  t.active = 1 AND t.login_type <> 'user' AND LOWER(t.title) LIKE LOWER('$query1251%') ORDER BY t.title ASC LIMIT 0,15;";
    $results = SQLProvider::ExecuteQuery($sql);
    $result = '';
    foreach ($results as $row)
      $result .= ",'".mb_convert_encoding($row['title'],"UTF-8","WINDOWS-1251")."'";
    $result = "{query:'$query',suggestions:[".substr($result,1)."]}";
    echo($result);
  }
}
?>