<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>

	
	<table border="0" width="100%" cellpadding="2" cellspacing="2" class="dataTable">
		<tr>
			<th >
				<a href="">ID</a>
			</th>
			<th>
				<a href="">Тип пользователя</a>
			</th>
			<th>
				<a href="">Название/Имя</a>
			</th>
			<th>
				<a href="">Логин</a>
			</th>
			<th>
				<a href="">Дата</a>
			</th>
		</tr>
		<?php
			$users = SQLProvider::ExecuteQuery("
			select
				tbl_obj_id,
				case login_type
					when 'user' then 'Пользователь'
					when 'contractor' then 'Подрядчик'
					when 'area' then 'Площадка'
					when 'artist' then 'Артист'
					when 'agency' then 'Агентство'
				end as type,
				title,
				login,
				edit_date
			from vw__all_users
			where subscribe2=0
			");
			foreach ($users as $key=>$user) {
			 if(!empty($user["edit_date"])) { $date = date('Y-m-d', $user["edit_date"]); }
				echo "<tr>";
				echo "<td  class=\"alterItem\">".$user["tbl_obj_id"]."</td>";
				echo "<td  class=\"alterItem\">".$user["type"]."</td>";
				echo "<td  class=\"alterItem\">".$user["title"]."</td>";
				echo "<td  class=\"alterItem\">".$user["login"]."</td>";
				echo "<td  class=\"alterItem\">". $date ."</td>";
				echo "</tr>";
			}
		?>
	</table>

</body>
</html>
