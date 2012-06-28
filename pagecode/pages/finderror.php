<?php
class finderror_php extends CPageCodeHandler
{

    public function finderror_php()
    {
        $this->CPageCodeHandler();
    }

    public function PreRender()
    {
        $av_rwParams = array();
		    CURLHandler::CheckRewriteParams($av_rwParams);  
        
        $form = $this->GetControl("errorForm");
        $form->dataSource['referer'] = $_SERVER["HTTP_REFERER"];        
        
        $name = GP('firstname');
        if (!is_null($name)) {
            $email = GP('email');
            $page = GP('page');
            $subject = GP('subject');
            $comments = GP('comments');
            
            $mail = new MailHelper();
            $mail->subject = "������� \"����� ������\"";
            $mail->to = "catalog@eventcatalog.ru";
            $mail->body = "<html>
<body style=\"font-family:Arial;font-size:10pt;line-height:11pt;\">
<div style=\"font-family:Arial;font-size:10pt;line-height:11pt;\">
������������ �������� �� ������:<br/><br/>
���: $name<br/>
E-mail: <a href=\"mailto:$email\">$email</a><br/>
��������: <a href=\"$page\">$page</a><br/>
���� ���������: $subject<br/>
�����������: $comments
</div>
</body>
</html>";
            $mail->send();
            
            $form = $this->GetControl("errorForm");
            $form->key = "thanks";
        }
    }
}