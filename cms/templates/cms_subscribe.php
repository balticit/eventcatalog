<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>
<div style="height: 700px;">

<?php

	if (!isset($_POST["sended"]) || !$_POST["sended"]) {

?>

	<script language = "JavaScript">
		var filescount = 1;
		var elid = "files1";
		var buttondiv = "button1";
		
		function AddItem() {
			filescount++;
			lastelid = elid;
			elid = "files" + filescount;
			document.getElementById(buttondiv).innerHTML = "";
			buttondiv = "button" + filescount;
			document.getElementById(lastelid).innerHTML = "\n<table cellspacing=0><tr><td><input type=file name='att"+filescount+"'></td><td><div id="+buttondiv+"><input type=button value=' + ' onClick=AddItem(); ID=AddPhoto></div></td></tr></table><div id="+elid+"></div>\n";
			document.getElementById("fileslabel").innerHTML = "Добавить файлы("+filescount+")";
			document.getElementById("trfiles").height = 15*filescount;
		}
	</script>


<form  action="" enctype="multipart/form-data" method="post">

	<table border="0" width="100%" cellpadding="2" cellspacing="2" class="dataTable">
		<tr>
			<td valign="top" class="itemLabel">
				От кого
			</td>
			<td class="editCell">
				<input type="text" name="from" value="promo@eventcatalog.ru"/>
			</td>
		</tr>
		<tr>
			<td valign="top"  class="itemLabel">
				Кому
			</td>
			<td class="editCell">
				<input type="text" name="to" />
			</td>
		</tr>
		<tr>
			<td valign="top"  class="itemLabel">
				Заголовок письма
			</td>
			<td class="editCell">
				<input type="text" name="subject" value="Event Promo Mail"/>
			</td>
		</tr>
		
		<tr>
			<td valign="top"  class="itemLabel">
				Дата и время рассылки
			</td>
			<td class="editCell">
			  <script language="javascript" type="text/javascript" src="/js/datetimepicker.js"></script>
				<input type="text" name="date" id="send_date" />
				<a target="_self" href="javascript:NewCal('send_date','yyyymmdd',true,24);"><img border="0" style="" title="" alt="" class="class" src="/images/cal.gif"></a>
			</td>
		</tr>
		
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="user" checked="checked" />
			</td>
			<td class="editCell">
				Разослать по обычным пользователям
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="contractor" checked="checked" />
			</td>
			<td class="editCell">
				Разослать по подрядчикам
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="area" checked="checked" />
			</td>
			<td class="editCell">
				Разослать по площадкам
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="artist" checked="checked" />
			</td>
			<td class="editCell">
				Разослать по артистам
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="agency" checked="checked" />
			</td>
			<td class="editCell">
				Разослать по агентствам
			</td>
		</tr>
        <tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user_subscribed" value="1" checked="checked" />
			</td>
			<td class="editCell">
				Разослать по незарегестированным пользователям
			</td>
		</tr>

		<tr>
			<td valign="top"  class="itemLabel">
				Сообщение
			</td>
			<td class="editCell">
				<!--<textarea  name="message" rows="8" cols="55"></textarea>
				<input type="class" name="message" class="CTinyMCETextareaGridDataField"/>-->
				<?php CRenderer::RenderControl("content"); ?>
			</td>
		</tr>
		<tr>
			<td id="trfiles" valign="top"  class="itemLabel">
				<div ID="fileslabel">Добавить файлы(1)</div>
			</td>
			<td style="padding: 0px;">
				<div ID="files" style="padding: 0px;">
					<table cellspacing=0>
						<tr>
							<td>
								<input type=file name='att1'>
							</td>
							<td>
								<div ID="button1" style="padding: 0px;">
									<input type="button" value=' + ' onClick='AddItem();' ID="AddPhoto">
								</div>
							</td>
						</tr>
					</table>
					<div ID="files1">
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="Отправить">
			</td>
			<td>
				<input type="reset" value="Отменить ввод">
			</td>
		</tr>
	</table>
	<input type="hidden" name="sended" value="sended" />

</form>
	
<?php

	}
	else {
	
	
        $boundary = md5(rand(0,65535));
        
        $subject = "=?windows-1251?b?" . base64_encode($_POST["subject"]) . "?=";
	
		$add_header = "From: EVENT INFO <$_POST[from]>
Reply-To: $_POST[from]
MIME-Version: 1.0
Content-Type: multipart/mixed; charset=\"windows-1251\"; boundary=\"$boundary\";
Content-Transfer-Encoding: 8bit\n";
						
		$body1 = "--$boundary
Content-Type: text/html; charset=\"windows-1251\"
Content-Transfer-Encoding: 8bit

<html>
<body style=\"font-family:Arial;font-size:10pt;line-height:11pt;\">
<div style=\"font-family:Arial;font-size:10pt;line-height:11pt;\">$_POST[message]</div>
</body>
</html>

";

		$body2 = "";

		foreach ($_FILES as $key=>$attfile) {
		
			if ($attfile["error"]==0) {
		
				ereg(".[\\|\/]?(.*)",$attfile["name"],$regs);
			
				$body2 .= "--$boundary
Content-Type: ".$attfile["type"]."; name=\"".$regs[0]."\";
Content-Transfer-Encoding: base64
Content-Deposition: attachment

".base64_encode(file_get_contents($attfile["tmp_name"]))."\n
";
			}	
		
		}
		
		
		$sendfilter = "";
		
		if (isset($_POST["user"])) {
			foreach ($_POST["user"] as $key=>$value) {
				$sendfilter .= " or login_type='".$value."'";
			}
		}
		
		$body2 .="\n--$boundary";
        
        $r = SQLProvider::ExecuteQuery("select count(tbl_obj_id) as kolvo from vw__all_users where subscribe=1 and (0=1 $sendfilter)");
        
        $totalcount = 0;
		    if ($r) {
            $r = $r[0];
		    $totalcount = $r['kolvo'];
        }
        $user_subscribed = 0;
        if (isset($_POST["user_subscribed"])) {
            $user_subscribed = 1;
            $r2 = SQLProvider::ExecuteQuery("select count(1) as kolvo from tbl__subscribed");
            if ($r2) {
                $r2 = $r2[0];
                $totalcount += $r2['kolvo'];
            }
        }    
        
        
        unlink(TMP_DIR.'sendmail_new.txt');
        file_put_contents(TMP_DIR.'sendmail_new.txt',$body1.$body2);        
		

		
		    if($_POST["date"]!="") {

		      $date = $_POST["date"];
		    

          $filter = "";
          if (isset($_POST["user"])) {
      			foreach ($_POST["user"] as $key=>$value) {
      				$filter .= " or login_type=\'".$value."\'";
      			}
      		}
      		
      		
      		$body1 = "--$boundary
Content-Type: text/html; charset=\"windows-1251\"
Content-Transfer-Encoding: 8bit

<html>
<body style=\"font-family:Arial;font-size:10pt;line-height:11pt;\">
<div style=\"font-family:Arial;font-size:10pt;line-height:11pt;\">$_POST[message]</div>
<p style='color:gray;font-size:8pt;'>
Если Вы не желаете получать рассылку от <a href=\"http://www.eventcatalog.ru/\">www.eventcatalog.ru</a>, пожалуйста, перейдите по <a href=\"http://eventcatalog.ru/u_cabinet\">этой ссылке</a>
</p>
</body>
</html>

";
          
          SQLProvider::ExecuteNonReturnQuery("update tbl_advertising_mailer_config set body='$body1.$body2',filter='$filter', header='$add_header', subject='$subject', date='$date', u_subscribed='$user_subscribed', status=0 WHERE id = '1' ");
          echo "Рассылка пройдет в " .$_POST["date"];
        }
		    else {
		    
      		if ($_POST["to"]!="") {
      			mail($_POST["to"],$subject,$body1.$body2,$add_header);
                  ?>Сообщение разослано<?php
      		}
          else {
              $linedelimeter = "";
              if (strpos($add_header,"\r\n") == false) {
                  $linedelimeter = "\n";
              }
              else {
                  $linedelimeter = "\r\n";
              }
              
              $add_header = trim(str_replace($linedelimeter,"\\n",$add_header));
              
              include 'cms_subscribe_ajax.php';
          }
          
        }
        
        
        

	}

?>
</div>
</body>
</html>
