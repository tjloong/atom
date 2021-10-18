<?php

namespace Jiannius\Atom\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class EasyParcel
{
    /**
     * Test API key
     * 
     * @return boolean
     */
    public static function test($api)
    {
        if (!$api) abort(422, 'API key is missing');
        
        $req = self::sendRequest([
            'api' => $api,
            'action' => 'EPCheckCreditBalance',
            'method' => 'POST',
        ]);

        return $req ? true : false;
    }

    /**
     * Get rates
     * 
     * @return array
     */
    public static function getRates($payload)
    {
        $res = self::sendRequest([
            'api' => $payload['api'] ?? null,
            'method' => 'POST',
            'action' => 'EPRateCheckingBulk',
            'body' => [
                'bulk' => [
                    [
                        'pick_code' => $payload['from']['postcode'] ?? '',
                        'pick_state' => self::getStateCode($payload['from']['state'] ?? ''),
                        'pick_country' => 'MY',
                        'send_code' => $payload['to']['postcode'] ?? '',
                        'send_state' => self::getStateCode($payload['to']['state'] ?? ''),
                        'send_country' => 'MY',
                        'weight' => (float)($payload['weight'] ?? 0),
                    ],
                ],
            ],
        ]);

        return $res['result'][0]['rates'];
    }

    /**
     * Create easyparcel order
     *
     * @param array $payload
     * @return array
     */
    public static function createOrder($payload)
    {
        $data = array_merge(
            [
                'weight' => $payload['weight'] ?? null,
                'content' => $payload['content'] ?? null,
                'value' => $payload['value'] ?? null,
                'service_id' => $payload['service_id'] ?? null,
                'pick_point' => $payload['sender_point'] ?? null,
                'send_point' => $payload['receiver_point'] ?? null,
                'collect_date' => $payload['collect_date'] ?? null,
                'send_email' => $payload['send_email'] ?? null,
                'reference' => $payload['reference'] ?? null,
                'sms' => true,
            ], 
            $payload['sender'], 
            $payload['receiver']
        );

        $res = self::sendRequest([
            'api' => $payload['api'] ?? null,
            'method' => 'POST',
            'action' => 'EPSubmitOrderBulk',
            'body' => [
                'bulk' => [$data],
            ],
        ]);

        $order = $res['result'][0];
        $status = $order['status'] ?? null;

        if ($status !== 'Success') abort(422, $order['remarks']);
        else return $order;
    }

    /**
     * Make order payment
     *
     * @param array $payload
     * @return array
     */
    public static function makePayment($payload)
    {
        $res = self::sendRequest([
            'api' => $payload['api'] ?? null,
            'method' => 'POST',
            'action' => 'EPPayOrderBulk',
            'body' => [
                'bulk' => [
                    [
                        'order_no' => $payload['order_number'] ?? null,
                    ],
                ],
            ],
        ]);

        return $res['result'][0];
    }

    /**
     * Check order status
     *
     * @param array $payload
     * @return array
     */
    public static function checkStatus($payload)
    {
        $res = self::sendRequest([
            'api' => $payload['api'] ?? null,
            'method' => 'POST',
            'action' => 'EPParcelStatusBulk',
            'body' => [
                'bulk' => [
                    [
                        'order_no' => $payload['order_number'] ?? null,
                    ],
                ],
            ],
        ]);

        $result = $res['result'][0];
        $parcel = $result['parcel'][0] ?? null;

        return $parcel;
    }

