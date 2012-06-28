<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css">
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
<script type="text/javascript" language="javascript">
function findChange()
{
  value = document.getElementById('inputField').value;
  if (value == "введите имя, никнэйм или email")
    value = "";
  document.getElementById('titleField').value = '%'+value+'%';
  document.getElementById('nikField').value = '%'+value+'%';
  document.getElementById('emailField').value = '%'+value+'%';
}

function inputBlur(obj)
{
  if (obj.value == ""){
    obj.value = "введите имя, никнэйм или email";
    obj.style.color = "gray";
  }
}

function inputFocus(obj)
{
  if (obj.value == "введите имя, никнэйм или email"){
    obj.value = "";
    obj.style.color = "black";
  }
}
</script>
</head>

<body>
<div style="padding-bottom:7px;">
  <form action="/cms/users" method="get" onsubmit="findChange(); return true;">
  <table cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td nowrap style="font-size: 10pt; padding: 0 5px 0 0;">Поиск</td>
    <td width="100%" valign="bottom">
      <table cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td><img src="/images/search/leftind.gif"></td>
        <td background="/images/search/bgind.gif" width="100%" style="vertical-align: top; padding-top: 5px;">
          <input type="hidden" value="" name="<?php print($this->GetControl("dataTable")->uniqueId); ?>[$autoValues][title]" id="titleField">
          <input type="hidden" value="" name="<?php print($this->GetControl("dataTable")->uniqueId); ?>[$autoValues][nikname]" id="nikField">
          <input type="hidden" value="" name="<?php print($this->GetControl("dataTable")->uniqueId); ?>[$autoValues][email]" id="emailField">
          <input value="<?php
          $value = $this->GetControl("dataTable")->findValue;
          print($value?$value:"введите имя, никнэйм или email"); ?>" onblur="inputBlur(this);" onfocus="inputFocus(this);" type="text" name="" style="width: 100%; border: 0px black solid; color: gray;" id="inputField"></td>
          <td><img src="/images/search/rightind.gif"></td>
      </tr>
      </table>
    </td>
    <td style="vertical-align:middle; padding-top:0px; padding: 0 0 0 10px;" valign="top"><input type="image"  src="/images/search/butind.gif"></td>
   </tr>
   </table>
  </form>
</div>
<?php CRenderer::RenderControl("dataTable"); ?>
</body>
</html>
