<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Untitled Document</title>
</head>

<body>

	<?php
	
	$order = GP("order","query asc");
	
	$queries = SQLProvider::ExecuteQuery("
		select 
			query, COUNT(*) as count
		from tbl__search_queries fq
		GROUP BY query
		order by $order
		");
	
	echo "Сортировать по&nbsp;&nbsp;<a href=\"?order=query asc\">запросу</a>&nbsp;&nbsp;<a href=\"?order=count desc\">количеству</a><br><br>";
	
	echo "<table border=1>";
	foreach ($queries as $key=>$query) {
		echo "<tr>";
		
		echo "<td>".$query["query"]."</td>";
		echo "<td>".$query["count"]."</td>";
		
		echo "</tr>";
	}
	echo "</table>";
	
	?>


</body>
</html>
