<?php

namespace Jiannius\Atom\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class Lalamove
{
    /**
     * Test API key and secret
     * 
     * @return boolean
     */
    public static function test($key, $secret, $location)
    {
        if (!$key) abort(422, 'API key is missing');
        if (!$secret) abort(422, 'API secret is missing');
        if (!$location) abort(422, 'Sender location is missing');

        $req = self::sendRequest([
            'key' => $key,
            'secret' => $secret,
            'method' => 'PUT',
            'path' => '/v2/orders/' . Str::random(10) . '/tips',
            'country' => self::getCountryCode($location),
            'body' => ['tips' => '1'],
        ], true);

        return $req->status() === 404;
    }

    /**
     * Get rate
     * 
     * @param array $payload
     * @return array
     */
    public static function getRate($payload)
    {
        $req = self::sendRequest([
            'key' => $payload['key'] ?? null,
            'secret' => $payload['secret'] ?? null,
            'method' => 'POST',
            'path' => '/v2/quotations',
            'country' => self::getCountryCode($payload['sender']['state'] ?? null),
            'body' => self::getQuotationPayload($payload),
        ]);

        return $req;
    }

    /**
     * Create order
     * 
     * @param array $payload
     * @return array
    */
    public static function createOrder($payload)
    {
        $req = self::sendRequest([
            'key' => $payload['key'] ?? null,
            'secret' => $payload['secret'] ?? null,
            'method' => 'POST',
            'path' => '/v2/orders',
            'country' => self::getCountryCode($payload['sender']['state'] ?? null),
            'body' => array_merge(
                [
                    'quotedTotalFee' => [
                        'amount' => $payload['amount'] ?? null,
                        'currency' => $payload['currency'] ?? null,
                    ],
                ],
                self::getQuotationPayload($payload),
            ),
        ]);

        return $req;
    }

    /**
     * Check order status
     * 
     * @param array $payload
     * @return array
     */
    public static function checkStatus($payload)
    {
        $req = self::sendRequest([
            'key' => $payload['key'] ?? null,
            'secret' => $payload['secret'] ?? null,
            'method' => 'GET',
            'path' => '/v2/orders/' . ($payload['order_number'] ?? ''),
            'country' => self::getCountryCode($payload['state'] ?? null),
        ]);

        return $req;
    }

    /**
     * Send request to Lalamove
     * 
     * @param array $payload
     * @param boolean returnRaw
     * @return Http
     */
    public static function sendRequest($payload, $returnRaw = false)
    {
		$time = time() * 1000;
        $key = $payload['key'] ?? '';
        $secret = $payload['secret'] ?? '';
        $method = $payload['method'] ?? '';
        $path = $payload['path'] ?? '';
        $body = json_encode($payload['body'] ?? '');
        $country = $payload['country'] ?? '';
        $url = env('LALAMOVE_ENDPOINT') . $path;

        // get token
		$raw = "{$time}\r\n{$method}\r\n{$path}\r\n\r\n{$body}";
		$signature = hash_hmac("sha256", $raw, $secret);
        $token = "$key:$time:$signature";

        // setup http
        $http = Http::withHeaders([
            'Authorization' => 'hmac ' . $token,
            'X-LLM-Country' => $country,
            'X-Request-ID' => Str::random(50),
        ])->withBody($body, 'application/json');

        if ($method === 'GET') $request = $http->get($url);
        else if ($method === 'POST') $request = $http->post($url);
        else if ($method === 'PUT') $request = $http->put($url);
        else if ($method === 'DELETE') $request = $http->delete($url);

        if ($request->status() === 401) {
            abort(422, 'Unable to authorize with Lalamove. Please check your API key and API secret.');
        }
        else if ($returnRaw) {
            return $request;
        }
        else {
            if ($request->status() === 200) {
                return $request->json();
            }
            else if ($request->status() === 404) {
                abort(422, 'Record not found');
            }
            else if ($message = self::getErrorMessage($request->json()['message'] ?? '')) {
                abort(422, $message);
            }
            else {
                abort(500, 'There were some error while performing the operation');
            }
        }
    }

    /**
     * Get service types
     * 
     * @return array
     */
    public static function getServiceTypes($name = null)
    {
        $types = [
            ['value' => 'MOTORCYCLE', 'label' => 'Motorcycle'],
            ['value' => 'CAR', 'label' => 'Car'],
            ['value' => 'VAN', 'label' => 'Van'],
            ['value' => '4X4', 'label' => '4X4'],
            ['value' => 'TRUCK330', 'label' => '1-Ton Lorry'],
            ['value' => 'TRUCK550', 'label' => '3-Ton Lorry'],
        ];

        return $name ? collect($types)->where('value', $name)->first() : $types;
    }

