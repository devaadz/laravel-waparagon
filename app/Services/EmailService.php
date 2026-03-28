<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = config('mail.host', env('MAIL_HOST', 'smtp.gmail.com'));
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = config('mail.username', env('MAIL_USERNAME'));
        $this->mailer->Password = config('mail.password', env('MAIL_PASSWORD'));
        $this->mailer->SMTPSecure = config('mail.encryption', env('MAIL_ENCRYPTION', 'tls'));
        $this->mailer->Port = config('mail.port', env('MAIL_PORT', 587));

        // Default sender
        $this->mailer->setFrom(
            config('mail.from.address', env('MAIL_FROM_ADDRESS')),
            config('mail.from.name', env('MAIL_FROM_NAME', 'WA Paragon'))
        );
    }

    /**
     * Send email notification for form submission
     */
    public function sendFormSubmissionNotification(array $data): bool
    {
        try {
            // Reset mailer for new email
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Recipients
            $this->mailer->addAddress($data['to']);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $data['subject'];
            $this->mailer->Body = $data['body'];
            $this->mailer->AltBody = strip_tags($data['body']);

            $result = $this->mailer->send();

            Log::info('Email sent successfully', [
                'to' => $data['to'],
                'subject' => $data['subject']
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Email sending failed', [
                'to' => $data['to'] ?? 'unknown',
                'subject' => $data['subject'] ?? 'unknown',
                'error' => $this->mailer->ErrorInfo,
                'exception' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Replace template variables
     */
    public function replaceTemplateVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
}