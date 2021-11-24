<?php

namespace Jiannius\Atom\Services;

use Pusher\PushNotifications\PushNotifications as Beams;

class PushNotifications
{
	/**
	 * Get pusher beams client
	 *
	 * @return PushNotifications
	 */
	public static function getBeamsClient()
	{
		return new Beams([
			'instanceId' => env('PUSHER_BEAMS_INSTANCE_ID'),
			'secretKey' => env('PUSHER_BEAMS_PRIMARY_KEY'),
		]);
	}

	/**
	 * Get publish body for beams from raw data
	 *
	 * @param array $data
	 * @return array
	 */
	public static function getBeamsPublishBody($data)
	{
		$gateway = $data['gateway'] ?? 'web';
		$type = $data['type'] ?? 'notification';

		$payload = [
			'title' => $data['title'] ?? '',
			'body' => $data['body'] ?? '',
			'icon' => $data['icon'] ?? asset('storage/img/logo.svg'),
		];

		if (isset($data['link'])) $payload['deep_link'] = $data['link'];

		$publishBody[$gateway] = [
			$type => $payload,
			'data' => (object)($data['data'] ?? []),
		];

		return $publishBody;
	}

	/**
	 * Publish push notification
	 *
	 * @param string $interest
	 * @param array $data
	 * @return array
	 */
	public static function publish($interest, $data)
	{
		$beamsClient = self::getBeamsClient();
		$publishBody = self::getBeamsPublishBody($data);

		return $beamsClient->publishToInterests((array)$interest, $publishBody);
	}
}