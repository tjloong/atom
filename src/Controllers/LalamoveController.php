<?php

namespace Jiannius\Atom\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class LalamoveController extends Controller
{
    /**
     * Get services
     */
    public function getServices()
    {
        $key = request()->input('api');
        $secret = request()->input('secret');
        if (!$key || !$secret) abort(422, 'Missing API key/secret');

        $locode = [
            'Johor Bahru' => 'MY JHB',
            'Kuala Lumpur' => 'MY_KUL',
            'Melaka' => 'MY MKZ',
            'Selangor' => 'MY KUL',
            'Pulau Pinang' => 'MY NTL',
        ][request()->input('state')] ?? null;

        if ($locode) {
            $req = $this->sendRequest([
                'key' => $key,
                'secret' => $secret,
                'method' => 'GET',
                'path' => '/v3/cities',
                'market' => 'MY',
            ], true);

            $cities = data_get($req->json(), 'data');
            $services = collect($cities)->firstWhere('locode', $locode);

            return data_get($services, 'services');
        }

        return [];
    }

    /**
     * Get rate
     */
    public function getQuotation()
    {
        $body = [
            'serviceType' => request()->input('service_type'),
            'language' => 'en_MY',
            'stops' => [
                // sender stop
                [
                    'coordinates' => [
                        'lat' => (string)request()->input('sender.location.lat'), 
                        'lng' => (string)request()->input('sender.location.lng'), 
                    ],
                    'address' => request()->input('sender.address'),
                ],
                // receiver stop
                [
                    'coordinates' => [
                        'lat' => (string)request()->input('receiver.location.lat'),
                        'lng' => (string)request()->input('receiver.location.lng'),
                    ],
                    'address' => request()->input('receiver.address'),
                ],
            ],
        ];

        if ($scheduleAt = request()->input('schedule_at')) $body['scheduleAt'] = $scheduleAt;
        if ($specialRequests = request()->input('special_requests')) $body['specialRequests'] = $specialRequests;

        $req = $this->sendRequest([
            'key' => request()->input('key'),
            'secret' => request()->input('secret'),
            'method' => 'POST',
            'path' => '/v3/quotations',
            'market' => 'MY',
            'body' => $body,
        ]);

        return $req;
    }

    /**
     * Create order
     */
    public function createOrder()
    {
        $body = [
            'quotationId' => request()->input('rate.quotationId'),
            'sender' => [
                'stopId' => request()->input('rate.stops.0.stopId'),
                'name' => request()->input('sender.name'),
                'phone' => $this->formatPhone(request()->input('sender.phone')),
            ],
            'recipients' =>[
                [
                    'stopId' => request()->input('rate.stops.1.stopId'),
                    'name' => request()->input('receiver.name'),
                    'phone' => $this->formatPhone(request()->input('receiver.phone')),
                    'remarks' => request()->input('receiver.remarks') ?? '',
                ],
            ],
        ];

        $req = $this->sendRequest([
            'key' => request()->input('key'),
            'secret' => request()->input('secret'),
            'method' => 'POST',
            'path' => '/v3/orders',
            'market' => 'MY',
            'body' => $body,
        ]);

        return $req;
    }

    /**
     * Get order details
     */
    public function getOrderDetails()
    {
        $req = $this->sendRequest([
            'key' => request()->input('key'),
            'secret' => request()->input('secret'),
            'method' => 'GET',
            'path' => '/v3/orders/'.request()->input('orderId'),
            'market' => 'MY',
        ]);

        return $req;
    }

    /**
     * Send request to lalamove
     */
    public function sendRequest($payload, $returnRaw = false)
    {
        $time = time() * 1000;
        $key = $payload['key'] ?? '';
        $secret = $payload['secret'] ?? '';
        $method = $payload['method'] ?? '';
        $path = $payload['path'] ?? '';
        $body = json_encode(['data' => $payload['body'] ?? []]);
        $market = $payload['market'] ?? '';
        $url = env('LALAMOVE_ENDPOINT') . $path;

        // get token
		$raw = "{$time}\r\n{$method}\r\n{$path}\r\n\r\n{$body}";
		$signature = hash_hmac("sha256", $raw, $secret);
        $token = "$key:$time:$signature";

        // setup http
        $http = Http::withHeaders([
            'Authorization' => 'hmac ' . $token,
            'Market' => $market,
            'Request-ID' => Str::random(50),
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
        else if ($request->status() >= 200 && $request->status() < 300) {
            return $request->json();
        }
        else if ($request->status() === 404) {
            abort(404, 'Record not found');
        }
        else {
            $error = collect(data_get($request->json(), 'errors'))
                ->flatten()
                ->map(fn($error) => $this->getErrorMessage($error));

            abort($request->status(), $error);
        }
    }

    /**
     * Get error message
     */
    public function getErrorMessage($code)
    {
        $codes = [
            'ERR_INVALID_MARKET' => 'Your location is currently not supported by Lalamove',
            'ERR_INVALID_COUNTRY' => 'Your location is currently not supported by Lalamove',
            'ERR_INVALID_PHONE_NUMBER' => 'Invalid phone number',
            'ERR_OUT_OF_SERVICE_AREA' => 'Your location is out of Lalamove service area',
            'ERR_INSUFFICIENT_CREDIT' => 'You do not have sufficient credit',
            'ERR_INSUFFICIENT_STOPS' => 'Not enough stops, number of stops should be between 2 and 16',
        ];

        return $codes[$code] ?? $code;
    }

    /**
     * Format phone
     */
    public function formatPhone($phone)
    {
        if (Str::startsWith($phone, '0')) return '+6'.$phone;
        if (Str::startsWith($phone, '60')) return '+'.$phone;

        return $phone;
    }
}
