<?php
require_once __DIR__ . '/../config/config.php';

class Email {

    public static function sendPasswordReset($email, $resetToken) {
        $resetUrl = SITE_URL . "/frontend/public/reset-password.html?token=" . $resetToken;

        $subject = "Password Reset - Mixlar Marketplace";

        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Password Reset Request</h1>
                </div>
                <div class="content">
                    <p>Hi there,</p>
                    <p>You requested to reset your password for your Mixlar Marketplace account.</p>
                    <p>Click the button below to reset your password. This link will expire in 1 hour.</p>
                    <div style="text-align: center;">
                        <a href="' . $resetUrl . '" class="button">Reset Password</a>
                    </div>
                    <p>If you didn\'t request this, please ignore this email and your password will remain unchanged.</p>
                    <p>For security reasons, this link will expire in 1 hour.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2024 MixlarLabs. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">" . "\r\n";

        return mail($email, $subject, $message, $headers);
    }

    public static function sendWelcome($email, $username) {
        $subject = "Welcome to Mixlar Marketplace";

        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to Mixlar Marketplace!</h1>
                </div>
                <div class="content">
                    <p>Hi ' . htmlspecialchars($username) . ',</p>
                    <p>Welcome to the Mixlar Plugin Marketplace! We\'re excited to have you join our community.</p>
                    <p>You can now:</p>
                    <ul>
                        <li>Browse and discover amazing plugins</li>
                        <li>Submit your own plugins for approval</li>
                        <li>Connect with other developers</li>
                    </ul>
                    <div style="text-align: center;">
                        <a href="' . SITE_URL . '" class="button">Explore Marketplace</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">" . "\r\n";

        return mail($email, $subject, $message, $headers);
    }
}
