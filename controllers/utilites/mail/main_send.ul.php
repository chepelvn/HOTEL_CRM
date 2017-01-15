<?php
/**
* SendMailSmtpClass
*
* Класс для отправки писем через SMTP с авторизацией
* Может работать через SSL протокол
* Тестировалось на почтовых серверах yandex.ru, mail.ru и gmail.com
*
* @author Ipatov Evgeniy <admin@ipatov-soft.ru>
* @version 1.0
*/
class mail_send {

    /**
    *
    * @var string $smtp_username - логин
    * @var string $smtp_password - пароль
    * @var string $smtp_host - хост
    * @var string $smtp_from - от кого
    * @var integer $smtp_port - порт
    * @var string $smtp_charset - кодировка
    *
    */
    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;

    public function __construct($smtp_username = null, $smtp_password = null, $smtp_host = null, $smtp_port = 25, $smtp_from = null, $smtp_from_name = null, $smtp_charset = "utf-8") {
        $this->smtp_username = $smtp_username;
        $this->smtp_password = $smtp_password;
        $this->smtp_host = $smtp_host;
        $this->smtp_port = $smtp_port;
        $this->smtp_charset = $smtp_charset;

        $this->from_mail = $smtp_from;
        $this->from_name = $smtp_from_name;

    }

    static public function getIstance($arguments = null){    	return new SendMail();
    }

    public function sett($object = null, $from_name = "", $from_mail = ""){        $this->smtp_username = $object['user_name'];
        $this->smtp_password = $object['password'];
        $this->smtp_host = $object['host'];
        $this->smtp_port = $object['port'];
        $this->smtp_charset = (isset($object['charset'])) ? $object['charset'] : $this->smtp_charset;

        $this->from_name = $from_name;
        $this->from_mail = $from_mail;
        return $this;
    }

    /**
    * Отправка письма
    *
    * @param string $mailTo - получатель письма
    * @param string $subject - тема письма
    * @param string $message - тело письма
    * @param string $headers - заголовки письма
    *
    * @return bool|string В случаи отправки вернет true, иначе текст ошибки    *
    */
    private function getHeadersFiles($files = null, $boundary = ""){        $files = (!is_array($files)) ? array($files) : $files;
        $file = "";

        foreach($files as $key => $value){
            $value = (object)$value;
            $value->name = (!$value->name) ? "File_" . $key : $value->name;
        	if (isset($value->file) && file_exists($value->file)) {
			    $fp = fopen($value->file, "r");
			    if($fp) {
			      $content = fread($fp, filesize($value->file));
			      fclose($fp);
			      $file .= "--".$boundary."\r\n";
			      $file .= "Content-Type: application/octet-stream\r\n";
			      $file .= "Content-Transfer-Encoding: base64\r\n";
			      $file .= "Content-Disposition: attachment; filename=\"".$value->name."\"\r\n\r\n";
			      $file .= chunk_split(base64_encode($content))."\r\n";
			    }
			}
        }

        return $file;
    }

    function send($mailTo, $subject, $message, $files = null) {
		$boundary = "--".md5(uniqid(time())); // генерируем разделитель
		$headers = "FROM: ".$this->from_name." <".$this->from_mail.">\r\n";
		$headers .= "Return-path: <".$this->from_mail.">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";

		$multipart = "--".$boundary."\r\n";
		$multipart .= "Content-type: text/plain; charset=\"".$this->smtp_charset."\"\r\n";
		$multipart .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";

		$multipart .= $message . "\r\n\r\n";

        if(isset($files) && $file = self::getHeadersFiles($files, $boundary)){        	$multipart .= $file."--".$boundary."--\r\n";
        }

        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?'  . base64_encode($subject) . "=?=\r\n";
        $contentMail .= iconv($this->smtp_charset, 'windows-1251', $headers) . "\r\n";
        $contentMail .= $multipart . "\r\n";

        try {
            if(!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)){
                throw new Exception($errorNumber.".".$errorDescription);
            }
            if (!$this->_parseServer($socket, "220")){
                throw new Exception('Connection error');
            }

			$server_name = $_SERVER["SERVER_NAME"];
            fputs($socket, "HELO $server_name\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: HELO');
            }

            fputs($socket, "AUTH LOGIN\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }



            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            if (!$this->_parseServer($socket, "235")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, "MAIL FROM: <".$this->smtp_username.">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }

			$mailTo = ltrim($mailTo, '<');
			$mailTo = rtrim($mailTo, '>');
            fputs($socket, "RCPT TO: <" . $mailTo . ">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: RCPT TO');
            }

            fputs($socket, "DATA\r\n");
            if (!$this->_parseServer($socket, "354")) {
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
            }

            fputs($socket, $contentMail."\r\n.\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception("E-mail didn't sent");
            }

            fputs($socket, "QUIT\r\n");
            fclose($socket);
        } catch (Exception $e) {
            return  $e->getMessage();
        }
        return $contentMail;
    }

    private function _parseServer($socket, $response) {
        while (@substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;

    }
}
?>