<?php

namespace App\Mail;

use App\Models\Configurator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class MailService
{
    protected CommonMailer $mailer;
    protected string $view;
    protected string $subject;
    protected string $recipients;
    protected string $mailKey;
    protected function send(array $data, ?array $files = []): bool
    {
        $this->mailer = new CommonMailer($data, $this->view);
        $this->mailer->subject = $this->subject;

        if (!empty($files)) {
            /** @var UploadedFile $file */
            foreach ($files as $file) {
                $this->mailer->attachData($file->get(), $file->getClientOriginalName());
            }
        }

        Mail::to($this->recipients)->queue($this->mailer);
        return true;
    }

    public function sendMail(array $data, string $subject, string $letterView, string|array $sendTo, ?array $files = []): bool
    {
        $this->view = $letterView;
        $this->subject = $subject;
        $this->recipients = $sendTo;
        return $this->send($data, $files);
    }

    public function sendManagerMail(array $data, string $subject, string $letterView, ?array $files = [])
    {
        $this->view = $letterView;
        $this->subject = $subject;
        $this->recipients = Configurator::query()->where('key', 'email_forms')->first()->value;

        return $this->send($data, $files);

    }
}
