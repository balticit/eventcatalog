<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
<script type="text/javascript" language="javascript" src="/js/datetimepicker.js"></script>
</head>

<body>
<div style="height: 700px;">

<?php

	if (!isset($_POST["sended"]) || !$_POST["sended"] || $_POST["sended"]=="false") {

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
			document.getElementById("fileslabel").innerHTML = "�������� �����("+filescount+")";
			document.getElementById("trfiles").height = 15*filescount;
		}
        
        function refreshPeriod() {
            document.getElementById("sendedHiddenField").value="false";
        }
	</script>


<form  action="" enctype="multipart/form-data" method="post">

	<table border="0" width="100%" cellpadding="2" cellspacing="2" class="dataTable">
        <tr>
            <td valign="top" class="itemLabel">
                ������ �������
            </td>
            <td class="editCell">
                <?php
                  $prevWeek = time() - (7 * 24 * 60 * 60);
                  if (GP('date_from')) {
                      $prevWeek = strtotime(GP('date_from'));
                  }
                  $prevWeek = date("Y-m-d",$prevWeek);                  
                ?>
                <input id="date_from" type="text" name="date_from" value="<?php echo $prevWeek ?>"/>
                <a href="javascript:NewCal('date_from','yyyymmdd',true,24);" target="_self"><img src="/images/cal.gif" class="class" border="0"/></a>
            </td>
        </tr>
        <tr>
            <td valign="top"  class="itemLabel">
                ����� �������
            </td>
            <td class="editCell">
                <?php
                  $dateTo = time();
                  if (GP('date_to')) {
                      $dateTo = strtotime(GP('date_to'));
                  }
                  $dateTo = date("Y-m-d",$dateTo);                  
                ?>            
                <input id="date_to" type="text" name="date_to" value="<?php echo $dateTo?>"/>
                <a href="javascript:NewCal('date_to','yyyymmdd',true,24);" target="_self"><img src="/images/cal.gif" class="class" border="0"/></a>
            </td>
        </tr>    
        <tr>
            <td valign="top"  class="itemLabel">&nbsp;</td>
            <td class="editCell">
              <input type="submit" value="�������� ������" onclick="refreshPeriod();">
            </td>
        </tr>         
		<tr>
			<td valign="top" class="itemLabel">
				�� ����
			</td>
			<td class="editCell">
				<input type="text" name="from" value="automail@eventcatalog.ru"/>
			</td>
		</tr>
		<tr>
			<td valign="top"  class="itemLabel">
				����
			</td>
			<td class="editCell">
				<input type="text" name="to" />
			</td>
		</tr>
		<tr>
			<td valign="top"  class="itemLabel">
				��������� ������
			</td>
			<td class="editCell">
				<input type="text" name="subject" value="��������� ���������� � EVENT��������"/>
			</td>
		</tr>
		
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="user" checked="checked" />
			</td>
			<td class="editCell">
				��������� �� ������� �������������
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="contractor" checked="checked" />
			</td>
			<td class="editCell">
				��������� �� �����������
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="area" checked="checked" />
			</td>
			<td class="editCell">
				��������� �� ���������
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="artist" checked="checked" />
			</td>
			<td class="editCell">
				��������� �� ��������
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user[]" value="agency" checked="checked" />
			</td>
			<td class="editCell">
				��������� �� ����������
			</td>
		</tr>
        <tr>
			<td valign="top" align="right"  class="itemLabel">
				<input type="checkbox" name="user_subscribed" value="1" checked="checked" />
			</td>
			<td class="editCell">
				��������� �� ������������������� �������������
			</td>
		</tr>
        
		<tr>
			<td valign="top"  class="itemLabel">
				���������
			</td>
			<td class="editCell">
				<!--<textarea  name="message" rows="8" cols="55"></textarea>
				<input type="class" name="message" class="CTinyMCETextareaGridDataField"/>-->
				<?php CRenderer::RenderControl("content"); ?>
			</td>
		</tr>
		<tr>
			<td id="trfiles" valign="top"  class="itemLabel">
				<div ID="fileslabel">�������� �����(1)</div>
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
				<input type="submit" value="���������">
			</td>
			<td>
				<input type="reset" value="�������� ����">
			</td>
		</tr>
	</table>
	<input id="sendedHiddenField" type="hidden" name="sended" value="true" />

</form>
<?php

    }
    else {
    
    
        $boundary = md5(rand(0,65535));
        
        $subject = "=?windows-1251?b?" . base64_encode($_POST["subject"]) . "?=";
    
        $add_header = "From: $_POST[from]
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
                
        if ($_POST["to"]!="") {
            mail($_POST["to"],$subject,$body1.$body2,$add_header);
            ?>��������� ���������<?php
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

?>
</div>
</body>
</html>
