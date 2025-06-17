<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function send_mail($to, $subject, $body) {
    // Get email configuration
    $config = require __DIR__ . '/../config/mail.php';
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Enable debug output and log to string
        $mail->SMTPDebug = 3; // Debug level: 0=off, 1=client, 2=client/server, 3=client/server/connection
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug: $str");
        };
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];
        
        // Set timeout options
        $mail->Timeout = 30; // Timeout in seconds
        
        // Set additional SMTP options if provided
        if (isset($config['smtp_options']) && is_array($config['smtp_options'])) {
            $mail->SMTPOptions = $config['smtp_options'];
        }
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));
        
        // Send email
        $result = $mail->send();
        error_log("Email sent successfully to: $to");
        return $result;
    } catch (Exception $e) {
        // Log detailed error information
        error_log("Email Error: " . $mail->ErrorInfo);
        error_log("SMTP Debug: " . print_r($mail->SMTPDebug, true));
        error_log("Host: " . $config['host']);
        error_log("Port: " . $config['port']);
        error_log("Username: " . $config['username']);
        error_log("From Email: " . $config['from_email']);
        error_log("To Email: " . $to);
        error_log("Exception Message: " . $e->getMessage());
        error_log("Exception Trace: " . $e->getTraceAsString());
        return false;
    }
}

function send_reset_password_email($to, $reset_link) {
    $subject = "Reset Password - Wshooes";
    
    // Create HTML email body
    $body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #3f51b5;">Reset Password Wshooes</h2>
        <p>Anda telah meminta untuk mereset password akun Wshooes Anda.</p>
        <p>Klik tombol di bawah ini untuk mereset password Anda:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="' . $reset_link . '" 
               style="background-color: #3f51b5; 
                      color: white; 
                      padding: 12px 25px; 
                      text-decoration: none; 
                      border-radius: 5px; 
                      display: inline-block;">
                Reset Password
            </a>
        </p>
        <p>Atau copy dan paste link berikut ke browser Anda:</p>
        <p style="background-color: #f5f5f5; padding: 10px; word-break: break-all;">
            ' . $reset_link . '
        </p>
        <p><strong>Link ini akan kedaluwarsa dalam 1 jam.</strong></p>
        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="color: #666; font-size: 12px;">
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
        </p>
    </div>';
    
    return send_mail($to, $subject, $body);
}