    /**
     * Send API request to EasyParcel
     * 
     * @param array $payload
     */
    public static function sendRequest($payload)
    {
        $api = $payload['api'] ?? '';
        $body = $payload['body'] ?? [];
        $method = $payload['method'] ?? 'GET';
        $action = $payload['action'] ?? '';
        $url = env('EASYPARCEL_ENDPOINT') . '?ac=' . $action;
        $data = array_merge(['api' => $api], $body);

        if ($method === 'GET') $req = Http::get($url, $data);
        else if ($method === 'POST') $req = Http::post($url, $data);
        else if ($method === 'PUT') $req = Http::put($url, $data);
        else if ($method === 'DELETE') $req = Http::delete($url, $data);

        if ($req->status() === 200) {
            $status = strtolower($req->json()['api_status']);
            $error = strtolower($req->json()['error_remark'] ?? '');

            if ($status === 'success') {
                return $req->json();
            }
            else if (Str::startsWith($error, 'unverified')) {
                abort(422, 'Unverified EasyParcel account. Please verify your account at easyparcel.com');
            }
            else if (Str::startsWith($error, 'unauthorized')) {
                abort(422, 'Unable to authorize with EasyParcel. Please check your API key.');
            }
            else {
                abort(422, 'There were some errors while communicating with EasyParcel.');
            }
        }
        else {
            abort(422, 'Unable to get a valid response from EasyParcel');
        }
    }

    /**
     * Get state code
     *
     * @param string $state
     * @return string
     */
    public static function getStateCode($state)
    {
        if (!$state) return null;

        $codes = [
            'Johor' => 'jhr',
            'Kedah' => 'kdh',
            'Kelantan' => 'ktn',
            'Melaka' => 'mlk',
            'Negeri Sembilan' => 'nsn',
            'Pahang' => 'phg',
            'Perak' => 'prk',
            'Perlis' => 'pls',
            'Pulau Pinang' => 'png',
            'Selangor' => 'sgr',
            'Terengganu' => 'trg',
            'Kuala Lumpur' => 'kul',
            'Putrajaya' => 'pjy',
            'Sarawak' => 'srw',
            'Sabah' => 'sbh',
            'Labuan' => 'lbn',
        ];

        return $codes[$state];
    }

    /**
     * Get receiver payload
     *
     * @param array $data
     * @return array
     */
    public static function getReceiverPayload($data)
    {
        $payload = [
            'send_name' => $data->name ?? null,
            'send_contact' => $data->phone ?? null,
            'send_mobile' => $data->phone ?? null,
            'send_addr1' => $data->shipping_address->addr ?? null,
            'send_city' => $data->shipping_address->city ?? null,
            'send_state' => self::getStateCode($data->shipping_address->state ?? null),
            'send_code' => $data->shipping_address->postcode ?? null,
            'send_country' => 'MY',
        ];

        Validator::make(['easyparcel_receiver' => $payload], [
            'easyparcel_receiver.send_name' => 'required',
            'easyparcel_receiver.send_contact' => 'required',
            'easyparcel_receiver.send_addr1' => 'required',
            'easyparcel_receiver.send_city' => 'required',
            'easyparcel_receiver.send_state' => 'required',
            'easyparcel_receiver.send_code' => 'required',
            'easyparcel_receiver.send_country' => 'required',
        ])->validate();

        return $payload;
    }

    /**
     * Get sender payload from merchant
     *
     * @param object $data
     * @return array
     */
    public static function getSenderPayload($data)
    {
        $payload = [
            'pick_name' => $data->name ?? null,
            'pick_contact' => $data->sender_phone ?? null,
            'pick_mobile' => $data->sender_phone ?? null,
            'pick_addr1' => $data->sender_address->addr ?? null,
            'pick_city' => $data->sender_address->city ?? null,
            'pick_state' => self::getStateCode($data->sender_address->state ?? null),
            'pick_code' => $data->sender_address->postcode ?? null,
            'pick_country' => 'MY',
        ];

        Validator::make(['easyparcel_sender' => $payload], [
            'easyparcel_sender.pick_name' => 'required',
            'easyparcel_sender.pick_contact' => 'required',
            'easyparcel_sender.pick_addr1' => 'required',
            'easyparcel_sender.pick_city' => 'required',
            'easyparcel_sender.pick_state' => 'required',
            'easyparcel_sender.pick_code' => 'required',
            'easyparcel_sender.pick_country' => 'required',
        ])->validate();

        return $payload;
    }
}
