<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>
<p>Редактирование главной EventTV</p>
<form method="post">
<table style="font-size: 12px;">
  <tr>
    <td>Идентификатор видео:</td>
    <td><input type="text" size="20" name="video_id" value="<?php print $this->video_id; ?>"></td>
    <td style="color: #888888;">Номер из URL видео vimeo (http://vimeo.com/<b>7100569</b>)</td>
  </tr>
  <tr>
    <td>Связанная статья: </td>
    <td><select size="1" name="doc_id"><?php print $this->doc_list; ?></select></td>
    <td style="color: #888888;">Для ссылки "Написать комментарий". Если не выбрать, то ссылка не будет отображаться</td>
  </tr>
</table>
<input type="submit" value="Сохранить">
</form>
</div>
</body>
</html>
