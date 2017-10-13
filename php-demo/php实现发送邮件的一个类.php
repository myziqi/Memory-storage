<?php
class emailui
{
  static function runlog($mode = 'SMTP',$b = '',$c = '',$d='') {

  }
  static function sendmail($toemail, $subject, $message, $from='',$cfg = array(), $sitename='') {
    global $db_wwwname,$mail_port,$mail_id,$mail_server,$mail_pw,$mail_type,$db_charset,$version,$smtpfrom;

    $sitename = $sitename ? $sitename : $db_wwwname;
    $adminemail = $mail_id ? $mail_id : 'admin@54ui.com';

    if($cfg && is_array($cfg)) {
      $adminemail = $cfg['from'];
      $mail_type = $cfg['mail_type'];  //邮件发送模式
      $mail = $cfg;
    } else {
      $adminemail = $mail_id;
      $mail_type  = $mail_type; //邮件发送模式
      #端口,邮件头的分隔符,收件人地址中包含用户名
      $mail= Array (
        'mailsend' => 2,
        'maildelimiter' => 1,
        'mailusername' => 1,
        'server' => $mail_server,       #服务器
        'port' => $mail_port,           #端口
        'auth' => $mail_id,             #作者
        'from' => $mail_id,             #发信者
        'auth_username' => $mail_id,    #账号
        'auth_password' => $mail_pw     #密码
      );
    }
    //mail 发送模式
    if(!$mail_type) {
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset='.$db_charset.'' . "\r\n";
      $headers .= 'From: '.$sitename.' <'.$from.'>' . "\r\n";
      mail($toemail, $subject, $message, $headers);
      return true;
    }
    //邮件头的分隔符
    $maildelimiter = $mail['maildelimiter'] == 1 ? "\r\n" : ($mail['maildelimiter'] == 2 ? "\r" : "\n");
    //收件人地址中包含用户名
    $mailusername = isset($mail['mailusername']) ? $mail['mailusername'] : 1;
    //端口
    $mail['port'] = $mail['port'] ? $mail['port'] : 25;
    $mail['mailsend'] = $mail['mailsend'] ? $mail['mailsend'] : 1;
    //发信者
    $email_from = $from == '' ? '=?'.$db_charset.'?B?'.base64_encode($sitename)."?= <".$adminemail.">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?'.$db_charset.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
    //收信人
    $email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.$db_charset.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;;
    //邮件标题
    $email_subject = '=?'.$db_charset.'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', $subject.'--'.$db_wwwname)).'?=';
    //邮件内容
    $message = $message."<span style=display:none>Published at  ".gmdate("Y-m-d h:i:s",mktime()+8*3600)." , Powered By uicms ".$version." (service.54ui.com)</span>";
    $email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));

    $headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: abaoei cms {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=".$db_charset."{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";

    if(!$fp = fsockopen($mail['server'], $mail['port'], $errno, $errstr, 30)) {
      self::runlog('SMTP', "($mail[server]:$mail[port]) CONNECT - Unable to connect to the SMTP server", 0);
      return false;
    }
    stream_set_blocking($fp, true);

    $lastmessage = fgets($fp, 512);
    if(substr($lastmessage, 0, 3) != '220') {
      self::runlog('SMTP', "$mail[server]:$mail[port] CONNECT - $lastmessage", 0);
      return false;
    }

    fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO')." uchome\r\n");
    $lastmessage = fgets($fp, 512);
    if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
      self::runlog('SMTP', "($mail[server]:$mail[port]) HELO/EHLO - $lastmessage", 0);
      return false;
    }

    while(1) {
      if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
        break;
      }
      $lastmessage = fgets($fp, 512);
    }

    if($mail['auth']) {
      fputs($fp, "AUTH LOGIN\r\n");
      $lastmessage = fgets($fp, 512);
      if(substr($lastmessage, 0, 3) != 334) {
        self::runlog('SMTP', "($mail[server]:$mail[port]) AUTH LOGIN - $lastmessage", 0);
        return false;
      }

      fputs($fp, base64_encode($mail['auth_username'])."\r\n");
      $lastmessage = fgets($fp, 512);
      if(substr($lastmessage, 0, 3) != 334) {
        self::runlog('SMTP', "($mail[server]:$mail[port]) USERNAME - $lastmessage", 0);
        return false;
      }

      fputs($fp, base64_encode($mail['auth_password'])."\r\n");
      $lastmessage = fgets($fp, 512);
      if(substr($lastmessage, 0, 3) != 235) {
        self::runlog('SMTP', "($mail[server]:$mail[port]) PASSWORD - $lastmessage", 0);
        return false;
      }

      $email_from = $mail['from'];
    }

    fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
    $lastmessage = fgets($fp, 512);
    if(substr($lastmessage, 0, 3) != 250) {
      fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
      $lastmessage = fgets($fp, 512);
      if(substr($lastmessage, 0, 3) != 250) {
        self::runlog('SMTP', "($mail[server]:$mail[port]) MAIL FROM - $lastmessage", 0);
        return false;
      }
    }

    fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
    $lastmessage = fgets($fp, 512);
    if(substr($lastmessage, 0, 3) != 250) {
      fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
      $lastmessage = fgets($fp, 512);
      self::runlog('SMTP', "($mail[server]:$mail[port]) RCPT TO - $lastmessage", 0);
      return false;
    }

    fputs($fp, "DATA\r\n");
    $lastmessage = fgets($fp, 512);
    if(substr($lastmessage, 0, 3) != 354) {
      self::runlog('SMTP', "($mail[server]:$mail[port]) DATA - $lastmessage", 0);
      return false;
    }

    $headers .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";

    fputs($fp, "Date: ".gmdate('r')."\r\n");
    fputs($fp, "To: ".$email_to."\r\n");
    fputs($fp, "Subject: ".$email_subject."\r\n");
    fputs($fp, $headers."\r\n");
    fputs($fp, "\r\n\r\n");
    fputs($fp, "$email_message\r\n.\r\n");
    $lastmessage = fgets($fp, 512);
    if(substr($lastmessage, 0, 3) != 250) {
      self::runlog('SMTP', "($mail[server]:$mail[port]) END - $lastmessage", 0);
    }
    fputs($fp, "QUIT\r\n");
    return true;
  }
}