<?php
    require_once __DIR__ . "/../../../common_files/smtp_mail.php";
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function sendmail1($Host, $Port, $EncriptionType, $checkSmtp, $Username, $Password, $FromMail, $FromName, $ToMail, $subject, $content, $multiEmail = "") {
        date_default_timezone_set('Asia/Tokyo');
        mb_language("ja");

        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $Host;                                  // Set the SMTP server to send through
            $mail->SMTPAuth   = ($checkSmtp == 1) ? true : false;                                   // Enable SMTP authentication
            $mail->Username   = $Username;                              // SMTP username
            $mail->Password   = $Password;                              // SMTP password

            if ($EncriptionType == 1) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }
            elseif ($EncriptionType == 2) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port    = $Port;                                    // TCP port to connect to

            $mail->CharSet = 'UTF-8';

            if (!is_null($FromMail)) {
                // そり蔵の既存のソースが、From: xxxxx\r\n で渡してくるので対応する。
                // From: xxxxx\r\n の形式を分割する。 \sではなく[\p{C}\p{Z}]なのは、改行文字を取り除くため。
                $val = preg_split('/:[\p{C}\p{Z}]?/', $FromMail);
                $fromVal = null;
                if (count($val) == 1) {
                    // From: xxxxx\r\n の形式ではなかった場合
                    $fromVal = $val[0];
                }
                else if (count($val) == 2 && mb_strtolower($val[0]) == "from") {
                    // From: xxxxx\r\n の形式の場合
                    $fromVal = $val[1];
                }

                if (!is_null($fromVal)) {
                    // setFromメソッドは、自動でSPF対応（envelope sender を設定）してくれる。
                    // 参考
                    // https://phpmailer.github.io/PHPMailer/classes/PHPMailer.PHPMailer.PHPMailer.html#method_setFrom
                    // https://phpmailer.github.io/PHPMailer/classes/PHPMailer.PHPMailer.PHPMailer.html#property_Sender

                    // xnamex<xxx@xxx>の形式の場合
                    $test = preg_match( "/(.+?)\<(.+?)\>/", $fromVal, $FromMail );
                    if ( $test ) {
                        $mail->setFrom( $FromMail[2], $FromMail[1] );
                        $mail->addReplyTo($FromMail[2], $FromMail[1]);
                    }
                    else {
                        $mail->setFrom($fromVal, $FromName);
                        $mail->addReplyTo($fromVal, $FromName);
                    }
                }
            }

            // Recipients
            if ( stripos( ",", $ToMail ) !== false ) {
                $toVal = explode( ",", $ToMail );
                foreach ( $toVal as $key => $value ) {
                    // xnamex<xxx@xxx>の形式の場合
                    if ( preg_match( "/(.+?)\<(.+?)\>/", $value, $ToMail ) ) {
                        $mail->addAddress( $ToMail[2], $ToMail[1] );
                    }
                    else {
                        $mail->addAddress($ToMail);
                    }
                }
            }
            else {
                $mail->addAddress($ToMail);
            }

            if ($multiEmail != "") {
                $arrBcc = explode(',', $multiEmail);
                foreach ($arrBcc as $bcc) {
                    $mail->AddBCC($bcc);
                }
            }

            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $content;

            $mail->send();
            return true;
        }
        catch ( Exception $e ) {
            return 'メールを送信できません';
        }
    }
?>
