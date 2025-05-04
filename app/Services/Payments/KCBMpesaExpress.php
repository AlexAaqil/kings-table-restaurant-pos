<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class KCBMpesaExpress
{
    protected $base_url = 'https://uat.buni.kcbgroup.com';
    protected $consumer_key;
    protected $consumer_secret;
    protected $org_short_code;
    protected $org_pass_key;
    protected $logger;

    public function __construct()
    {
        $this->consumer_key = env('KCB_CONSUMER_KEY');
        $this->consumer_secret = env('KCB_CONSUMER_SECRET');
        $this->org_short_code = env('KCB_ORG_SHORT_CODE');
        $this->org_pass_key = env('KCB_ORG_PASS_KEY');
        $this->logger = Log::channel('kcb-mpesa');
    }

    public function getToken()
    {
        try {
            $credentials = base64_encode("{$this->consumer_key}:{$this->consumer_secret}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post("{$this->base_url}/token?grant_type=client_credentials");

            $this->logger->info('Token Response', ['body' => $response->body()]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            $this->logger->error('Failed to retrieve the token', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Authorization failed while retrieving token.');
        } catch(Exception $e) {
            $this->logger->critical('Token exception', ['message' => $e->getmessage()]);
            throw $e;
        }
    }

    public function stkPush($phone, $amount, $invoice)
    {
        try {
            $token = $this->getToken();

            $payload = [
                'phoneNumber' => $this->formatPhone($phone),
                'amount' => $amount,
                'invoiceNumber' => $invoice,
                'sharedShortCode' => false,
                'orgShortCode' => $this->org_short_code,
                'orgPassKey' => $this->org_pass_key,
                'callbackUrl' => route('api.payment.callback'),
                'transactionDescription' => 'Payment for Order #' . $invoice,
            ];

            $this->logger->info('STK Payload', $payload);

            $response = Http::withToken($token)->post("{$this->base_url}/mm/api/request/1.0.0/stkpush", $payload);

            $this->logger->info('STK Response', ['status' => $response->status(), 'body' => $response->body()]);

            if($response->successful()) {
                return $response->json();
            }

            return [
                'CustomerMessage' => 'Payment initiation failed. Please try again.',
                'error' => true,
                'response' => $response->json(),
            ];
        } catch (Exception $e) {
            $this->logger->critical('STK Push Exception', ['error' => $e->getMessage()]);
            return [
                'CustomerMessage' => 'Something went wrong while trying to process your request',
                'error' => true,
                'exception' => $e->getMessage(),
            ];
        }
    }

    protected function formatPhone($phone)
    {
        return preg_replace('/^0/', '254', $phone);
    }
}
