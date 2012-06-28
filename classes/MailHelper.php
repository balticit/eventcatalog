<?php
class MailHelper {
    
    public $subject;
    
    public $from = "dontreply@eventcatalog.ru";
    
    public $to;
    
    public $body;
    
    public function send() {
        
        $boundary = md5(rand(0,65535));
        
        $mailsubject = "=?windows-1251?b?" . base64_encode($this->subject) . "?=";
    
        $headers = "From: ".$this->from."
Reply-To: ".$this->from."
MIME-Version: 1.0
Content-Type: multipart/mixed; charset=\"windows-1251\"; boundary=\"$boundary\";
Content-Transfer-Encoding: 8bit\n";
                        
        $mailbody = "--$boundary
Content-Type: text/html; charset=\"windows-1251\"
Content-Transfer-Encoding: 8bit

".$this->body;

        mail($this->to,$mailsubject,$mailbody,$headers);
    }
}
