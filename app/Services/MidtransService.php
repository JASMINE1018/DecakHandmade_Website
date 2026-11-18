<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;
    private bool $isProduction;
    private string $baseUrl;
    private Client $httpClient;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key', '');
        $this->isProduction = (bool) config('midtrans.is_production', false);
        $this->baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $caBundle = base_path('vendor/midtrans/midtrans-php/data/cacert.pem');
        $verify = file_exists($caBundle) ? $caBundle : ! app()->isLocal();

        $this->httpClient = new Client([
            'timeout' => 20,
            'http_errors' => false,
            'verify' => $verify,
        ]);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->serverKey);
    }

    /**
     * Membuat charge pembayaran QRIS via Midtrans Core API.
     *
     * @return array{
     *     status: string,
     *     qris_url?: ?string,
     *     raw: array<string, mixed>
     * }
     */
    public function chargeQris(Order $order): array
    {
        if (! $this->isConfigured()) {
            return [
                'status' => 'pending',
                'qris_url' => null,
                'raw' => ['note' => 'Midtrans server key is not configured.'],
            ];
        }

        $grossAmount = (int) $order->grand_total;

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->customer?->name ?? 'Pelanggan Decak',
                'phone' => $order->customer?->phone ?? '',
                'email' => $order->customer?->email ?? '',
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
            'custom_expiry' => [
                'expiry_duration' => 5,
                'unit' => 'minute',
            ],
        ];

        try {
            $response = $this->httpClient->post($this->baseUrl . '/charge', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
                ],
                'json' => $params,
            ]);

            $body = json_decode((string) $response->getBody(), true) ?? [];
            $actions = $body['actions'] ?? [];
            $qrisUrl = null;

            foreach ($actions as $action) {
                if (in_array($action['name'] ?? '', ['generate-qr-code', 'generate-qr-code-v2'], true)) {
                    $qrisUrl = $action['url'] ?? null;
                    if ($action['name'] === 'generate-qr-code') {
                        break;
                    }
                }
            }

            if (! $qrisUrl && ! empty($body['qr_string'])) {
                $qrisUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($body['qr_string']);
            }

            // Jika order sudah pernah dibuat di Midtrans (status 406 duplicate)
            if (! $qrisUrl && isset($body['status_code']) && (string) $body['status_code'] === '406') {
                $statusRes = $this->getStatus($order->order_number);
                if (! empty($statusRes['transaction_id'])) {
                    $qrisUrl = $this->baseUrl . '/qris/' . $statusRes['transaction_id'] . '/qr-code';
                    return [
                        'status' => $statusRes['transaction_status'] ?? 'pending',
                        'qris_url' => $qrisUrl,
                        'raw' => $statusRes,
                    ];
                }
            }

            return [
                'status' => $body['transaction_status'] ?? 'pending',
                'qris_url' => $qrisUrl,
                'raw' => $body,
            ];
        } catch (Exception $e) {
            Log::error('Midtrans QRIS Charge Error: ' . $e->getMessage(), ['order' => $order->order_number]);

            return [
                'status' => 'pending',
                'qris_url' => null,
                'raw' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Membuat charge pembayaran Bank Transfer (Virtual Account) via Midtrans.
     *
     * @return array{
     *     status: string,
     *     bank: string,
     *     va_number: ?string,
     *     raw: array<string, mixed>
     * }
     */
    public function chargeBankTransfer(Order $order, string $bank = 'bca'): array
    {
        if (! $this->isConfigured()) {
            $fallback = config('midtrans.manual_bank');

            return [
                'status' => 'pending',
                'bank' => $fallback['bank_name'] ?? 'BCA',
                'va_number' => $fallback['account_number'] ?? '1234567890',
                'raw' => ['note' => 'Midtrans server key not set, fallback to manual bank transfer.'],
            ];
        }

        $grossAmount = (int) $order->grand_total;
        $bank = strtolower($bank);

        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->customer?->name ?? 'Pelanggan Decak',
                'phone' => $order->customer?->phone ?? '',
                'email' => $order->customer?->email ?? '',
            ],
            'bank_transfer' => [
                'bank' => $bank,
            ],
        ];

        try {
            $response = $this->httpClient->post($this->baseUrl . '/charge', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
                ],
                'json' => $params,
            ]);

            $body = json_decode((string) $response->getBody(), true) ?? [];
            $vaNumber = null;

            if (! empty($body['va_numbers'][0]['va_number'])) {
                $vaNumber = $body['va_numbers'][0]['va_number'];
            } elseif (! empty($body['permata_va_number'])) {
                $vaNumber = $body['permata_va_number'];
            }

            return [
                'status' => $body['transaction_status'] ?? 'pending',
                'bank' => strtoupper($bank),
                'va_number' => $vaNumber,
                'raw' => $body,
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Bank Transfer Charge Error: ' . $e->getMessage(), ['order' => $order->order_number]);
            $fallback = config('midtrans.manual_bank');

            return [
                'status' => 'pending',
                'bank' => $fallback['bank_name'] ?? 'BCA',
                'va_number' => $fallback['account_number'] ?? '1234567890',
                'raw' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Cek status transaksi di Midtrans.
     *
     * @return array<string, mixed>
     */
    public function getStatus(string $orderId): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = $this->httpClient->get($this->baseUrl . '/' . $orderId . '/status', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
                ],
            ]);

            return json_decode((string) $response->getBody(), true) ?? [];
        } catch (Exception $e) {
            Log::error('Midtrans Get Status Error: ' . $e->getMessage(), ['order_id' => $orderId]);

            return [];
        }
    }
}
