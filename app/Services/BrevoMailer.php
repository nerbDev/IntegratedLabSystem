<?php

namespace App\Services;

use Brevo\Brevo;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;

class BrevoMailer
{
    protected Brevo $client;

    public function __construct()
    {
        $this->client = new Brevo(apiKey: env('BREVO_API_KEY'));
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlContent): void
    {
        $request = new SendTransacEmailRequest([
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'sender' => new SendTransacEmailRequestSender([
                'email' => env('MAIL_FROM_ADDRESS'),
                'name' => env('MAIL_FROM_NAME'),
            ]),
            'to' => [
                new SendTransacEmailRequestToItem([
                    'email' => $toEmail,
                    'name' => $toName,
                ]),
            ],
        ]);

        $this->client->transactionalEmails->sendTransacEmail($request);
    }
}