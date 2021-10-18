<?php

use Carbon\Carbon;

/**
 * Get app version
 */
function app_version()
{
    $path = base_path('.git/refs/tags');
    $version = '1.0.0';

    if (is_dir($path)) {
        $files = collect(scandir($path))->filter(function($file) {
            return !in_array($file, ['.', '..']);
        });

        if ($files->last()) $version = $files->last();
    }

    return $version;
}

/**
 * Get current locale url
 */
function locale_url($url)
{
    $locale = app()->currentLocale();

    if ($locale === 'en') return $url;
    else return '/' . $locale . str_replace($locale, '', $url);
}

/**
 * Format number to currency
 *
 * @return string
 */
function currency($num, $symbol = null, $bracket = true)
{
    $num = (float)$num ?: 0;
    $currency = number_format($num, 2);

    if ($symbol) $currency = "$symbol $currency";
    if ($bracket && $num < 0) $currency = '(' . str_replace('-', '', $currency) . ')';

    return $currency;
}

/**
 * Parse date range from string
 *
 * @param string $str
 * @return object
 */
function date_range($from, $to, $tz = 'UTC')
{
    if (!$from instanceof Carbon) $from = Carbon::createFromFormat($from, 'Y-m-d H:i:s');
    if (!$to instanceof Carbon) $to = Carbon::createFromFormat($to, 'Y-m-d H:i:s');

    return (object)[
        'from' => (object)[
            'date' => $from->copy()->setTimeZone($tz)->toDateString(),
            'datetime' => $from->copy()->setTimeZone($tz)->toDatetimeString(),
            'carbon' => $from->copy()->setTimezone($tz),
        ],
        'to' => (object)[
            'date' => $to->copy()->setTimeZone($tz)->toDateString(),
            'datetime' => $to->copy()->setTimeZone($tz)->toDatetimeString(),
            'carbon' => $to->copy()->setTimezone($tz),
        ],
        'diffInDays' => $from->copy()->diffInDays($to),
        'diffInMonths' => $from->copy()->diffInMonths($to->copy()->endOfMonth()),
        'diffInYears' => $from->copy()->diffInYears($to->copy()->endOfYear()),
    ];
}
