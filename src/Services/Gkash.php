<?php

namespace Jiannius\Atom\Services;

use Illuminate\Support\Facades\Http;

class Gkash
{
    /**
     * Get Gkash request data
     *
     * @param array $payload
     * @return array
     */
    public static function getRequestData($payload = [])
    {
        if (!$payload) return null;

        $currency = $payload['currency'] ?? 'MYR';

        $signature = implode(';', [
            $payload['signature'],
            $payload['mid'],
            $payload['cartid'],
            number_format($payload['amount'], 2, '', ''),
            $currency,
        ]);

        return (object)[
            'data' => [
                'endpoint' => env('GKASH_ENDPOINT'),
                'version' => env('GKASH_VERSION'),
                'CID' => $payload['mid'],
                'signature' => hash('sha512', strtoupper($signature)),
                'v_currency' => $currency,
                'v_amount' => number_format($payload['amount'], 2, '.', ''),
                'v_cartid' => $payload['cartid'],
                'preselection' => self::getPreselection($payload['mode'] ?? null),
                'returnurl' => route('gkash.redirect', $payload['params'] ?? []),
                'callbackurl' => route('gkash.webhook', $payload['params'] ?? []),
            ],
            'form' => trim("
                <form method=\"POST\" action=\"" . env('GKASH_ENDPOINT') . "PaymentForm.aspx\" style=\"height: 0px; overflow: hidden;\">
                    <input type=\"text\" name=\"version\"       value=\"" . env('GKASH_VERSION') . "\">
                    <input type=\"text\" name=\"CID\"           value=\"" . $payload['mid'] . "\">
                    <input type=\"text\" name=\"signature\"     value=\"" . hash('sha512', strtoupper($signature)) . "\">
                    <input type=\"text\" name=\"v_currency\"    value=\"" . $currency . "\">
                    <input type=\"text\" name=\"v_amount\"      value=\"" . number_format($payload['amount'], 2, '.', '') . "\">
                    <input type=\"text\" name=\"v_cartid\"      value=\"" . $payload['cartid'] . "\">
                    <input type=\"text\" name=\"preselection\"  value=\"" . self::getPreselection($payload['mode'] ?? null) . "\">
                    <input type=\"text\" name=\"returnurl\"     value=\"" . route('gkash.redirect', $payload['params'] ?? []) . "\">
                    <input type=\"text\" name=\"callbackurl\"   value=\"" . route('gkash.webhook', $payload['params'] ?? []) . "\">
                </form>
            "),
        ];
    }

    /**
     * Verify response signature
     *
     * @param array $response
     * @param array $payload
     * @return boolean
     */
    public static function verifyResponseSignature($response, $payload = [])
    {
        if (!$payload) return false;

        $signature = implode(';', [
            $payload['key'],
            $payload['cid'],
            $response['POID'] ?? '',
            $payload['cartid'],
            number_format($payload['amount'], 2, '', ''),
            'MYR',
            $response['status'] ?? '',
        ]);

        $hash = hash('sha512', strtoupper($signature));

        if ($hash === ($response['signature'] ?? '')) return true;

        abort(401);
    }

    /**
     * Translate status from Gkash
     *
     * @param string $status
     * @return string
     */
    public static function translateStatus($status)
    {
        $translations = [
            '88 - Transferred' => 'success',
            '66 - Failed' => 'failed',
            '11 - Pending' => 'pending',
            '99 - Error' => 'error',
        ];

        return $translations[$status] ?? 'pending';
    }

    /**
     * Get preselection
     * 
     * @param string $paymode
     * @return string
     */
    public static function getPreselection($paymode = null)
    {
        $modes = [
            'fpx' => 'EBANKING',
            'ewallet' => 'EWALLET',
            'credit-card' => 'ECOMM',
        ];

        return $modes[$paymode] ?? 'EWALLET';
    }
}
