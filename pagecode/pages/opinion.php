<?php
class opinion_php extends CPageCodeHandler
{

	public function opinion_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$av_rwParams = array();
		CURLHandler::CheckRewriteParams($av_rwParams);  
		
		if ($_POST["sended"]=="sended") {
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['captcha'] && $_POST['name']!="" && $_POST['email']!=""){
			
				$addform['comm_begin_if_ok'] = '<!--';
				$addform['comm_end_if_ok'] = '-->';
				$addform['comm_begin_if_notok'] = '';
				$addform['comm_end_if_notok'] = '';
				
				$text = "<table border=1>";
				
				$text .= "<tr><td>���� ���*</td><td>".$_POST['name']."</td></tr>";
				$text .= "<tr><td>���� ���������</td><td>".$_POST['position']."</td></tr>";
				$text .= "<tr><td>����� �������� �� �������������</td><td>".$_POST['company']."</td></tr>";
				$text .= "<tr><td>�����</td><td>".$_POST['city']."</td></tr>";
				$text .= "<tr><td>E-mail*</td><td>".$_POST['email']."</td></tr>";
				$text .= "<tr><td>�������</td><td>".$_POST['phone']."</td></tr>";
				$text .= "<tr><td>���������������� �� �� �� �����?</td><td>".$_POST['registered']."</td></tr>";
				$text .= "<tr><td>���������������� �� ���� ��������/������ �� �����?</td><td>".$_POST['reg_comp']."</td></tr>";
				$text .= "<tr><td>��� �� ������ � �������?</td><td>".$_POST['how_knew']."</td></tr>";
				$text .= "<tr><td>�������� �� �� ��� � ����� ������?</td><td>".$_POST['helps']."</td></tr>";
				$text .= "<tr><td>�������� �� �� ��� ������� � ��������?</td><td>".$_POST['useful']."</td></tr>";
				$text .= "<tr><td>����� ������ �� ��������� �������� �����?</td><td>".$_POST['partition']."</td></tr>";
				$text .= "<tr><td>����� ��� ������� ��� ���������?</td><td>".$_POST['else_part']."</td></tr>";
				$text .= "<tr><td>��� �� �������� ������ �� �����?</td><td>".$_POST['unnecessary']."</td></tr>";
				$text .= "<tr><td>���� �� ������� �� ������ ������?</td><td>".$_POST['whatneed']."</td></tr>";
				$text .= "<tr><td>��������� �� ��� ������ ������ �������������?</td><td>".$_POST['comments']."</td></tr>";
				$text .= "<tr><td>�� ��������� ������ � ���������, � �������� ��������?</td><td>".$_POST['diducomment']."</td></tr>";
				$text .= "<tr><td>���������� �� ��� ������������� ������� �������?</td><td>".$_POST['interactive']."</td></tr>";
				$text .= "<tr><td>������� �� �� ��������� ������� � ������ � event-�����?</td><td>".$_POST['games']."</td></tr>";
				$text .= "<tr><td>������� �� �� �� ������������ �� �����?</td><td>".$_POST['news']."</td></tr>";
				$text .= "<tr><td>���� �� � ��� ����� ��������� � ������ ������ ��������� ��� �������?</td><td>".$_POST['wishes']."</td></tr>";
				$text .= "<tr><td>������� �� �� �������� �������� �������� ������� (�� ���� 1 ���� � �����)?</td><td>".$_POST['subscribe']."</td></tr>";
				
				$text .= "</table>";
				
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";
				
				// Additional headers
				$headers .= 'development@eventcatalog.ru' . "\r\n";
				$headers .= 'From: development@eventcatalog.ru' . "\r\n";
				
				mail('development@eventcatalog.ru','���� ������ (����������� ������ ������������)',$text,$headers);
			
			
			}else{
				$addform['name'] = $_POST['name'];
				$addform['position'] = $_POST['position'];
				$addform['company'] = $_POST['company'];
				$addform['city'] = $_POST['city'];
				$addform['email'] = $_POST['email'];
				$addform['phone'] = $_POST['phone'];
				$addform['registered'] = $_POST['registered'];
				$addform['reg_comp'] = $_POST['reg_comp'];
				$addform['how_knew'] = $_POST['how_knew'];
				$addform['helps'] = $_POST['helps'];
				$addform['useful'] = $_POST['useful'];
				$addform['partition'] = $_POST['partition'];
				$addform['else_part'] = $_POST['else_part'];
				$addform['unnecessary'] = $_POST['unnecessary'];
				$addform['whatneed'] = $_POST['whatneed'];
				$addform['comments'] = $_POST['comments'];
				$addform['diducomment'] = $_POST['diducomment'];
				$addform['interactive'] = $_POST['interactive'];
				$addform['games'] = $_POST['games'];
				$addform['news'] = $_POST['news'];
				$addform['wishes'] = $_POST['wishes'];
				$addform['subscribe'] = $_POST['subscribe'];
				$addform['comm_begin_if_ok'] = '';
				$addform['comm_end_if_ok'] = '';
				$addform['comm_begin_if_notok'] = '<!--';
				$addform['comm_end_if_notok'] = '-->';
			}
		}
		else {
			$addform['name'] = "";
			$addform['position'] = "";
			$addform['company'] = "";
			$addform['city'] = "";
			$addform['email'] = "";
			$addform['phone'] = "";
			$addform['registered'] = "";
			$addform['reg_comp'] = "";
			$addform['how_knew'] = "";
			$addform['helps'] = "";
			$addform['useful'] = "";
			$addform['partition'] = "";
			$addform['else_part'] = "";
			$addform['unnecessary'] = "";
			$addform['whatneed'] = "";
			$addform['comments'] = "";
			$addform['diducomment'] = "";
			$addform['interactive'] = "";
			$addform['games'] = "";
			$addform['news'] = "";
			$addform['wishes'] = "";
			$addform['subscribe'] = "";
			$addform['comm_begin_if_ok'] = '';
			$addform['comm_end_if_ok'] = '';
			$addform['comm_begin_if_notok'] = '<!--';
			$addform['comm_end_if_notok'] = '-->';
		}

		$groupList= $this->GetControl("addform");
		$groupList->dataSource = $addform;

	}
}
?>