    /**
     * Get special requests
     * 
     * @return array
     */
    public static function getSpecialRequests($group = null)
    {
        $requests = [
            ['value' => 'PURCHASE_SERVICE',' label' => 'Buy4U', 'groups' => ['MOTORCYCLE']],
            ['value' => 'LALABAG',' label' => 'Lalabag', 'groups' => ['MOTORCYCLE']],
            ['value' => 'DOOR2DOOR',' label' => 'Door to Door delivery', 'groups' => ['CAR']],
            ['value' => 'DOOR2DOOR_TRUCK330',' label' => 'Moving Services by Driver', 'groups' => ['TRUCK330']],
            ['value' => 'DOOR2DOOR_TRUCK550',' label' => 'Moving Services by Driver', 'groups' => ['TRUCK550']],
            ['value' => 'DOOR2DOOR_1HELPER_TRUCK330',' label' => 'Moving Services by Driver + 1 Helper', 'groups' => ['TRUCK330']],
            ['value' => 'DOOR2DOOR_1HELPER_TRUCK550',' label' => 'Moving Services by Driver + 1 Helper', 'groups' => ['TRUCK550']],
            ['value' => 'DOOR2DOOR_2HELPER_TRUCK330',' label' => 'Moving Services by Driver + 2 Helpers', 'groups' => ['TRUCK330']],
            ['value' => 'DOOR2DOOR_2HELPER_TRUCK550',' label' => 'Moving Services by Driver + 2 Helpers', 'groups' => ['TRUCK550']],
            ['value' => 'RETURNTRIP',' label' => 'Return Trip', 'groups' => ['MOTORCYCLE', 'CAR']],
            ['value' => 'LOADING_SERVICE',' label' => 'Loading Service', 'groups' => ['TRUCK330', 'TRUCK550']],
        ];

        return $group 
            ? collect($requests)->filter(fn($request) => in_array($group, $request['groups']))->values()
            : $requests;
    }

    /**
     * Get country code
     * 
     * @param string $country
     * @return string
     */
    public static function getCountryCode($country)
    {
        $codes = [
            'Selangor' => 'MY_KUL',
            'Kuala Lumpur' => 'MY_KUL',
            'Johor Bahru' => 'MY_JHB',
            'Pulau Pinang' => 'MY_NTL',
        ];

        return $codes[$country] ?? '';
    }

    /**
     * Get error message
     * 
     * @param string $code
     * @return string
     */
    public static function getErrorMessage($code)
    {
        $codes = [
            'ERR_INVALID_MARKET' => 'Your location is currently not supported by Lalamove',
            'ERR_INVALID_COUNTRY' => 'Your location is currently not supported by Lalamove',
            'ERR_INVALID_PHONE_NUMBER' => 'Invalid phone number',
            'ERR_OUT_OF_SERVICE_AREA' => 'Your location is out of Lalamove service area',
            'ERR_INSUFFICIENT_CREDIT' => 'You do not have sufficient credit',
        ];

        return $codes[$code] ?? null;
    }

    /**
     * Get quotation body payload
     * 
     * @param array $payload
     * @return array
     */
    public static function getQuotationPayload($payload)
    {
        $data = [
            'serviceType' => $payload['service_type'] ?? null,
            'stops' => [
                [
                    'location' => [
                        'lat' => (string)$payload['sender']['location']['lat'] ?? null,
                        'lng' => (string)$payload['sender']['location']['lng'] ?? null
                    ],
                    'addresses' => [
                        'en_MY' => [
                            'displayString' => $payload['sender']['address'] ?? null,
                            'country' => self::getCountryCode($payload['sender']['state'] ?? null),
                        ],
                    ],
                ],
                [
                    'location' => [
                        'lat' => (string)$payload['receiver']['location']['lat'] ?? null,
                        'lng' => (string)$payload['receiver']['location']['lng'] ?? null
                    ],
                    'addresses' => [
                        'en_MY' => [
                            'displayString' => $payload['receiver']['address'] ?? null,
                            'country' => self::getCountryCode($payload['receiver']['state'] ?? null),
                        ],
                    ],
                ],
            ],
            'deliveries' => [
                [
                    'toStop' => 1,
                    'toContact' => [
                        'name' => $payload['receiver']['name'] ?? null,
                        'phone' => $payload['receiver']['phone'] ?? null,
                    ],
                    'remarks' => "ORDERING.MY\r\n" . ($payload['remarks'] ?? ''),
                ],
            ],
            'requesterContact' => [
                'name' => $payload['sender']['name'] ?? null,
                'phone' => $payload['sender']['phone'] ?? null,
            ],
        ];

        if ($specialRequests = $payload['special_requests'] ?? null) $data['specialRequests'] = $specialRequests;
        if ($scheduleAt = $payload['schedule_at'] ?? null) $data['scheduleAt'] = $scheduleAt;

        return $data;
    }
}
